<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use ReflectionMethod;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class ParseValueTest extends TestCase
{

    public function testParseValuePositiveInteger() : void
    {
        $output = self::invokeParseValue( value: '123' );
        $this->assertSame( 123, $output );
    }

    public function testParseValueNegativeInteger() : void
    {
        $output = self::invokeParseValue( value: '-123' );
        $this->assertSame( -123, $output );
    }

    public function testParseValueZeroInteger() : void
    {
        $output = self::invokeParseValue( value: '0' );
        $this->assertSame( 0, $output );
    }

    public function testParseValuePositiveFoat() : void
    {
        $output = self::invokeParseValue( value: '123.123' );
        $this->assertSame( 123.123, $output );
    }

    public function testParseValueNegativeFoat() : void
    {
        $output = self::invokeParseValue( value: '-123.123' );
        $this->assertSame( -123.123, $output );
    }

    public function testParseValueBooleanTrue() : void
    {
        $output = self::invokeParseValue( value: 'true' );
        $this->assertTrue( $output );
    }

    public function testParseValueBooleanFalse() : void
    {
        $output = self::invokeParseValue( value: 'False' );
        $this->assertFalse( $output );
    }

    public function testParseValueString() : void
    {
        $output = self::invokeParseValue( value: 'string' );
        $this->assertSame( 'string', $output );
    }

    public function testParseValueEmpty() : void
    {
        $output = self::invokeParseValue( value: '' );
        $this->assertSame( '', $output );
    }


    public function testParseQuotedStringDoubleQuote() : void
    {
        $output = self::invokeParseValue( value: '"TEST STRING"' );
        $this->assertSame( 'TEST STRING', $output );
    }

    public function testParseQuotedStringDoubleQuoteNumber() : void
    {
        $output = self::invokeParseValue( value: '"123"' );
        $this->assertSame( '123', $output );
    }



    private static function invokeParseValue( string $value ) : string|int|float|bool
    {
        $method = ReflectionMethod::createFromMethodName( method: Env::class . '::parseValue' );
        return $method->invoke( null, $value );
    }
}