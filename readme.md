# Multisite Backup Command for spatie/laravel-backup

[![Latest Stable Version](https://poser.pugx.org/timacdonald/multisite-backup-command/v/stable)](https://packagist.org/packages/timacdonald/multisite-backup-command) [![Total Downloads](https://poser.pugx.org/timacdonald/multisite-backup-command/downloads)](https://packagist.org/packages/timacdonald/multisite-backup-command) [![License](https://poser.pugx.org/timacdonald/multisite-backup-command/license)](https://packagist.org/packages/timacdonald/multisite-backup-command)

This command wraps the `spatie/laravel-backup` backup command to make it super simple to backup multiple sites on a single server - from the one Laravel installation. You can read more about is [over on my blog](https://timacdonald.me/backup-multiple-sites-frameworks-laravel-backup/).

## Installation

You should already have `spatie/laravel-backup` installed and configured.

You can install using [composer](https://getcomposer.org/) from [Packagist](https://packagist.org/packages/timacdonald/multisite-backup-command).

```
composer require timacdonald/multisite-backup-command
```

## Versioning

This package uses *Semantic Versioning*. You can find out more about what this is and why it matters by reading [the spec](http://semver.org) or for something more readable, check out [this post](https://laravel-news.com/building-apps-composer).

## Basic Usage

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

## Contributing

Please feel free to suggest new ideas or send through pull requests to make this better. If you'd like to discuss the project, feel free to reach out on [Twitter](https://twitter.com/timacdonald87). I just throw my ideas for the project in the [issues list](https://github.com/timacdonald/multisite-backup-command/issues) if you want to help implement anything.

## License

This package is under the MIT License. See [LICENSE](https://github.com/timacdonald/multisite-backup-command/blob/master/LICENSE) file for details.

## Thanks

Big thanks to Spatie for providing such a great backup package!

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