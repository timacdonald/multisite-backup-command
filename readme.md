# Multisite Backup Command for spatie/laravel-backup

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
            'domain' => 'timacdonald.com.au',
            'database' => 'timacdonald',
            'paths' => ['storage/app'],
        ],
        [
            'domain' => 'my.wordpress.site.com.au',
            'database' => 'my_wp_db',
            'paths' => ['wp-content/uploads'],
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