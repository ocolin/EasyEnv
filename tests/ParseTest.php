<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv\Tests;

use Ocolin\EasyEnv\EasyEnvFileHandleError;
use Ocolin\EasyEnv\Env;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;


class ParseTest extends TestCase
{
    private vfsStreamDirectory $root;

    public function testParseGood() : void
    {
        $output = Env::parse( file: $_ENV['FIXTURES_DIR'] . '/valid.env' );
        $this->assertIsArray( $output );
        $this->assertCount( 6, $output );
        $this->assertArrayHasKey( 'DB_HOST', $output );
        $this->assertSame( 'localhost', $output['DB_HOST'] );
        $this->assertSame( 3306, $output['DB_PORT'] );
    }

    public function testParseEmpty() : void
    {
        $output = Env::parse( file: $_ENV['FIXTURES_DIR'] . '/empty.env' );
        $this->assertEmpty( $output );
    }

    public function testParseDirectory() : void
    {
        $output = Env::parse( file: $_ENV['FIXTURES_DIR'] . '/envDir' );
        $this->assertIsArray( $output );
        $this->assertCount( 6, $output );
        $this->assertArrayHasKey( 'DB_HOST', $output );
        $this->assertSame( 'localhost', $output['DB_HOST'] );
        $this->assertSame( 3306, $output['DB_PORT'] );
    }

    public function testParseException() : void
    {
        $this->expectException( EasyEnvFileHandleError::class );
        Env::parse( file: '/fake/path/.env' );
    }

    public function testParseUnreadableFile() : void
    {
        vfsStream::newFile( name: 'unreadable.env', permissions: 0000 )->at( $this->root );
        $path = vfsStream::url( path: 'testDir/unreadable.env' );

        $this->expectException( EasyEnvFileHandleError::class );
        Env::parse( file: $path );
    }

    protected function setUp() : void
    {
        $this->root = vfsStream::setup( rootDirName: 'testDir' );
    }
}