<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use ReflectionMethod;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class ParseQuotedStringTest extends TestCase
{
    public function testParseQuotedStringDoubleQuote() : void
    {
        $output = self::invokeParseQuotedString( value: '"TEST STRING"' );
        $this->assertSame( 'TEST STRING', $output );
    }

    public function testParseQuotedStringSingleQuote() : void
    {
        $output = self::invokeParseQuotedString( value: "'TEST STRING'" );
        $this->assertSame( 'TEST STRING', $output );
    }

    public function testParseQuotedStringMismatch() : void
    {
        $output = self::invokeParseQuotedString( value: "'TEST STRING\"" );
        $this->assertNull( $output );
    }

    public function testParseQuotedStringUnclosedQuotes() : void
    {
        $output = self::invokeParseQuotedString( value: "'TEST STRING" );
        $this->assertNull( $output );
    }

    public function testParseQuotedStringSingleChar() : void
    {
        $output = self::invokeParseQuotedString( value: "T" );
        $this->assertNull( $output );
    }

    public function testParseQuotedStringEmptyQuote() : void
    {
        $output = self::invokeParseQuotedString( value: '""' );
        $this->assertSame( '', $output );
    }

    public function testParseQuotedStringEscapedQuote() : void
    {
        $output = self::invokeParseQuotedString( value: '"TEST \"STRING"' );
        $this->assertSame( 'TEST "STRING', $output );
    }

    public function testParseQuotedStringPreserveSpacee() : void
    {
        $output = self::invokeParseQuotedString( value: '"TEST  STRING"' );
        $this->assertSame( 'TEST  STRING', $output );
    }

    public function testParseQuotedStringUnquoted() : void
    {
        $output = self::invokeParseQuotedString( value: 'TEST' );
        $this->assertNull( $output );
    }



    private static function invokeParseQuotedString( string $value ) : ?string
    {
        $method = ReflectionMethod::createFromMethodName( method: Env::class . '::parseQuotedString' );
        return $method->invoke( null, $value );
    }
}