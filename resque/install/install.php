<?php

class resqueModuleInstaller extends \Jelix\Installer\Module\Installer
{
    public function install(Jelix\Installer\Module\API\InstallHelpers $helpers)
    {
        $helpers->database()->createTableFromDao('resque~resque_jobs');
    }
}
