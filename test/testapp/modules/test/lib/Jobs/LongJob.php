<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\ResqueTest\Jobs;


use Jelix\Resque\Worker\AbstractJob;
use Jelix\Resque\Worker\JobStatusManager;

class LongJob extends AbstractJob
{

    static public $history = array();

    public function perform()
    {
        self::$history = array();
        usleep(200);
        JobStatusManager::updateStatusToProgress($this->getJobId(), '1', 'first step');
        usleep(200);
        JobStatusManager::updateStatusToProgress($this->getJobId(), '2', 'second step');
        usleep(200);
        JobStatusManager::updateStatusToProgress($this->getJobId(), '3', 'third step');
        usleep(200);
        JobStatusManager::updateStatusToProgress($this->getJobId(), '4', 'final step');
        return self::$history;
    }
}