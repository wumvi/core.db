<?php

namespace Core\Db\Common;

/**
 * Interface Fields
 *
 * @codeCoverageIgnore
 */
interface FieldsInterface
{
    /** Навание поля */
    const ARRAY_NAME = 1;

    /** Тип поля */
    const ARRAY_TYPE = 2;

    /** Тип поля это уникальный ключ */
    const ARRAY_KEY_PK = 3;

    /** Тип Integer */
    const TYPE_INT = 1;

    /** Тип Float */
    const TYPE_FLOAT = 2;

    /** Тип String */
    const TYPE_STRING = 3;

    /** Тип Дата */
    const TYPE_DATE = 4;

    /** Тип Boolean */
    const TYPE_BOOL = 5;

    /** Тип Array */
    const TYPE_ARRAY = 6;

    /** Тип Array */
    const TYPE_UNIXTIME = 7;
}
