<?php
declare(strict_types = 1);

namespace Wumvi\Classes\Db\Common;

/**
 * Интерфейс для реализации драйвера БД
 */
interface DriverInterface
{
    /**
     * @param string $funcName Название функции
     * @param array $bind Bind переменных
     * @return FetchAbstract Fetch
     */
    public function exec(string $funcName, array $bind = []) : FetchAbstract;
}
