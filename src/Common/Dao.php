<?php
declare(strict_types=1);

namespace Wumvi\Classes\Db\Common;

/**
 * Основа дао класса
 */
abstract class Dao
{
    /** @var DriverInterface Драйвер */
    public $driver;

    /**
     * Конструктор
     * @param DriverInterface $driver Драйвер
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
}
