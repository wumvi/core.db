<?php
declare(strict_types=1);

namespace Wumvi\Classes\Db\Driver;

use Wumvi\Classes\Db\Common\DriverInterface;
use Wumvi\Classes\Db\Common\FetchAbstract;
use Wumvi\Classes\Db\Exception\DbConnectionException;
use Wumvi\Classes\Db\Fetch\PostgresSqlFetch;
use Wumvi\Classes\Db\Model\PostgreSqlDbParam;

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
            'dbname' => $dbInfo->getDb(),
            'user' => $dbInfo->getUser(),
            'password' => $dbInfo->getPwd(),
        ];
        $url = http_build_query($params, '', ' ') . ' options=\'--client_encoding=UTF8\' connect_timeout=5';
        $this->handle = @pg_connect($url);
        if (!$this->handle) {
            $msg = sprintf('Unable to connect to PostgreSQL server %s:%s', $dbInfo->getHost(), $dbInfo->getPort());
            throw new DbConnectionException($msg);
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
        return new PostgresSqlFetch(
            $this->makeClearSql($funcName, $bindPair),
            $this->handle
        );
    }

    /**
     * Составляет SQL вызова функции и bind-ит переменные
     * @param string $funcName название фукнции
     * @param array $bindPair Массив переменных
     * @return string Sql
     */
    protected function makeClearSql(string $funcName, array $bindPair = []): string
    {
        $sql = $funcName;
        foreach ($bindPair as $name => $value) {
            if (is_array($value)) {
                $data = array_map(function ($item) {
                    return $this->convertType($item);
                }, $value);

                $partSql = 'array[' . implode(',', $data) . ']';
            } else {
                $partSql = $this->convertType($value);
            }

            $sql = str_replace(':' . $name, $partSql, $sql);
        }

        return $sql;
    }

    protected function convertType($value)
    {
        if (is_array($value)) {
            $data = array_map(function ($item) {
                return $this->convertType($item);
            }, $value);

            return '[' . implode(',', $data) . ']';
        } elseif (is_string($value)) {
            return '\'' . pg_escape_string($value) . '\'';
        } elseif (is_numeric($value)) {
            if ($value < -2147483648 || 2147483647 < $value) {
                return $value . '::bigint';
            }

            return $value;
        } elseif ($value === null) {
            return 'null';
        }elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return $value;
    }
}
