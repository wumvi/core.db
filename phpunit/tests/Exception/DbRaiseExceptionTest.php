<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Db\Exception\DbRaiseException;
use Core\Db\Exception\DbException;

/**
 * @covers \Core\Db\Exception\DbRaiseException
 */
class DbRaiseExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $ex = new DbRaiseException();
        $this->assertTrue($ex instanceof DbException, 'Constructor');
    }
}
