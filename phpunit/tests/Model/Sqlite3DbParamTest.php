<?php
declare(strict_types = 1);

use \PHPUnit\Framework\TestCase;
use Core\Db\Model\Sqlite3DbParam;

/**
 * @covers \Core\Db\Model\Sqlite3DbParam
 */
class Sqlite3DbParamTest extends TestCase
{
    private const DB_NAME = 'test';

    /**
     * @covers \Core\Db\Model\Sqlite3DbParam::__construct
     */
    public function testCreation() {
        $dbInfo = new Sqlite3DbParam([
            'db' => self::DB_NAME,
        ]);

        $this->assertTrue($dbInfo->getDb() === self::DB_NAME, 'Db Name');
    }
}
