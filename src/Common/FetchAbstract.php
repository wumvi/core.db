<?php
declare(strict_types=1);

namespace Core\Db\Common;

use Core\Db\Exception\DbException;

/**
 * Абстрактный интерфейс для фетча
 */
abstract class FetchAbstract implements FetchInterface
{
    /** @var array[] Меппинг данных */
    protected $mappingList = [];

    /**
     * Преобразовывываем переменную к заданному типу
     *
     * @param mixed $value Переменная
     * @param int   $type Тип переменной см Fields::TYPE_*
     *
     * @return mixed
     */
    protected function convertType($value, int $type)
    {
        switch ($type) {
            case Fields::TYPE_INT:
                return (int)$value;
            case Fields::TYPE_FLOAT:
                return (float)$value;
            case Fields::TYPE_BOOL:
                return is_numeric($value) ? (bool)$value : $value === 't';
            case Fields::TYPE_DATE:
                return strtotime($value);
            case Fields::TYPE_UNIXTIME:
                $date = new \DateTime();
                $date->setTimestamp((int)$value);

                return $date;
            case Fields::TYPE_STRING:
                return $value ? trim($value) : '';
            case Fields::TYPE_ARRAY:
                return $value;
            default:
                return $value;
        }
    }

    /**
     * Обрабатываем меппинг данных
     *
     * @param array $data Данные
     *
     * @return array Результирующий массив
     *
     * @throws DbException
     */
    protected function makeMapping(array $data): array
    {
        if (!$data) {
            return [];
        }

        $result = [];
        foreach ($data as $dataItem) {
            $dataTmp = [];
            $keyId = null;
            foreach ($this->mappingList as $mapKey => $mapItem) {
                $name = $mapItem[Fields::ARRAY_FIELD_NAME];
                $type = $mapItem[Fields::ARRAY_TYPE_NAME] ?? Fields::TYPE_STRING;

                if (!key_exists($mapKey, $dataItem)) {
                    $msg = vsprintf('Item "%s" not found in %s', [$mapKey, var_export($dataItem, true)]);
                    throw new DbException($msg, DbException::WRONG_MAPPING);
                }

                $dataTmp[$name] = $this->convertType($dataItem[$mapKey], $type);

                if (key_exists(Fields::ARRAY_KEY_PK, $mapItem)) {
                    $keyId = $dataTmp[$name];
                }
            }

            if ($keyId === null) {
                $result[] = $dataTmp;
            } else {
                $result[$keyId] = $dataTmp;
            }
        }

        return $result;
    }

    /**
     * Устанавливаем mapping
     *
     * @param array $mapping Меппинг для модели
     *
     * @return FetchAbstract
     */
    public function mappingModel(array $mapping): FetchAbstract
    {
        $this->mappingList = $mapping;

        return $this;
    }
}
