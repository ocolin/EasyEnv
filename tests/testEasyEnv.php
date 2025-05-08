<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use Ocolin\EasyEnv\LoadEnv;
use PHPUnit\Framework\TestCase;

class testEasyEnv extends TestCase
{

/* TEST BASIC FUNCTIONS
---------------------------------------------------------------------------- */

    public function testBasic() : void
    {
        new LoadEnv( files: __DIR__ . '/.env1' );
        $sys = getenv();

        $this->assertIsArray( actual: $_ENV );
        $this->assertIsArray( actual: $sys );

        $this->assertArrayHasKey( key: 'TEST1', array: $_ENV );
        $this->assertArrayHasKey( key: 'TEST2', array: $_ENV );

        $this->assertArrayHasKey( key: 'TEST1', array: $sys );
        $this->assertArrayHasKey( key: 'TEST2', array: $sys );

        $this->assertEquals( expected: 'Answer1', actual: $_ENV['TEST1'] );
        $this->assertEquals( expected: 'Answer2', actual: $_ENV['TEST2'] );

        $this->assertEquals( expected: 'Answer1', actual: $sys['TEST1'] );
        $this->assertEquals( expected: 'Answer2', actual: $sys['TEST2'] );
    }



/* TEST APPEND OPTION
---------------------------------------------------------------------------- */

    public function testAppend() : void
    {
        $_ENV['TEST1'] = 'Test Value';
        putenv( assignment: 'TEST1=Test Value' );
        new LoadEnv( files: __DIR__ . '/.env1', append: true );
        $sys = getenv();

        $this->assertEquals( expected: 'Test Value', actual: $_ENV['TEST1'] );
        $this->assertEquals( expected: 'Test Value', actual: $sys['TEST1'] );
    }
}