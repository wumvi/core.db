<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Db\Exception\DbConnectionException;
use Core\Db\Exception\DbException;

/**
 * @covers \Core\Db\Exception\DbConnectionException
 */
class DbConnectionExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $ex = new DbConnectionException();
        $this->assertTrue($ex instanceof DbException, 'Constructor');
    }
}
