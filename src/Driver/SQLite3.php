<?php
declare(strict_types = 1);

namespace Wumvi\Classes\Db\Driver;

use Wumvi\Classes\Db\Common\DriverInterface;
use Wumvi\Classes\Db\Common\FetchAbstract;
use Wumvi\Classes\Db\Fetch\SQLiteFetch;
use Wumvi\Classes\Db\Model\SQLite3DbParam;

/**
 * Драйвер для SQLite3
 */
class SQLite3 implements DriverInterface
{
    /** @var \SQLite3 Handle для SQLite */
    protected $handle;

    /**
     * Конструктор
     * @param SQLite3DbParam $dbInfo Данные для коннекта
     */
    public function __construct(SQLite3DbParam $dbInfo)
    {
        $this->handle = new \SQLite3($dbInfo->getDb());
    }

    /**
     * Делаем любой запрос
     * @param string $sql SQL запрос
     * @param array $bind Переменные для подстановки
     * @return FetchAbstract
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

        return new SQLiteFetch($sql, $result);
    }

    /**
     * Получаем тип переменной для SQLITE3
     * @param mixed $arg Переменная
     * @return int см. SQLITE3_*
     */
    protected function getArgType($arg): int
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
                throw new \InvalidArgumentException('Argument is of invalid type ' . gettype($arg));
        }
    }
}
