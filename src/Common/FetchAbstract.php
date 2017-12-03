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
    protected function convert($value, int $type)
    {
        switch ($type) {
            case FieldsInterface::TYPE_INT:
                $result = (int)$value;
                break;
            case FieldsInterface::TYPE_FLOAT:
                $result = (float)$value;
                break;
            case FieldsInterface::TYPE_BOOL:
                $result = is_numeric($value) ? (bool)$value : $value === 't';
                break;
            case FieldsInterface::TYPE_DATE:
                $result = strtotime($value);
                break;
            case FieldsInterface::TYPE_UNIXTIME:
                $result = new \DateTime();
                $result->setTimestamp((int)$value);
                break;
            case FieldsInterface::TYPE_STRING:
                $result = $value ?? '';
                break;
            case FieldsInterface::TYPE_ARRAY:
            default:
                $result = $value;
        }

        return $result;
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
    protected function rename(array $data): array
    {
        if (!$data) {
            return [];
        }

        $result = [];
        foreach ($data as $dataItem) {
            list($keyId, $dataTmp) = $this->getKeyValue($dataItem);

            if ($keyId === null) {
                $result[] = $dataTmp;
            } else {
                $result[$keyId] = $dataTmp;
            }
        }

        return $result;
    }

    /**
     * @param array $dataItem
     *
     * @return array
     *
     * @throws DbException
     */
    private function getKeyValue(array $dataItem): array
    {
        $result = [];
        $keyId = null;

        foreach ($this->mappingList as $mapKey => $mapItem) {
            $name = $mapItem[FieldsInterface::ARRAY_NAME];
            $type = $mapItem[FieldsInterface::ARRAY_TYPE] ?? FieldsInterface::TYPE_STRING;

            if (!key_exists($mapKey, $dataItem)) {
                $msg = vsprintf('Item "%s" not found in %s', [$mapKey, var_export($dataItem, true),]);
                throw new DbException($msg, DbException::WRONG_MAPPING);
            }

            $result[$name] = $this->convert($dataItem[$mapKey], $type);

            if (key_exists(FieldsInterface::ARRAY_KEY_PK, $mapItem)) {
                $keyId = $result[$name];
            }
        }

        return [$keyId, $result];
    }

    /**
     * Устанавливаем mapping
     *
     * @param array $mapping Меппинг для модели
     *
     * @return FetchAbstract
     */
    public function mapping(array $mapping): FetchAbstract
    {
        $this->mappingList = $mapping;

        return $this;
    }
}
