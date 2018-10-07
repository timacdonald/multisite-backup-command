# Multisite Backup Command for spatie/laravel-backup

[![Latest Stable Version](https://poser.pugx.org/timacdonald/multisite-backup-command/v/stable)](https://packagist.org/packages/timacdonald/multisite-backup-command) [![Total Downloads](https://poser.pugx.org/timacdonald/multisite-backup-command/downloads)](https://packagist.org/packages/timacdonald/multisite-backup-command) [![License](https://poser.pugx.org/timacdonald/multisite-backup-command/license)](https://packagist.org/packages/timacdonald/multisite-backup-command)

This command wraps the `spatie/laravel-backup` backup command to make it super simple to backup multiple sites on a single server - from the one Laravel installation. You can read more about is [over on my blog](https://timacdonald.me/backup-multiple-sites-frameworks-laravel-backup/).

## Installation

You should already have `spatie/laravel-backup` installed and configured.

You can install using [composer](https://getcomposer.org/) from [Packagist](https://packagist.org/packages/timacdonald/multisite-backup-command).

```
$ composer require timacdonald/multisite-backup-command
```

## Usage

Extend the class and add the command to your app as per usual. Then add you sites the `$sites` variable:

```php
<?php

namespace App\Console\Commands;

use TiMacDonald\MultisiteBackupCommand\BackupCommand as BaseBackupCommand;

class BackupCommand extends BaseBackupCommand
{
    protected $sites = [
        [
            'name' => 'My Website',
            'databases' => [
                'mysql' => 'timacdonald_mysql_db',
                'pgsql' => 'timacdonald_pgsql_db',
            ],
            'include' => ['timacdonald.me/storage/app'],
        ],
        [
            'name' => 'A Wordpress Website',
            'databases' => ['mysql' => 'my_wp_db'],
            'include' => ['wordpress.site.com.au/wp-content/uploads'],
        ],
    ];
}

```

And then you will have to following commands available:

```bash
# Backup all sites
php artisan app:backup --run

# Clean all sites
php artisan app:backup --clean

# List all sites
php artisan app:backup --list

# Monitor all sites
php artisan app:backup --monitor
```

## v1 to v2 upgrade guide

The structure of the `$sites` array has changed. Please update the array and you should be good to go.

Previously:

```php
protected $sites = [
    [
        'domain' => 'timacdonald.me',
        'database' => 'timacdonald_mysql_db',
        'paths' => ['storage/app'],
    ],
];
```

Update each site to the following structure:

```php
protected $sites = [
    [
        'name' => 'https://timacdonald.me',
        'databases' => ['mysql' => 'timacdonald_mysql_db'],
        'include' => ['timacdonald.me/storage/app'],
    ],
];
```

And your done!

## Thanksware

You are free to use this package, but I ask that you reach out to someone (not me) who has previously, or is currently, maintaining or contributing to an open source library you are using in your project and thank them for their work. Consider your entire tech stack: packages, frameworks, languages, databases, operating systems, frontend, backend, etc.