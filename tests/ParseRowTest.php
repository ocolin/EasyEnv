<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use ReflectionMethod;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class ParseRowTest extends TestCase
{
    public function testParseRowString() : void
    {
        $output = self::invokeParseValue( row: 'PARAM=VALUE' );
        self::assertSame( ['PARAM', 'VALUE' ], $output );
    }

    public function testParseRowInt() : void
    {
        $output = self::invokeParseValue( row: 'PARAM=123' );
        self::assertSame( ['PARAM', 123 ], $output );
    }

    public function testParseRowFloat() : void
    {
        $output = self::invokeParseValue( row: 'PARAM=123.123' );
        self::assertSame( ['PARAM', 123.123 ], $output );
    }

    public function testParseRowBool() : void
    {
        $output = self::invokeParseValue( row: 'PARAM=True' );
        self::assertSame( ['PARAM', true ], $output );
    }

    public function testParseRowQuoted() : void
    {
        $output = self::invokeParseValue( row: 'PARAM="True"' );
        self::assertSame( ['PARAM', 'True' ], $output );
    }

    public function testParseRowExtraEqualSign() : void
    {
        $output = self::invokeParseValue( row: 'PARAM=ABC=DEF' );
        self::assertSame( ['PARAM', 'ABC=DEF' ], $output );
    }

    public function testParseRowNoEqualSign() : void
    {
        $output = self::invokeParseValue( row: 'PARAM ABCDEF' );
        self::assertNull( $output );
    }

    public function testParseRowInvalidParam() : void
    {
        $output = self::invokeParseValue( row: '1PARAM=ABCDEF' );
        self::assertNull( $output );
    }

    public function testParseRowEmptyParam() : void
    {
        $output = self::invokeParseValue( row: '"PARAM"=VALUE' );
        self::assertSame( ['PARAM', 'VALUE' ], $output );
    }

    public function testParseRowEmptyValue() : void
    {
        $output = self::invokeParseValue( row: '"PARAM"=' );
        self::assertSame( ['PARAM', '' ], $output );
    }




    private static function invokeParseValue( string $row ) : ?array
    {
        $method = ReflectionMethod::createFromMethodName( method: Env::class . '::parseRow' );
        return $method->invoke( null, $row );
    }
}