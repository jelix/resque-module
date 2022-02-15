<?php

use Jelix\Resque\ResqueConfig;

/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

class jobsTest extends \Jelix\UnitTests\UnitTestCase {

    public function testJobAddition()
    {
        self::initJelixConfig();
        $profile = \jProfiles::get('resque', 'default');
        $resqueConfig = new ResqueConfig($profile);
        $resqueConfig->prepareResqueBackend();

        $token = Resque::enqueue('resquetests', 'Addition', [5, 3], true);

        $done = false;
        while (!$done) {
            usleep(500);
            $status = new Resque_Job_Status($token);
            $jobStatus = $status->get();
            $done = ($jobStatus == Resque_Job_Status::STATUS_FAILED || $jobStatus == Resque_Job_Status::STATUS_COMPLETE);
        }

        $this->assertEquals(Resque_Job_Status::STATUS_COMPLETE,  $status->get());
        $this->assertEquals(8, $status->result());
    }
}
