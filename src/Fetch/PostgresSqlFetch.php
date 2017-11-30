<?php
declare(strict_types=1);

namespace Core\Db\Fetch;

use Core\Db\Common\FetchAbstract;
use Core\Db\Exception\DbBadSqlException;
use Core\Db\Exception\DbRaiseException;

/**
 * Class PostgresSqlFetch
 * @package Core\Db\Fetch
 */
class PostgresSqlFetch extends FetchAbstract
{
    /** @var string Sql запрос */
    private $sql;

    /** @var resource Handle на коннект */
    private $handle;

    /**
     * PostgresSqlFetch constructor.
     * @param string $sql
     * @param resource $handle
     */
    public function __construct(string $sql, $handle)
    {
        $this->sql = $sql;
        $this->handle = $handle;
    }

    private function makeSql(int $type, string $sql, int $count = self::UNLIMIT): string
    {
        $limit = $count !== self::UNLIMIT ? 'limit ' . $count : '';
        switch ($type) {
            case self::TYPE_FUNCTION_QUERY:
                return vsprintf('select * from %s', [$sql,]);
            case self::TYPE_FUNCTION_CURSOR:
                return vsprintf('select %s; fetch all from _result %s;', [$sql, $limit,]);
            default:
                return $sql;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAll(int $type = self::TYPE_FUNCTION_QUERY, int $limit = 0): array
    {
        $sql = $this->makeSql($type, $this->sql, $limit);
        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            $msg = vsprintf('Error to exec sql "%s". %s', [$sql, $errorText,]);
            throw new DbRaiseException($msg, DbBadSqlException::ERROR_TO_EXEC_SQL);
        }

        if ($result === FALSE) {
            $msg = vsprintf('Error to exec sql "%s"', [$sql,]);
            throw new DbBadSqlException($msg, DbBadSqlException::ERROR_TO_EXEC_SQL);
        }

        $data = pg_fetch_all($result);
        if (!$data) {
            return [];
        }

        return $this->mappingList ? $this->makeMapping($data) : $data;
    }

    /**
     * @inheritdoc
     */
    public function fetchFirst(int $type = self::TYPE_FUNCTION_QUERY): array
    {
        $data = $this->fetchAll($type, 1);

        return $data[0] ?? [];
    }

    /**
     * @inheritdoc
     * @throws DbBadSqlException
     * @throws DbRaiseException
     */
    public function call(): void
    {
        $sql = $this->makeSql(self::TYPE_FUNCTION_QUERY, $this->sql, self::UNLIMIT);
        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            throw new DbRaiseException($errorText . ' : ' . $this->sql);
        }

        if ($result === FALSE) {
            $msg = vsprintf('Error to exec sql "%s"', [$this->sql,]);
            throw new DbBadSqlException($msg, DbBadSqlException::ERROR_TO_EXEC_SQL);
        }

    }

    /**
     * @inheritdoc
     */
    public function getValue(int $type)
    {
        $sql = vsprintf('select %s as result limit 1', [$this->sql,]);
        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            $msg = vsprintf('Error to exec sql "%s". %s', [$sql, $errorText,]);
            throw new DbRaiseException($msg, DbBadSqlException::ERROR_TO_EXEC_SQL);
        }

        if ($result === FALSE) {
            $msg = vsprintf('Error to exec sql "%s"', [$sql,]);
            throw new DbBadSqlException($msg, DbBadSqlException::ERROR_TO_EXEC_SQL);
        }

        $data = pg_fetch_object($result);

        return $this->convertType($data->result, $type);
    }

    /**
     * @inheritdoc
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}
