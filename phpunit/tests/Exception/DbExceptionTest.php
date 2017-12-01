<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Db\Exception\DbException;

/**
 * @covers \Core\Db\Exception\DbException
 */
class DbExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $ex = new DbException();
        $this->assertTrue($ex instanceof \Exception, 'Constructor');
    }
}
