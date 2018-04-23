<?php

namespace TiMacDonald\MultisiteBackupCommand;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Config\Repository;

class BackupCommand extends Command
{
    protected $config;

    protected $sites = [];

    public function __construct(Repository $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    protected $signature = 'app:backup {--clean} {--list} {--monitor} {--run}';

    protected $description = 'Backup all the sites';

    public function handle()
    {
        $this->isSingleSiteCommand() ? $this->runSingleSiteCommand() : $this->runMultiSiteCommand();
    }

    protected function isSingleSiteCommand()
    {
        return in_array($this->backupType(), ['clean', 'run']);
    }

    protected function runSingleSiteCommand()
    {
        foreach ($this->sites() as $site) {
            $this->setupSingleSiteConfig($site);
            $this->callBackupCommand();
        }
    }

    protected function runMultiSiteCommand()
    {
        $this->setupMultiSiteConfig();
        $this->callBackupCommand();
    }

    protected function setupSingleSiteConfig($site)
    {
        $this->setNameConfig($site);

        $this->setDatabaseConfig($site);

        $this->setIncludeConfig($site);
    }

    protected function setNameConfig($site)
    {
        $this->config->set('backup.backup.name', $site['name']);
    }

    protected function setDatabaseConfig($site)
    {
        foreach ($site['databases'] ?? [] as $connection => $name) {
            $this->config->set("database.connections.{$connection}.database", $name);
        }
    }

    protected function setIncludeConfig($site)
    {
        $this->config->set('backup.backup.source.files.include', $this->siteIncludes($site));
    }

    protected function setupMultiSiteConfig()
    {
        $this->config->set('backup.monitorBackups', $this->configForMultiSite());
    }

    protected function configForMultiSite()
    {
        return array_map(function ($site) {
            return array_merge($this->multiSiteConfigTemplate(), Arr::only($site, 'name'));
        }, $this->sites());
    }

    protected function siteIncludes($site)
    {
        return array_map(function ($include) use ($site) {
            return base_path("../$include");
        }, $site['include'] ?? []);
    }

    protected function callBackupCommand()
    {
        $this->call('backup:'.$this->backupType());
    }

    protected function backupType()
    {
        return Collection::make($this->options())
            ->only(['run', 'clean', 'list', 'monitor'])
            ->filter()
            ->keys()
            ->first() ?? 'run';
    }

    protected function sites()
    {
        return $this->sites;
    }

    protected function multiSiteConfigTemplate()
    {
        return $this->config->get('backup.monitorBackups.0');
    }
}
