<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

require_once(__DIR__.'/../application.init.php');

ini_set('date.timezone', 'Europe/Paris');
date_default_timezone_set('Europe/Paris');

jApp::setEnv('tests');
if (file_exists(jApp::tempPath())) {
    jAppManager::clearTemp(jApp::tempPath());
} else {
    jFile::createDir(jApp::tempPath(), intval("775",8));
}

