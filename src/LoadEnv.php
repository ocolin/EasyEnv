<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv;

use Exception;
use Ocolin\EasyEnv\Errors\EasyEnvFileHandleError;
use Ocolin\EasyEnv\Errors\EasyEnvInvalidRowError;
use Ocolin\EasyEnv\Errors\EasyEnvInvalidFilePathError;
readonly class LoadEnv
{


/* CONSTRUCTOR
---------------------------------------------------------------------------- */

    /**
     * @param string|array<string> $files Path to environment file.
     * @param bool $append Don't add environment variables that already exist.
     * @param bool $silent Ignore any errors.
     * @param bool $system Set system environment as well.
     * @throws EasyEnvFileHandleError
     * @throws EasyEnvInvalidFilePathError
     */
    public function __construct(
        private string|array $files,
        private bool $append = false,
        private bool $silent = false,
        private bool $system = true
    )
    {
        $file_list = [];
        if( is_array( $this->files )) {
            $file_list = $this->files;
        }
        else {
            $file_list[] = $files;
        }


        foreach( $file_list as $file ) {
            if( !is_string( $file ) ) {
                if( $this->silent === true ) { return; }
                throw new EasyEnvInvalidFilePathError( message: "A file path is invalid." );
            }

            $this->load( filename: $file );
        }
    }



/* LOAD AN ENV FILE
---------------------------------------------------------------------------- */

    /**
     * @param string $filename
     * @return void
     * @throws EasyEnvFileHandleError
     * @throws Exception
     */
    private function load( string $filename ) : void
    {
        try {
            $file = file_get_contents( filename: $filename );
        }
        catch( Exception $e) {
            if( $this->silent === true ) {  return; }
            throw new EasyEnvFileHandleError( message: "Unable to load file '{$filename}'" );
        }

        if( $file === false ) {
            if( $this->silent === true ) {  return; }
            throw new EasyEnvFileHandleError( message: "Unable to load file '{$filename}'" );
        }

        $this->parse( raw: $file );
    }


/* PARSE THE TEXT FILE
---------------------------------------------------------------------------- */

    /**
     * Parse the text file
     *
     * @param  string $raw Raw string from Env file text
     * @throws EasyEnvInvalidRowError
     */

    private function parse( string $raw ) : void
    {
        $rows = explode( separator: PHP_EOL, string: trim( $raw ));

        foreach( $rows as $key => $row )
        {
            // IGNORE COMMENTS
            if( str_starts_with( haystack: $row, needle: '#' )) { continue; }
            if( str_starts_with( haystack: $row, needle: '//' )) { continue; }

            list( $param, $value ) = self::parse_Row( row: $row );
            if( $param === '' ) {
                if( !$this->silent ) {
                    throw new EasyEnvInvalidRowError( message: "Row {$key} '{$row}' is not valid" );
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
            if( is_bool( $value )) {  $value = $value ? 1 : 0; }
            else { $value = '' . $value; }

            // Load into system environment
            if( $this->system === true ) {
                putenv( assignment: "{$param}={$value}" );
            }
        }
    }



/* PARSE A ROW OF ENV FILE
---------------------------------------------------------------------------- */

    /**
     * Parse and individual row of text file.
     *
     * @param  string  $row    Single line from env file
     * @return array<int, string|int|float|bool> Array containing parameter
     *  name, and value
     */

    private static function parse_Row( string $row ) : array
    {
        if( !str_contains( $row, needle: '=' )) {
            return [ '', '' ];
        }

        list( $param, $value ) = explode( separator: '=', string: $row, limit: 2 );
        $param = self::parse_Param( param: $param );
        $value = self::parse_Value( value: $value );

        return [ $param, $value ];
    }



/* PARSE ENV PARAMETER
---------------------------------------------------------------------------- */

    /**
     * Parse individual parameter name.
     *
     * @param  string $param  Env parameter name
     * @return string Either trimmed parameter name or false if invalid
     */

    private static function parse_Param( string $param ) : string
    {
        $param = trim( string: $param, characters: '"' );
        $param = trim( string: $param, characters: "'" );
        if(
            empty( $param ) OR
            !preg_match( pattern: "#^[A-Z_][A-Z0-9_]*$#i", subject: $param )
        ) {
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
        $value = trim( string: $value, characters: '"' );
        $value = trim( string: $value, characters: "'" );

        if( self::is_An_Int( input: $value )) {
            return (int)$value;
        }

        if( is_numeric( value: $value )) {
            return (float)$value;
        }

        if( strtolower( string: $value ) == 'true' ) {
            return true;
        }

        if( strtolower( string: $value ) == 'false' ) {
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
        if( strlen( $input ) === 0 ) { return false; }
        if ( $input[0] == '-' ) {
            return ctype_digit( text: substr( string: $input, offset: 1 ));
        }

        return ctype_digit( text: $input );
    }

}