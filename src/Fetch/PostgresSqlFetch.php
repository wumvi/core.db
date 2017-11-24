<?php
declare(strict_types=1);

namespace Wumvi\Classes\Db\Fetch;

use Wumvi\Classes\Db\Common\FetchAbstract;
use Wumvi\Classes\Db\Exception\DbBadSqlException;
use Wumvi\Classes\Db\Exception\DbRaiseException;

/**
 * Class PostgresSqlFetch
 * @package Wumvi\Classes\Db\Fetch
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

    /**
     * @inheritdoc
     */
    public function fetchAll($cursor = false): array
    {
        if ($cursor) {
            $sql = 'select ' . $this->sql . '; fetch all from _result;';
        } else {
            $sql = 'select * from ' . $this->sql;
        }

        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            throw new DbRaiseException($errorText . ' : ' . $sql);
        }

        if (!$result) {
            throw new DbBadSqlException();
        }

        $data = pg_fetch_all($result);
        if (!$data) {
            return [];
        }

        if ($this->mappingList) {
            $data = $this->makeMapping($data);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function fetchFirst($cursor = false): array
    {
        if ($cursor) {
            $sql = 'select ' . $this->sql . '; fetch all from _result;';
        } else {
            $sql = 'select * from ' . $this->sql . ' limit 1;';
        }
        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            throw new DbRaiseException($errorText . ' : ' . $sql);
        }

        if (!$result) {
            throw new DbBadSqlException();
        }

        $data = pg_fetch_array($result, null, PGSQL_ASSOC);
        if (!$data) {
            return [];
        }

        if ($this->mappingList) {
            return $this->makeMapping([$data])[0];
        }

        return $data;
    }

    /**
     * @inheritdoc
     * @throws DbBadSqlException
     * @throws DbRaiseException
     */
    public function call(): void
    {
        $result = @pg_query($this->handle, 'select ' . $this->sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            throw new DbRaiseException($errorText . ' : ' . $this->sql);
        }

        if (!$result) {
            throw new DbBadSqlException();
        }
    }


    /**
     * @inheritdoc
     */
    public function getValue(int $type)
    {
        $sql = 'select ' . $this->sql . ' as result';
        $result = @pg_query($this->handle, $sql);

        $errorText = pg_last_error($this->handle);
        if ($errorText) {
            throw new DbRaiseException($errorText . ' : ' . $sql);
        }

        if (!$result) {
            throw new DbBadSqlException();
        }

        $data = pg_fetch_object($result);
        if (!$data) {
            return null;
        }

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
