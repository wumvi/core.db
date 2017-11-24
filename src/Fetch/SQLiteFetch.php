<?php
declare(strict_types = 1);

namespace Wumvi\Classes\Db\Fetch;

use Wumvi\Classes\Db\Common\FetchAbstract;

/**
 * Featch для SQLite
 */
class SQLiteFetch extends FetchAbstract
{
    /** @var \SQLite3Result Handle на результаты */
    protected $result;

    /** @var string */
    protected $sql;

    /**
     * SQLiteFetch constructor.
     * @param string $sql
     * @param \SQLite3Result $result
     */
    public function __construct(string $sql, \SQLite3Result $result)
    {
        $this->result = $result;
        $this->sql = $sql;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll() : array
    {
        $dataArray = [];
        while ($res = $this->result->fetchArray(SQLITE3_ASSOC)) {
            $dataArray[] = $res;
        }

        if ($this->mappingList) {
            return $this->makeMapping($dataArray);
        }

        return $dataArray;
    }

    /**
     * @inheritdoc
     */
    public function fetchFirst() : array
    {
        $array = $this->result->fetchArray(SQLITE3_ASSOC);
        return $array ?: [];
    }

    /**
     * @inheritdoc
     */
    public function call(): void
    {
        throw new \Exception('Method not implements');
    }

    /**
     * @inheritdoc
     */
    public function getValue(int $type)
    {
        throw new \Exception('Method not implements');
    }

    /**
     * @inheritdoc
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}
