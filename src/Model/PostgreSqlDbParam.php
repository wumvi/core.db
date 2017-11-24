<?php
declare(strict_types = 1);

namespace Wumvi\Classes\Db\Model;

use Wumvi\Classes\Read;

/**
 * Модель параметров для БД
 * @method string getDb() Имя базы данных
 * @method string getHost() Хост
 * @method string getPort() Порт
 * @method string getUser() Имя пользователя
 * @method string getPwd() Пароль
 */
class PostgreSqlDbParam extends Read
{
}
