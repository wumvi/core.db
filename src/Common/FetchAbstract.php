<?php
declare(strict_types=1);

namespace Wumvi\Classes\Db\Common;

use Wumvi\Classes\Read;

/**
 * Абстрактный интерфейс для фетча
 */
abstract class FetchAbstract implements FetchInterface
{
    /** @var array[] Меппинг данных */
    protected $mappingList = [];

    /**
     * Преобразовывываем переменную к заданному типу
     * @param mixed $value Переменная
     * @param int $type Тип переменной см Read::TYPE_*
     * @return mixed
     */
    protected function convertType($value, int $type)
    {
        switch ($type) {
            case Read::TYPE_INT:
                return (int)$value;
            case Read::TYPE_FLOAT:
                return (float)$value;
            case Read::TYPE_BOOL:
                return is_numeric($value) ? (bool)$value : $value === 't';
            case Read::TYPE_DATE:
                return strtotime($value);
            case Read::TYPE_UNIXTIME:
                $date = new \DateTime();
                $date->setTimestamp((int) $value);
                return $date;
                break;
            case Read::TYPE_STRING:
                return $value ? trim($value) : '';
            case Read::TYPE_ARRAY:
                return $value;
            default:
                return $value;
        }
    }

    /**
     * Обрабатываем меппинг данных
     * @param array $data Данные
     * @return array Результирующий массив
     * @throws \Exception
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
                $name = $mapItem[Read::ARRAY_FIELD_NAME];
                $type = $mapItem[Read::ARRAY_TYPE_NAME] ?? Read::TYPE_STRING;

                if (!key_exists($mapKey, $dataItem)) {
                    throw new \Exception('Item ' . $mapKey . ' not found in ' . var_export($dataItem, true));
                }

                $dataTmp[$name] = $this->convertType($dataItem[$mapKey], $type);

                if (key_exists(Read::ARRAY_KEY_PK, $mapItem)) {
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
     * @param [] $mapping Меппинг для модели
     * @return FetchAbstract
     */
    public function mappingModel(array $mapping): FetchAbstract
    {
        $this->mappingList = $mapping;
        return $this;
    }
}
