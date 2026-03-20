<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv;

/**
 * EasyEnv - Simple environment variable loader for PHP
 * @link https://github.com/ocolin/EasyEnv
 */

class Env
{
    private function __construct() {}


/* LOAD ENV FILE OR FILES
---------------------------------------------------------------------------- */

    /**
     * Load a file or multiple files into your environment.
     *
     * @param string|array<string> $files env files or files.
     * @param bool $append Don't over wright existing variables.
     * @param bool $silent Don't throw errors on missing or unreadable files.
     * @param bool $system Set system environment variables.
     * @param bool $server Set PHP $_SERVER variables.
     * @return void
     * @throws EasyEnvFileHandleError
     */
    public static function load(
        string|array $files,
        bool $append = false,
        bool $silent = false,
        bool $system = true,
        bool $server = false
    ) : void
    {
        // GET PROPERTIES
        $properties = [];
        if( is_string( $files ) ) { $files = [$files]; }
        foreach( $files as $file )
        {
            try {
                $properties = array_merge(
                    $properties, self::parse( file: $file )
                );
            }
            catch ( EasyEnvFileHandleError $e ) {
                if( $silent === false ) { throw $e; }
            }
        }

        // INSERT PROPERTIES
        foreach( $properties as $param => $value )
        {
            if(
                $append === false  OR
                !array_key_exists( key: $param, array: $_ENV )
            ) {
                $_ENV[$param] = $value;
            }

            if( $system === true ) {
                if(
                    $append === false OR
                    !array_key_exists( key: $param, array: getenv())
                ) {
                    $stringValue = match( true ) {
                        is_bool( value: $value ) => $value ? 'true' : 'false',
                        default => (string)$value
                    };
                    putenv( assignment: "{$param}={$stringValue}" );
                }
            }

            if( $server === true ) {
                if(
                    $append === false  OR
                    !array_key_exists( key: $param, array: $_SERVER )
                ) {
                    $_SERVER[$param] = $value;
                }
            }
        }
    }



/* PARSE AN ENV FILE
---------------------------------------------------------------------------- */

    /**
     * Load environment files into an array for use other than environment.
     *
     * @param string $file File to load.
     * @return array<string, int|float|string|bool> Parameters and values.
     * @throws EasyEnvFileHandleError
     */
    public static function parse( string $file ) : array
    {
        $output = [];

        $raw = self::loadFile( filename: $file );
        $rows = preg_split( pattern: '#\r\n|\r|\n#', subject: trim( $raw ));
        if( $rows === false ) { return []; }

        foreach( $rows as $row )
        {
            $row = self::stripComments( row: $row );
            if( !str_contains( haystack: $row, needle: '=' )) { continue; }

            $parse = self::parseRow( row: $row );
            if( $parse === null ) { continue; }

            $output[$parse[0]] = $parse[1];
        }

        return $output;
    }



/* LOAD FILE CONTENTS
---------------------------------------------------------------------------- */

    /**
     * Load the file contents from disk.
     *
     * @param string $filename Name of file or directory to load.
     * @return string Raw file text contents.
     * @throws EasyEnvFileHandleError
     */
    private static function loadFile( string $filename ) : string
    {
        if( is_dir( filename: $filename )) { $filename .= '/.env'; }

        if( !file_exists( filename: $filename )) {
            throw new EasyEnvFileHandleError(
                message: "File '{$filename}' does not exist."
            );
        }
        if( !is_readable( filename: $filename )) {
            throw new EasyEnvFileHandleError(
                message: "File '{$filename}' is not readable."
            );
        }

        $file = file_get_contents( filename: $filename );

        if( $file === false ) {
            throw new EasyEnvFileHandleError(
                message: "Unable to load file '{$filename}'."
            );
        }

        return $file;
    }



/* STRIP COMMENTS OUT OF ROWS
---------------------------------------------------------------------------- */

    /**
     * Strip out comments in an environment variable row.
     *
     * @param string $row .env file individual row.
     * @return string Row with comments stripped out.
     */
    private static function stripComments( string $row ) : string
    {
        if( str_starts_with( $row, '#' )) { return ''; }
        if( str_starts_with( $row, '//' )) { return ''; }

        $row = strstr( haystack: $row, needle: ' #', before_needle: true ) ?: $row;
        $row = strstr( haystack: $row, needle: ' //', before_needle: true ) ?: $row;

        return trim( string: $row );
    }



/* PARSE A SINGLE ROW
---------------------------------------------------------------------------- */

    /**
     * Parse a single row of an environment file.
     *
     * @param string $row Unparsed text row.
     * @return array{string, int|float|string|bool}|null Array containing a parameter and value.
     */
    private static function parseRow( string $row ) : ?array
    {
        $parts = explode( separator: '=', string: $row, limit: 2 );
        if( count( $parts ) !== 2 ) { return null; }

        $param = self::parseParam( param: $parts[0] );

        if( $param === null ) { return null; }

        return [ $param, self::parseValue( value: $parts[1] )];
    }



/* PARSE ENV PARAMETER
---------------------------------------------------------------------------- */

    /**
     * Parse individual parameter name.
     *
     * @param  string $param  Env parameter name
     * @return ?string Trimmed parameter name, or null string if invalid.
     */

    private static function parseParam( string $param ) : ?string
    {
        $param = trim( string: $param );
        $param = trim( string: $param, characters: '"' );
        $param = trim( string: $param, characters: "'" );
        if(
            !preg_match( pattern: "#^[A-Z_][A-Z0-9_]*$#i", subject: $param )
        ) {
            return null;
        }

        return trim( string: $param );
    }



/* PARSE PARAMETER VALUE
---------------------------------------------------------------------------- */

    /**
     * Parse an environment variable value into its type.
     *
     * @param string $value Original string value to evaluate.
     * @return int|float|string|bool Typed value.
     */
    private static function parseValue( string $value ) : int|float|string|bool
    {
        // QUOTED STRING
        $unquoted = self::parseQuotedString( quotedString: $value );
        if( $unquoted !== null ) { return $unquoted; }

        // INTEGER
        $int = filter_var( value: $value, filter: FILTER_VALIDATE_INT );
        if( $int !== false ) { return $int; }

        // FLOAT
        $float = filter_var( value: $value, filter: FILTER_VALIDATE_FLOAT );
        if( $float !== false ) { return $float; }

        // BOOLEAN
        $lower = strtolower( string: $value );
        if( $lower === 'true' ) { return true; }
        if( $lower === 'false' ) { return false; }

        // STRING
        return $value;
    }



/* PARSE QUOTED STRINGS
---------------------------------------------------------------------------- */

    /**
     * Parse the contents from a variable that is in quotes.
     *
     * @param string $quotedString String being stripped of quotes.
     * @return string|null Unquoted string or null if no quotes found.
     */
    private static function parseQuotedString( string $quotedString ) : ?string
    {
        if( strlen( $quotedString ) < 2 ) { return null; }

        $quotes = [ "'", '"' ];
        $firstChar = $quotedString[0];
        if( !in_array( needle: $firstChar, haystack: $quotes )) { return null; }

        $lastChar = substr( string: $quotedString, offset: -1 );
        if( $lastChar !== $firstChar ) { return null; }

        $string = substr( string: $quotedString, offset: 1, length: -1 );

        return str_replace(
            search: '\\' . $firstChar, replace: $firstChar, subject: $string
        );
    }
}