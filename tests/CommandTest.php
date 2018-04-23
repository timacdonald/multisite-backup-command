<?php

namespace Tests\MultisiteBackupCommand;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Config\Repository;
use TiMacDonald\MultisiteBackupCommand\BackupCommand;

class CommandTest extends TestCase
{
    function test_single_site_clean()
    {
        TestBackupCommand::setSites([
            ['name' => 'timacdonald.me'],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);

        $kernal->call('app:backup', ['--clean' => true]);

        $this->assertEquals($command->called, [['command' => 'backup:clean', 'arguments' => []]]);
        $this->assertEquals($config->get('backup.backup.name'), 'timacdonald.me');
    }

    function test_mulit_site_clean()
    {
        TestBackupCommand::setSites([
            ['name' => 'timacdonald.me'],
            ['name' => 'spatie.be'],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);

        $kernal->call('app:backup', ['--clean' => true]);

        $this->assertEquals($command->called, [
            ['command' => 'backup:clean', 'arguments' => []],
            ['command' => 'backup:clean', 'arguments' => []],
        ]);
        $this->assertEquals($config->get('backup.backup.name'), 'spatie.be');
    }

    function test_can_list_multiple_sites()
    {
        TestBackupCommand::setSites([
            [
                'name' => 'timacdonald.me',
                'databases' => ['mysql' => 'timacdonald'],
                'include' => ['timacdonald.me/storage/app'],
            ],
            [
                'name' => 'spatie.be',
                'databases' => ['mysql' => 'spatie'],
                'include' => ['spatie.be/storage/app/public'],
            ],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);
        $template = $config->get('backup.monitorBackups.0');

        $kernal->call('app:backup', ['--list' => true]);

        $this->assertEquals($command->called, [['command' => 'backup:list', 'arguments' => []]]);
        $template['name'] = 'timacdonald.me';
        $this->assertEquals($config->get('backup.monitorBackups.0'), $template);
        $template['name'] = 'spatie.be';
        $this->assertEquals($config->get('backup.monitorBackups.1'), $template);
    }

    function test_can_monitor_multiple_sites()
    {
        TestBackupCommand::setSites([
            [
                'name' => 'timacdonald.me',
                'databases' => ['mysql' => 'timacdonald'],
                'include' => ['timacdonald.me/storage/app'],
            ],
            [
                'name' => 'spatie.be',
                'databases' => ['mysql' => 'spatie'],
                'include' => ['spatie.be/storage/app/public'],
            ],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);
        $template = $config->get('backup.monitorBackups.0');

        $kernal->call('app:backup', ['--monitor' => true]);

        $this->assertEquals($command->called, [['command' => 'backup:monitor', 'arguments' => []]]);
        $template['name'] = 'timacdonald.me';
        $this->assertEquals($config->get('backup.monitorBackups.0'), $template);
        $template['name'] = 'spatie.be';
        $this->assertEquals($config->get('backup.monitorBackups.1'), $template);
    }

    function test_can_run_single_site()
    {
        TestBackupCommand::setSites([
            [
                'name' => 'timacdonald.me',
                'databases' => ['mysql' => 'timacdonald'],
                'include' => ['timacdonald.me/storage/app'],
            ],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);

        $kernal->call('app:backup', ['--run' => true]);

        $this->assertEquals($command->called, [['command' => 'backup:run', 'arguments' => []]]);
        $this->assertEquals($config->get('backup.backup.name'), 'timacdonald.me');
        $this->assertEquals($config->get('database.connections.mysql.database'), 'timacdonald');
        $this->assertEquals($config->get('backup.backup.source.files.include'), [base_path('../timacdonald.me/storage/app')]);
    }

    function test_can_run_mulitple_sites()
    {
        TestBackupCommand::setSites([
            [
                'name' => 'timacdonald.me',
                'databases' => ['mysql' => 'timacdonald'],
                'include' => ['timacdonald.me/storage/app'],
            ],
            [
                'name' => 'spatie.be',
                'databases' => ['mysql' => 'spatie'],
                'include' => ['spatie.be/storage/app/public'],
            ],
        ]);
        $config = $this->app->make(Repository::class);
        $kernal = $this->app->make(Kernel::class);
        $command = $this->app->make(TestBackupCommand::class);
        $kernal->registerCommand($command);

        $kernal->call('app:backup', ['--run' => true]);

        $this->assertEquals($command->called, [['command' => 'backup:run', 'arguments' => []], ['command' => 'backup:run', 'arguments' => []]]);
        $this->assertEquals($config->get('backup.backup.name'), 'spatie.be');
        $this->assertEquals($config->get('database.connections.mysql.database'), 'spatie');
        $this->assertEquals($config->get('backup.backup.source.files.include'), [base_path('../spatie.be/storage/app/public')]);
    }
}

class TestBackupCommand extends BackupCommand
{
    public $called = [];

    protected static $testSites;

    public static function setSites($sites)
    {
        static::$testSites = $sites;
    }

    public function call($command, array $arguments = [])
    {
        $this->called[] = ['command' => $command, 'arguments' => $arguments];
    }

    protected function sites()
    {
        return static::$testSites;
    }
}
