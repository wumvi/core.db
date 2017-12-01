<?php
declare(strict_types = 1);

namespace Core\Db\Fetch;

use Core\Db\Common\FetchAbstract;

/**
 * Featch для SQLite
 */
class SqliteFetch extends FetchAbstract
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
    public function fetchAll(int $type = self::TYPE_FUNCTION_QUERY) : array
    {
        $dataArray = [];
        while ($res = $this->result->fetchArray(SQLITE3_ASSOC)) {
            $dataArray[] = $res;
        }

        if ($this->mappingList) {
            return $this->rename($dataArray);
        }

        return $dataArray;
    }

    /**
     * @inheritdoc
     */
    public function fetchFirst(int $type = self::TYPE_FUNCTION_QUERY) : array
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
