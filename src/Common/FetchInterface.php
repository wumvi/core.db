<?php
declare(strict_types=1);

namespace Wumvi\Classes\Db\Common;

/**
 * Интерфейс для фетча
 */
interface FetchInterface
{
    /**
     * Получаем записи
     *
     * @param bool $isCursor
     *
     * @return array Записи
     */
    public function fetchAll($isCursor = false): array;

    /**
     * Получаем одну запись
     *
     * @param bool $isCursor
     *
     * @return array Запись
     */
    public function fetchFirst($isCursor = false): array;

    /**
     * Просто вызов
     */
    public function call(): void;

    /**
     * Получаем единичный результат
     * @param int $type см. $type см Read::TYPE_*
     * @return mixed Значение
     */
    public function getValue(int $type);

    /**
     * Получаем SQL запрос
     * @return string
     */
    public function getSql(): string;
}
