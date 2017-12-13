<?php
declare(strict_types=1);

namespace Core\Db\Driver;

use Core\Db\Common\DriverInterface;
use Core\Db\Common\FetchAbstract;
use Core\Db\Exception\DbConnectionException;
use Core\Db\Exception\DbException;
use Core\Db\Fetch\PostgresSqlFetch;
use Core\Db\Model\PostgreSqlDbParam;

/**
 * Реализация драйвера для Postresql
 */
class PostgreSql implements DriverInterface
{
    /** @var resource Handle на коннект */
    protected $handle;

    /**
     * Construct.
     *
     * @param PostgreSqlDbParam $dbInfo Модель настроек для драйвера
     *
     * @throws DbConnectionException
     */
    public function __construct(PostgreSqlDbParam $dbInfo)
    {
        $params = [
            'host' => $dbInfo->getHost(),
            'port' => $dbInfo->getPort(),
            'dbname' => $dbInfo->getDbName(),
            'user' => $dbInfo->getUser(),
            'password' => $dbInfo->getPassword(),
        ];
        $url = http_build_query($params, '', ' ');
        $url .= ' options=\'--client_encoding=UTF8\' connect_timeout=5';
        $pinbaHandle = pinba_timer_start([
            'type' => 'db.connect',
            'db.host' => $dbInfo->getHost(),
            'srv.host' => gethostname(),]);
        $this->handle = pg_connect($url);
        pinba_timer_stop($pinbaHandle);
        if ($this->handle === false) {
            $msg = sprintf('Unable to connect to PostgreSQL server %s:%s', $dbInfo->getHost(), $dbInfo->getPort());
            throw new DbConnectionException($msg, DbConnectionException::CAN_NOT_CONNECT_TO_DB);
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->handle) {
            pg_close($this->handle);
        }
    }

    /**
     * @inheritdoc
     */
    public function exec(string $funcName, array $bindPair = []): FetchAbstract
    {
        return new PostgresSqlFetch($this->makeClearSql($funcName, $bindPair), $this->handle);
    }

    /**
     * Составляет SQL вызова функции и bind-ит переменные
     *
     * @param string $funcName название фукнции
     * @param array  $bindPair Массив переменных
     *
     * @return string Sql
     *
     * @throws DbException
     */
    protected function makeClearSql(string $funcName, array $bindPair = []): string
    {
        $sql = $funcName;
        foreach ($bindPair as $name => $value) {
            if (is_array($value)) {
                $data = array_map(function ($item) {
                    return $this->convert($item);
                }, $value);

                $partSql = 'array[' . implode(',', $data) . ']';
            } else {
                $partSql = $this->convert($value);
            }

            $sql = str_replace(':' . $name, $partSql, $sql);
        }

        return $sql;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws DbException
     */
    public function convert($value)
    {
        switch (gettype($value)) {
            case 'string':
                $result = '\'' . pg_escape_string($value) . '\'';
                break;
            case 'array':
                $data = array_map(function ($item) {
                    return $this->convert($item);
                }, $value);

                $result = '[' . implode(',', $data) . ']';
                break;
            case 'double':
            case 'integer':
                $result = $value < -2147483648 || 2147483647 < $value ? $value . '::bigint' : $value;
                break;
            case 'NULL':
                $result = 'null';
                break;
            case 'boolean':
                $result = $value ? 'true' : 'false';
                break;
            default:
                throw new DbException('Unsupported type ' . gettype($value), DbException::WRONG_TYPE);
        }

        return $result;
    }
}
