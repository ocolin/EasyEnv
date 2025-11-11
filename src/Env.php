<?php

declare( strict_types = 1 );

namespace Ocolin\EasyEnv;

readonly class Env
{

/* GET STRING VALUE OF ENVIRONMENT VARIABLE
---------------------------------------------------------------------------- */

    /**
     * @param string $name Name of parameter to get.
     * @return string Parameter value, or empty string if not found.
     */
    public static function getString( string $name ) : string
    {
        $value = getenv( name: $name );
        if( !is_string( $value )) { return ''; }

        return $value;
    }


/* GET STRING VALUE OR NULL ENVIRONMENT VALUE
---------------------------------------------------------------------------- */

    /**
     * @param string $name Name of environment variable to get.
     * @return string|null String value of variable, or null if not found.
     */
    public static function getStringNull( string $name ) : string | null
    {
        $value = getenv( name: $name );
        if( is_string( $value )) { return $value; }

        return null;
    }


/* GET BOOL VALUE OF ENVIRONMENT VARIABLE
---------------------------------------------------------------------------- */

    /**
     * @param string $name Name of environment variable to get.
     * @return bool Value of environment variable.
     */
    public static function getBool( string $name ) : bool
    {
        $value = getenv( name: $name );
        if( !is_bool( $value )) { return false; }

        return $value;
    }


/* GET BOOL VALUE OF ENVIRONMENT VARIABLE OR NULL
---------------------------------------------------------------------------- */

    /**
     * @param string $name Name of environment variable to get.
     * @return bool|null Bool value, or null if it is not found.
     */
    public static function getBoolNull( string $name ) : bool | null
    {
        $value = getenv( name: $name );
        if( is_bool( $value )) { return $value; }

        return null;
    }
}