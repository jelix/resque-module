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

    public function testJobAdditionWithSQLHistory()
    {
        self::initJelixConfig();

        $profile = \jProfiles::get('resque', 'default');
        $resqueConfig = new ResqueConfig($profile);
        $resqueConfig->prepareResqueBackend();

        $jobId = \Jelix\Resque\Client\Jobs::enqueue('resquetests', 'Addition', [5, 3]);

        $done = false;
        while (!$done) {
            usleep(500);
            $status = new Resque_Job_Status($jobId);
            $jobStatus = $status->get();
            $done = ($jobStatus == Resque_Job_Status::STATUS_FAILED || $jobStatus == Resque_Job_Status::STATUS_COMPLETE);
        }

        $this->assertEquals(Resque_Job_Status::STATUS_COMPLETE,  $status->get());
        $this->assertEquals(8, $status->result());

        $job = \Jelix\Resque\Client\Jobs::getJob($jobId);
        $this->assertNotNull($job);
        $this->assertEquals($jobId, $job->job_id);
        $this->assertEquals('Addition', $job->class);
        $this->assertEquals('resquetests', $job->queue);
        $this->assertEquals([5, 3], $job->args);
        $this->assertEquals(\Jelix\Resque\Worker\JobStatusManager::STATUS_COMPLETE, $job->status);
        $this->assertNotEquals('', $job->enqueue_date);
        $this->assertNotEquals('', $job->start_perform_date);
        $this->assertNotEquals('', $job->end_perform_date);
        $this->assertEquals('', $job->failure_message);
        $this->assertEquals([], $job->metadata);
        $this->assertEquals(0, $job->progress_count);
        $this->assertEquals('', $job->progress_message);
        $this->assertEquals('', $job->progress_date);

        \Jelix\Resque\Client\Jobs::deleteJobHistory($jobId);
        $job = \Jelix\Resque\Client\Jobs::getJob($jobId);
        $this->assertNull($job);
    }

    public function testJobProgression()
    {
        self::initJelixConfig();

        $profile = \jProfiles::get('resque', 'default');
        $resqueConfig = new ResqueConfig($profile);
        $resqueConfig->prepareResqueBackend();

        $jobId = \Jelix\Resque\Client\Jobs::enqueue('resquetests', 'LongJob');

        $done = false;
        while (!$done) {
            usleep(450);
            $status = new Resque_Job_Status($jobId);
            $jobStatus = $status->get();
            $done = ($jobStatus == Resque_Job_Status::STATUS_FAILED || $jobStatus == Resque_Job_Status::STATUS_COMPLETE);
        }

        $this->assertEquals(Resque_Job_Status::STATUS_COMPLETE,  $status->get());
        $this->assertEquals(
            array(
                array(
                    'status' => 2,
                    'failure_message' => '',
                    'progress_count' => 1,
                    'progress_message' => 'first step'
                ),
                array(
                    'status' => 2,
                    'failure_message' => '',
                    'progress_count' => 2,
                    'progress_message' => 'second step'
                ),
                array(
                    'status' => 2,
                    'failure_message' => '',
                    'progress_count' => 3,
                    'progress_message' => 'third step'
                ),
                array(
                    'status' => 2,
                    'failure_message' => '',
                    'progress_count' => 4,
                    'progress_message' => 'final step'
                ),
                // because this list is returned by the job before the "afterPerform" event, we
                // don't have the item with the last status (4)
            )
            , $status->result());

        $job = \Jelix\Resque\Client\Jobs::getJob($jobId);
        $this->assertNotNull($job);
        $this->assertEquals($jobId, $job->job_id);
        $this->assertEquals('LongJob', $job->class);
        $this->assertEquals('resquetests', $job->queue);
        $this->assertEquals([], $job->args);
        $this->assertEquals(\Jelix\Resque\Worker\JobStatusManager::STATUS_COMPLETE, $job->status);
        $this->assertNotEquals('', $job->enqueue_date);
        $this->assertNotEquals('', $job->start_perform_date);
        $this->assertNotEquals('', $job->end_perform_date);
        $this->assertEquals('', $job->failure_message);
        $this->assertEquals([], $job->metadata);
        $this->assertEquals(4, $job->progress_count);
        $this->assertEquals('final step', $job->progress_message);
        $this->assertNotEquals('', $job->progress_date);

        \Jelix\Resque\Client\Jobs::deleteJobHistory($jobId);
        $job = \Jelix\Resque\Client\Jobs::getJob($jobId);
        $this->assertNull($job);

    }
}
