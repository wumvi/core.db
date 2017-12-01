<?php
declare(strict_types = 1);

namespace Core\Db\Exception;

/**
 * Не валидный SQL
 */
class DbBadSqlException extends DbException
{
    public const ERROR_TO_EXEC_SQL = 1;
}
