<?php
declare(strict_types = 1);

namespace Core\Db\Model;

use Core\Model\Read;

/**
 * Модель параметров для БД
 * @method string getDbName() Имя базы данных
 * @method string getHost() Хост
 * @method string getPort() Порт
 * @method string getUser() Имя пользователя
 * @method string getPassword() Пароль
 */
class PostgreSqlDbParam extends Read
{
}
