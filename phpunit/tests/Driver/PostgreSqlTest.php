<?php
declare(strict_types=1);

use Assert\Mockup;
use Core\Db\Common\DriverInterface;
use Core\Db\Common\FetchInterface;
use Core\Db\Driver\PostgreSql;
use Core\Db\Exception\DbConnectionException;
use Core\Db\Exception\DbException;
use Core\Db\Model\PostgreSqlDbParam;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Core\Db\Driver\PostgreSql
 */
class PostgreSqlTest extends TestCase
{
    private const HOST = 'localhost';
    private const PORT = 1234;
    private const DB_NAME = 'test';
    private const USER = 'user';
    private const PASSWORD = 'password';

    /**
     * @var PostgreSqlDbParam
     */
    private $dbInfo;

    public function setUp(): void
    {
        $this->dbInfo = new PostgreSqlDbParam([
            'host' => self::HOST,
            'port' => self::PORT,
            'dbname' => self::DB_NAME,
            'user' => self::USER,
            'password' => self::PASSWORD,
        ]);
    }

    private function makeMockUp(): Mockup
    {
        $mockup = new Mockup();
        $mockup->setFunction('pg_connect', 3);
        $mockup->setFunction('pg_close', true);

        return $mockup;
    }

    /**
     * @covers \Core\Db\Driver\PostgreSql::__construct
     * @covers \Core\Db\Driver\PostgreSql::__destruct
     */
    public function testConstructor(): void
    {
        $mockup = $this->makeMockUp();

        $postgreSql = new PostgreSql($this->dbInfo);

        $this->assertTrue($postgreSql instanceof DriverInterface, 'Constructor');

        unset($postgreSql, $mockup);
    }

    /**
     * @covers \Core\Db\Driver\PostgreSql::__construct
     * @covers \Core\Db\Driver\PostgreSql::__destruct
     */
    public function testWrongConstructor(): void
    {
        $this->expectException(DbConnectionException::class);
        $this->expectExceptionCode(DbConnectionException::CAN_NOT_CONNECT_TO_DB);

        $mockup = new Mockup();
        $mockup->setFunction('pg_connect', FALSE);
        $mockup->setFunction('pg_close', true);

        $postgreSql = new PostgreSql($this->dbInfo);

        unset($postgreSql, $mockup);
    }

    /**
     * @covers \Core\Db\Driver\PostgreSql::exec
     */
    public function testExec(): void
    {
        $mockup = $this->makeMockUp();

        $postgreSql = new PostgreSql($this->dbInfo);

        $fetch = $postgreSql->exec('me');
        $this->assertTrue($fetch instanceof FetchInterface, 'Constructor');

        unset($postgreSql, $mockup);
    }

    /**
     * @covers \Core\Db\Driver\PostgreSql::makeClearSql
     */
    public function testMakeClearSql(): void
    {
        $mockup = $this->makeMockUp();

        $postgreSql = new PostgreSql($this->dbInfo);

        $fetch = $postgreSql->exec('me()');
        $this->assertEquals($fetch->getSql(), 'me()', 'Empty sql');

        $fetch = $postgreSql->exec('me(:id)', ['id' => 123]);
        $this->assertEquals($fetch->getSql(), 'me(123)', 'Replace id');

        $fetch = $postgreSql->exec('me(:id)', ['id' => [123]]);
        $this->assertEquals($fetch->getSql(), 'me(array[123])', 'Replace array');

        unset($postgreSql, $mockup);
    }

    /**
     * @covers \Core\Db\Driver\PostgreSql::convert
     *
     * @throws \Core\Db\Exception\DbException
     */
    public function testConvertType(): void
    {
        $mockup = $this->makeMockUp();

        $postgreSql = new PostgreSql($this->dbInfo);

        $value = $postgreSql->convert('dd');
        $this->assertEquals($value, "'dd'", 'String');

        $value = $postgreSql->convert('\'dd');
        $this->assertEquals($value, "'''dd'", 'Sql injection');

        $value = $postgreSql->convert(1);
        $this->assertEquals($value, 1, 'Number');

        $value = $postgreSql->convert(2147483648);
        $this->assertEquals($value, '2147483648::bigint', 'Big number');

        $value = $postgreSql->convert(null);
        $this->assertEquals($value, 'null', 'Null');

        $value = $postgreSql->convert(true);
        $this->assertEquals($value, 'true', 'Boolean true');

        $value = $postgreSql->convert(false);
        $this->assertEquals($value, 'false', 'Boolean false');

        $value = $postgreSql->convert([1]);
        $this->assertEquals($value, '[1]', 'Array');

        unset($postgreSql, $mockup);
    }

    /**
     */
    public function testWrongConvertType(): void
    {
        $mockup = $this->makeMockUp();

        $postgreSql = new PostgreSql($this->dbInfo);

        try {
            $postgreSql->convert(function () {
            });
        } catch (DbException $ex) {
            $this->assertEquals($ex->getCode(), DbException::WRONG_TYPE, 'Wrong Type');
        }

        unset($postgreSql, $mockup);
    }
}
