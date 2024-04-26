# About

This is a super basic environment variable loader. It's meant for very basic cases where loading environmont variables from a file are needed.

It also does not load any content into $_SERVER like many do.

This was not really written for public use so it may lack special case options. However feel free to make any suggestions. If they don't add bloat (which can be handled by the many more robust libraries out there ) and doesn't hinder the private use cases they can be implemented.

## Static Usage
    
    EasyEnv::loadEnv(
        path: __DIR__ . '/.env';
        silent: true,
        append: true
    );

### path

The path to your .env file containing your variables

### silent

Fail silently instead of reporting errors if there is a problem with the contents of the environments file

### append

Do not load environment variables if they already exist.

## Dynamic Usage

    $easyenv = new EasyEnv(
        path: __DIR__ . '/.env';
        silent: true,
        append: true
    );
    
    $easyenv->load();