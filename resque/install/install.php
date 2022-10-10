<?php

class resqueModuleInstaller extends \Jelix\Installer\Module\Installer
{
    public function install(Jelix\Installer\Module\API\InstallHelpers $helpers)
    {
        $helpers->database()->useDbProfile('resque');
        $helpers->database()->createTableFromDao('resque~resque_jobs');
    }
}
