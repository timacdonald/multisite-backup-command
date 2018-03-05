<?php

namespace TiMacDonald\MultisiteBackupCommand;

use Illuminate\Console\Command;
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
        if ($this->isSingleSiteCommand()) {
            $this->runSingleSiteCommand();
            return;
        }
        $this->runMultiSiteCommand();
    }

    protected function isSingleSiteCommand()
    {
        return in_array($this->backupType(), ['clean', 'run']);
    }

    protected function runSingleSiteCommand()
    {
        collect($this->sites())->each(function ($site) {
            $this->setupSingleSiteConfig($site);
            $this->callBackupCommand();
        });
    }

    protected function runMultiSiteCommand()
    {
        $this->setupMultiSiteConfig();
        $this->callBackupCommand();
    }

    protected function setupSingleSiteConfig($site)
    {
        $this->config->set('backup.backup.name', $this->siteName($site));

        $this->config->set('database.connections.mysql.database', $site['database']);

        $this->config->set('backup.backup.source.files.include', $this->siteIncludes($site));
    }

    protected function setupMultiSiteConfig()
    {
        $this->config->set('backup.monitorBackups', $this->configForMultiSite());
    }

    protected function configForMultiSite()
    {
        return array_map(function ($site) {
            return array_merge($this->config->get('backup.monitorBackups.0'), [
                'name' => $this->siteName($site),
            ]);
        }, $this->sites());
    }

    protected function siteName($site)
    {
        return 'https://'.$site['domain'];
    }

    protected function siteIncludes($site)
    {
        return array_map(function ($path) use ($site) {
            return base_path("../{$site['domain']}/$path");
        }, $site['paths']);
    }

    protected function callBackupCommand()
    {
        $this->call('backup:'.$this->backupType());
    }

    protected function backupType()
    {
        return collect($this->options())
            ->only(['run', 'clean', 'list', 'monitor'])
            ->filter()
            ->keys()
            ->first() ?? 'run';
    }

    protected function sites()
    {
        return $this->sites;
    }
}
