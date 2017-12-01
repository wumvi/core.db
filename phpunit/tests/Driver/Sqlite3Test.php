<?php
declare(strict_types=1);

use Core\Db\Common\DriverInterface;
use Core\Db\Common\FetchInterface;
use Core\Db\Driver\Sqlite3;
use Core\Db\Model\Sqlite3DbParam;
use PHPUnit\Framework\TestCase;
use Core\Db\Exception\DbException;

/**
 * @covers \Core\Db\Driver\Sqlite3
 */
class Sqlite3Test extends TestCase
{
    private const DB_NAME = 'phpunit/asserts/file/db';

    /**
     * @var Sqlite3DbParam
     */
    private $dbInfo;

    public function setUp(): void
    {
        $this->dbInfo = new Sqlite3DbParam([
            'db' => self::DB_NAME,
        ]);
    }

    /**
     * @covers \Core\Db\Driver\Sqlite3::__construct
     */
    public function testConstructor(): void
    {
        $db = new Sqlite3($this->dbInfo);

        $this->assertTrue($db instanceof DriverInterface, 'DriverInterface');
    }

    /**
     * @covers \Core\Db\Driver\Sqlite3::exec
     *
     * @throws DbException
     */
    public function testExec(): void
    {
        $db = new Sqlite3($this->dbInfo);
        $fetch = $db->exec('select 1', []);

        $this->assertTrue($fetch instanceof FetchInterface, 'FetchInterface');

        $fetch = $db->exec('select 2 as num', []);
        $this->assertTrue($fetch->fetchFirst()['num'] === 2, 'Exec without vars');

        $fetch = $db->exec('select :number as num', ['number' => 3,]);
        $this->assertTrue($fetch->fetchFirst()['num'] === 3, 'Exec with vars');
    }

    /**
     * @covers \Core\Db\Driver\Sqlite3::getArgType
     *
     * @throws \Core\Db\Exception\DbException
     */
    public function testGetArgType(): void
    {
        $db = new Sqlite3($this->dbInfo);

        $this->assertTrue($db->getArgType(3.2) === SQLITE3_FLOAT, 'Float');
        $this->assertTrue($db->getArgType(8) === SQLITE3_INTEGER, 'Int');
        $this->assertTrue($db->getArgType(true) === SQLITE3_INTEGER, 'Bool');
        $this->assertTrue($db->getArgType(null) === SQLITE3_NULL, 'Null');
        $this->assertTrue($db->getArgType('sql') === SQLITE3_TEXT, 'String');
    }

    /**
     * @covers \Core\Db\Driver\Sqlite3::getArgType
     *
     * @throws DbException
     */
    public function testWrongType(): void
    {
        $this->expectException(DbException::class);
        $this->expectExceptionCode(DbException::WRONG_TYPE);

        $db = new Sqlite3($this->dbInfo);

        $db->getArgType(function(){});
    }
}
