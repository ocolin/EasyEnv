# About

This is a simple library for loading environment variables from a file. You provide the path to the environment files, and it will load them into memory.

# Arguments

## $files

### REQUIRED

This can either be a string for an individual file, or it can be an array of paths to multiple environment files if you want to keep the variables in separate files. If you use an array, the elements must be strings.

## $append

- Default: false
- Type: bool

When set to true, any existing environment variable already set and with the same name as one being loaded, will not be overwritten. This way if you have multiple variables with the same name, they will not be overridden. If you load multiple environment files, the first instance will be used and any identical variable keys will not be updated.

If set to false and multiple files with identical variable names are loaded, the last one to load will be used. 

## $silent

- Default: false
- Type: bool

If set to true, any errors will fail silently and the variables will not be loaded.

If set to false, any problems encountered will throw an error and stop.

## $system

- Default: true
- Type: bool

If set to true, variables will be loaded into system environment as well as PHP environment. In case one does not want them in both spaces.

# Usage

### Single File Basic

```php
new \Ocolin\EasyEnv\LoadEnv(
    files: '/dir1/.env',
);
```

### Multiple Files Basic

```php
new \Ocolin\EasyEnv\LoadEnv(
    files: [ '/dir1/.env', '/dir2/.env' ],
);
```

### Advanced

```php
new \Ocolin\EasyEnv\LoadEnv(
     files: [ '/dir1/.env', '/dir2/.env' ],
    append: true,
    silent: true,
    system: false
);
```



