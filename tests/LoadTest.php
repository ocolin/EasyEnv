<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use Ocolin\EasyEnv\EasyEnvFileHandleError;
use Ocolin\EasyEnv\Env;
use PHPUnit\Framework\TestCase;

class LoadTest extends TestCase
{

    public function testLoadSingleFile() : void
    {
        Env::load( files: $_ENV['FIXTURES_DIR'] . '/load1.env' );
        $this->assertSame( 'localhost', $_ENV['DB_HOST'] );
        $this->assertSame( 3306, $_ENV['DB_PORT'] );
    }

    public function testLoadMultiFile() : void
    {
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ]);
        $this->assertSame( 'remotehost', $_ENV['DB_HOST'] );
        $this->assertSame( 3306, $_ENV['DB_PORT'] );
        $this->assertSame( 'MyApp', $_ENV['APP_NAME'] );
    }

    public function testLoadMultiFileWithAppend() : void
    {
        $_ENV['DB_HOST'] = 'PREEXISTING_HOST';
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ], append: true );
        $this->assertSame( 'PREEXISTING_HOST', $_ENV['DB_HOST'] );
        $this->assertSame( 3306, $_ENV['DB_PORT'] );
        $this->assertSame( 'MyApp', $_ENV['APP_NAME'] );
    }

    public function testLoadNoSilence() : void
    {
        $this->expectException( EasyEnvFileHandleError::class );
        Env::load( files: '/bad/directory/.env' );
    }

    public function testLoadWithSilence() : void
    {
        $this->expectNotToPerformAssertions();
        Env::load( files: '/bad/directory/.env', silent: true );
    }


    public function testLoadWithSystem() : void
    {
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ], system: true );
        $this->assertSame( 'remotehost', getenv( name: 'DB_HOST' ));
    }

    public function testLoadNoSystem() : void
    {
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ], system: false );
        $this->assertFalse( getenv( name: 'DB_HOST' ) );
    }

    public function testLoadWithServer() : void
    {
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ], server: true );
        $this->assertSame( 'remotehost', $_SERVER['DB_HOST'] );
    }

    public function testLoadNoServer() : void
    {
        Env::load( files: [
            $_ENV['FIXTURES_DIR'] . '/load1.env',
            $_ENV['FIXTURES_DIR'] . '/load2.env'
        ], server: false );
        $this->assertArrayNotHasKey( 'DB_PORT', $_SERVER );
    }

    protected function tearDown(): void
    {
        $keys = [ 'DB_HOST', 'DB_PORT' , 'APP_NAME' ];

        foreach( $keys as $key ) {
            unset( $_ENV[$key] );
            unset( $_SERVER[$key] );
            putenv( assignment: $key );
        }
    }
}