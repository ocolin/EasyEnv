<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use ReflectionMethod;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class ParseParamTest extends TestCase
{

    public function testParseParamGood() : void
    {
        $output = self::invokeParseParam( param: 'DB_HOST' );
        $this->assertSame( 'DB_HOST', $output );
    }

    public function testParseParamDblQuotes() : void
    {
        $output = self::invokeParseParam( param: '"DB_HOST"' );
        $this->assertSame( 'DB_HOST', $output );
    }

    public function testParseParamSingleQuotes() : void
    {
        $output = self::invokeParseParam( param: "'DB_HOST'" );
        $this->assertSame( 'DB_HOST', $output );
    }

    public function testParseParamSpace() : void
    {
        $output = self::invokeParseParam( param: ' DB_HOST' );
        $this->assertSame( 'DB_HOST', $output );
    }

    public function testParseParamNoAlphaNum() : void
    {
        $output = self::invokeParseParam( param: 'DB HOST' );
        $this->assertNull( $output );
    }

    public function testParseParamNumberStart() : void
    {
        $output = self::invokeParseParam( param: '9DB HOST' );
        $this->assertNull( $output );
    }

    public function testParseParamEmpty() : void
    {
        $output = self::invokeParseParam( param: '' );
        $this->assertNull( $output );
    }



    private static function invokeParseParam( string $param ) : ?string
    {
        $method = ReflectionMethod::createFromMethodName( method: Env::class . '::parseParam' );
        return $method->invoke( null, $param );
    }
}