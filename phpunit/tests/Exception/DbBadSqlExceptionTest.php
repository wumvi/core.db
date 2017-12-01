<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Db\Exception\DbBadSqlException;
use Core\Db\Exception\DbException;

/**
 * @covers \Core\Db\Exception\DbBadSqlException
 */
class DbBadSqlExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $ex = new DbBadSqlException();
        $this->assertTrue($ex instanceof DbException, 'Constructor');
    }
}
