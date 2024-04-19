<?php

declare( strict_types = 1 );

namespace Ocolin\Env;

use Exception;

/**
 * @author      Colin Miller <snoopy@teaboyaudio.com>
 * @copyright   2024 Teaboyaudio
 * @license     MIT
 * @package     ocolin\env
 * @version     1.0
 * 
 * Super basic tool for loading environment variables from a file.
 * @example Env::loadEnv( path: __DIR__ . '/.env' );
 */

class Env {

    /**
     * @var string $path The file path of your environment file.
     */
    private string $path = '';

    /**
     * @var bool $silent_mode Ignore any errors other than missing file.
     */
    private bool $silent_mode = true;

    /**
     * @var bool $append Do not overwrite pre-existing parameters.
     */
    private bool $append = false;


/* CONSTRUCTOR
---------------------------------------------------------------------------- */

    /**
     * @param string $path   Path to environment file
     * @param bool   $silent Ignore errors and continue on
     * @param bool   $append Don't add env params that are already set
     * @throws Exception
     */

    public function __construct(
        string $path,
          bool $silent = true,
          bool $append = false
    )
    {
        if( !file_exists( $path )) {
            throw new Exception( message: "File {$path} not found" );
        }

        $this->silent_mode = $silent;
        $this->path        = $path;
        $this->append      = $append;
    }



/* LOAD ENVIRONMENT VARIABLES
---------------------------------------------------------------------------- */

    /**
     * Load variables into the environment.
     * @throws Exception
     */
    public function load() : void
    {
        try {
            $raw = trim( (string)file_get_contents($this->path) );
        }
        catch( Exception $e ) {
            return;
        }

        $this->parse( raw: $raw );
    }



/* STATIC METHOD TO LOAD ENV
---------------------------------------------------------------------------- */

    /**
     * Static function to load class and environment variables
     *
     * @param string $path Path to environment file
     * @param bool $silent Ignore errors and continue on
     * @param bool $append Don't add env params that are already set
     * @throws Exception
     */

    public static function loadEnv(
        string $path,
          bool $silent = true,
          bool $append = false
    ) : void
    {
        $o = new self( path: $path, silent: $silent, append: $append );
        $o->load();
    }



/* PARSE THE TEXT FILE
---------------------------------------------------------------------------- */

    /**
     * Parse the text file
     *
     * @param  string $raw Raw string from Env file text
     * @throws Exception
     */

    private function parse( string $raw ) : void
    {
        $rows = explode( PHP_EOL, trim( $raw ));

        foreach( $rows as $key => $row )
        {
            if( str_starts_with( $row, needle: '#' )) { continue; }
            if( str_starts_with( $row, needle: '//' )) { continue; }

            list( $param, $value ) = self::parse_Row( row: $row );
            if( $param === '' ) {
                if( !$this->silent_mode ) {
                    throw new Exception( "Row {$key} '{$row}' is not valid" );
                }
                continue;
            }

            if( $this->append ) {
                if( !empty( $_ENV[$param] )) {
                    continue;
                }
            }

            // Load into PHP environment
            $_ENV[$param] = $value;

            // Load into System Env for sharing with other programs
            $param = '' . $param;
            if( is_bool( $value )) {
                $value = $value ? 1 : 0;
            }
            else {
                $value = '' . $value;
            }

            // Load into system environment
            putenv( assignment: "{$param}='{$value}'" );
        }
    }



/* PARSE A ROW OF ENV FILE
---------------------------------------------------------------------------- */

/**
 * Parse and individual row of text file.
 *
 * @param  string  $row    Single line from env file
 * @return array<int, mixed> Array containing parameter name, and value
 */

    private static function parse_Row( string $row ) : array
    {
        if( !str_contains( $row, needle: '=' )) {
            return [ '', false ];
        }

        list( $param, $value ) = explode( '=', $row, limit: 2 );
        $param = self::parse_Param( $param );
        $value = self::parse_Value( $value );

        return [ $param, $value ];
    }



/* PARSE ENV PARAMETER
---------------------------------------------------------------------------- */

/**
 * Parse individual parameter name.
 *
 * @param  string $param  Env parameter name
 * @return string         Either trimmed parameter name or false if invalid
*/

    private static function parse_Param( string $param ) : string
    {
        $param = trim( $param, characters: '"' );
        $param = trim( $param, characters: "'" );
        if( empty( $param ) OR !preg_match( "#^[A-Z_][A-Z0-9_]*$#i", $param )) {
            return '';
        }

        return $param;
    }



/* PARSE ENV VALUE
---------------------------------------------------------------------------- */

/**
 * Parse individual parameter value.
 *
 * @param  string  $value         Env value to be parsed
 * @return int|float|string|bool  parsed and cast parameter value
*/

    private static function parse_Value( string $value ): int|float|string|bool
    {
        $value = trim( $value, characters: '"' );
        $value = trim( $value, characters: "'" );

        if( self::is_An_Int( $value )) {
            return (int)$value;
        }

        if( is_numeric( $value )) {
            return (float)$value;
        }

        if( strtolower( $value ) == 'true' ) {
            return true;
        }

        if( strtolower( $value ) == 'false' ) {
            return false;
        }

        return $value;
    }



/* TEST IF STRING IS AN INTEGER
---------------------------------------------------------------------------- */

/**
 * Check if string can be an integer.
 *
 * @param   string  $input  String to be tested as an integer
 * @return  bool    Whether string is an integer or not
*/

    private static function is_An_Int( string $input ) : bool
    {
        if ( $input[0] == '-' ) {
            return ctype_digit( substr( $input, offset: 1 ));
        }

        return ctype_digit( $input );
    }
}