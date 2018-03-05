<?php

namespace Tests\MultisiteBackupCommand;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [\Spatie\Backup\BackupServiceProvider::class];
    }
}
