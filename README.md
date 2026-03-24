# EasyEnv

## What is it?

EasyEnv is a simple tool for loading environment variables into your PHP project. It is designed for minimal hassle, but with optional parameters for expanded control.

## Why it exists

There are plenty of tools out there for loading environment variables that are wonderful. I wanted to add one that was just as simple to use if not easier, but also with the ability to prevent the data from being inserted into specific global or environmental spaces. 

Often times the data can be sensitive and existing in some environments may be a security issue for some users. It allows loading into `$_ENV` while not `$_SERVER` or system environment unless so desired. 

## Requirements

This plugin was designed for PHP 8.2 or above.

## Installation

```
composer require Ocolin\EasyEnv
```


## Environment variables and the prefix

What makes this tool useful is the prefix parameter. Rather than specifying each variable to create a database handler, you provide a prefix name. The prefix is the beginning of the environment variable names used for your database connection. 

### Example:

Prefix of "MYDB"

This will load the following environment variables for your database handler:












## Basic Usage

By default, the only thing you need is the path of your env file or an array of file paths if there are multiple env files to load.

### Single file

```php

use Ocolin\EasyEnv\Env;

Env::load( '/path/to/.env' );

```

### Multiple files

Files are loaded in order, with later files taking priority over earlier ones on conflicting keys.

```php
use Ocolin\EasyEnv\Env;

Env::load( files: [
    '/path/to/.env.local',
    '/path/for/.env',
    '/path/directory/' // Will look for '.env' file
]);
```

## Optional Reference

Here are some of the optional arguments that can be used for finer control:

|Argument| Type          | Description                         |Default|
|--------|---------------|-------------------------------------|-------|
|files| string\|array | Path to file or array of files      |N/a|
|append| boolean       | Does not overwrite existing variables | false|
|silent| boolean       | Does not throw errors loading files | false|
|system| boolean       | Loads into system environment| true|
|server| boolean       |Loads into $_SERVER global| false|

## Advanced Usage

### Options

```php
use Ocolin\EasyEnv\Env;
Env::load(
    files: '/path/.env',
    append: true,
    silent: true,
    system: false,
    server: true
)
```
### Parse

You can also parse a file into an array of values, which can be handy if you want to load them into something other than an environment. 

```php
use Ocolin\EasyEnv\Env;

$array = Env::parse( file: '/path/file' );
```


## Contributing/License

MIT — free to use, modify, and distribute. See [LICENSE](LICENSE) for details.
