<?php

namespace Core\Db\Exception;

class DbConnectionException extends DbException
{
    public const CAN_NOT_CONNECT_TO_DB = 1;
}
