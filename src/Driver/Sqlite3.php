<?php
declare(strict_types=1);

namespace Core\Db\Driver;

use Core\Db\Common\DriverInterface;
use Core\Db\Common\FetchAbstract;
use Core\Db\Exception\DbException;
use Core\Db\Fetch\SqliteFetch;
use Core\Db\Model\Sqlite3DbParam;

/**
 * Драйвер для SQLite3
 */
class Sqlite3 implements DriverInterface
{
    /**
     * @var \SQLite3 Handle для SQLite
     */
    protected $handle;

    /**
     * Конструктор
     *
     * @param Sqlite3DbParam $dbInfo Данные для коннекта
     */
    public function __construct(Sqlite3DbParam $dbInfo)
    {
        $this->handle = new \SQLite3($dbInfo->getDb());
    }

    /**
     * Делаем любой запрос
     *
     * @param string $sql SQL запрос
     * @param array  $bind Переменные для подстановки
     *
     * @return FetchAbstract
     *
     * @throws DbException
     */
    public function exec(string $sql, array $bind = []): FetchAbstract
    {
        if ($bind) {
            $stmt = $this->handle->prepare($sql);
            foreach ($bind as $name => $val) {
                $type = $this->getArgType($val);
                $stmt->bindValue(':' . $name, $val, $type);
            }

            $result = $stmt->execute();
        } else {
            $result = $this->handle->query($sql);
        }

        return new SqliteFetch($sql, $result);
    }

    /**
     * Получаем тип переменной для SQLITE3
     *
     * @param mixed $arg Переменная
     *
     * @return int см. SQLITE3_*
     *
     * @throws DbException
     */
    public function getArgType($arg): int
    {
        switch (gettype($arg)) {
            case 'double':
                return SQLITE3_FLOAT;
            case 'integer':
                return SQLITE3_INTEGER;
            case 'boolean':
                return SQLITE3_INTEGER;
            case 'NULL':
                return SQLITE3_NULL;
            case 'string':
                return SQLITE3_TEXT;
            default:
                $msg  = vsprintf('Argument is of invalid type "%s"', [gettype($arg),]);
                throw new DbException($msg, DbException::WRONG_TYPE);
        }
    }
}
