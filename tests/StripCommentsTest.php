<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use ReflectionMethod;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class StripCommentsTest extends TestCase
{
    public function testStripCommentsGood() : void
    {
        $output = self::invokeStripComments( row: 'DB_HOST=localhost' );
        $this->assertSame( 'DB_HOST=localhost', $output );
    }

    public function testStripCommentsStartHash() : void
    {
        $output = self::invokeStripComments( row: '#DB_HOST=localhost' );
        $this->assertEmpty( $output );
    }

    public function testStripCommentsStartSlash() : void
    {
        $output = self::invokeStripComments( row: '//DB_HOST=localhost' );
        $this->assertEmpty( $output );
    }

    public function testStripCommentsInlineHash() : void
    {
        $output = self::invokeStripComments( row: 'DB_HOST=localhost # Comment' );
        $this->assertSame( 'DB_HOST=localhost', $output );
    }

    public function testStripCommentsInlineSlash() : void
    {
        $output = self::invokeStripComments( row: 'DB_HOST=localhost // Comment' );
        $this->assertSame( 'DB_HOST=localhost', $output );
    }

    public function testStripCommentsHexColor() : void
    {
        $output = self::invokeStripComments( row: 'DB_HOST=#000000' );
        $this->assertSame( 'DB_HOST=#000000', $output );
    }

    public function testStripCommentsWhiteSpace() : void
    {
        $output = self::invokeStripComments( row: 'DB_HOST=#000000  ' );
        $this->assertSame( 'DB_HOST=#000000', $output );
    }

    private static function invokeStripComments( string $row ) : string
    {
        $method = ReflectionMethod::createFromMethodName( method: Env::class . '::stripComments' );
        return $method->invoke( null, $row );
    }
}