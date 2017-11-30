<?php
declare(strict_types=1);

namespace Core\Db\Common;

/**
 * Интерфейс для фетча
 *
 * @codeCoverageIgnore
 */
interface FetchInterface
{
    public const TYPE_FUNCTION_CURSOR = 1;
    public const TYPE_FUNCTION_QUERY = 2;
    public const TYPE_SIMPLE_QUERY = 3;

    public const UNLIMIT = -1;

    /**
     * Получаем записи
     *
     * @param int $type
     *
     * @return array Записи
     */
    public function fetchAll(int $type = self::TYPE_FUNCTION_QUERY): array;

    /**
     * Получаем одну запись
     *
     * @param int $type
     *
     * @return array Запись
     */
    public function fetchFirst(int $type = self::TYPE_FUNCTION_QUERY): array;

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
