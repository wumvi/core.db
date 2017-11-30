<?php
declare(strict_types = 1);

namespace Core\Db\Exception;

/**
 * @codeCoverageIgnore
 */
class DbException extends \Exception
{
    public const WRONG_MAPPING = 1;
    public const WRONG_TYPE = 2;
}
