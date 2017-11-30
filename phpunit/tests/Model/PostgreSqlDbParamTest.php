<?php
declare(strict_types = 1);

use \PHPUnit\Framework\TestCase;
use Core\Db\Model\PostgreSqlDbParam;

/**
 * @covers \Core\Db\Model\PostgreSqlDbParam
 */
class PostgreSqlDbParamTest extends TestCase
{
    private const HOST = 'localhost';
    private const PORT = 1234;
    private const DB_NAME = 'test';
    private const USER = 'user';
    private const PASSWORD = 'password';

    /**
     */
    public function testCreation() {
        $dbInfo = new PostgreSqlDbParam([
            'host' => self::HOST,
            'port' => self::PORT,
            'dbName' => self::DB_NAME,
            'user' => self::USER,
            'password' => self::PASSWORD,
        ]);

        $this->assertTrue($dbInfo->getHost() === self::HOST, 'Host');
        $this->assertTrue($dbInfo->getPort() === self::PORT, 'Port');
        $this->assertTrue($dbInfo->getDbName() === self::DB_NAME, 'Db name');
        $this->assertTrue($dbInfo->getUser() === self::USER, 'User');
        $this->assertTrue($dbInfo->getPassword() === self::PASSWORD, 'Pwd');
    }
}
