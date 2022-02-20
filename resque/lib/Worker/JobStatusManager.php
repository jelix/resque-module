<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\Resque\Worker;


use Jelix\Resque\Job;

class JobStatusManager
{
    const STATUS_WAITING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FAILED = 3;
    const STATUS_COMPLETE = 4;

    static protected function saveStatus($dao, $jobRec, $event = 'ResqueWorkerJobStatus')
    {
        $dao->update($jobRec);
        \jEvent::notify($event,  array(
            'job' => new Job($jobRec)
        ));
    }

    static public function updateStatusToStart(\Resque_Job $job)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $jobId = $job->payload['id'];
        $jobRec = $dao->get($jobId);
        if (!$jobRec) {
            return;
        }

        $jobRec->status = self::STATUS_RUNNING;
        $jobRec->start_perform_date = date('Y-m-d H:i:s');
        self::saveStatus($dao, $jobRec);

    }

    /**
     * Can be called by jobs, to indicate a progress
     *
     * @param string $jobId the job id. In a Resque_JobInterface object, it is saved into `$this->job->payload['id']`
     * @param int $progressCount the progress counter. Any number that is significant for the job
     * @param string $progressMessage the progress message.
     * @return void
     * @throws \jException
     */
    static public function updateStatusToProgress($jobId, $progressCount, $progressMessage)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $jobRec = $dao->get($jobId);
        if (!$jobRec) {
            return;
        }

        $jobRec->status = self::STATUS_RUNNING;
        $jobRec->progress_date = date('Y-m-d H:i:s');
        $jobRec->progress_count = $progressCount;
        $jobRec->progress_message = $progressMessage;
        self::saveStatus($dao, $jobRec, 'ResqueWorkerJobProgress');
    }

    static public function updateStatusToSuccess(\Resque_Job $job)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $jobId = $job->payload['id'];
        $jobRec = $dao->get($jobId);
        if (!$jobRec) {
            return;
        }

        $jobRec->status = self::STATUS_COMPLETE;
        $jobRec->end_perform_date = date('Y-m-d H:i:s');

        self::saveStatus($dao, $jobRec);
    }

    static public function updateStatusToFail(\Resque_Job $job, $exception)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $jobId = $job->payload['id'];
        $jobRec = $dao->get($jobId);
        if (!$jobRec) {
            return;
        }

        $jobRec->status = self::STATUS_FAILED;
        $jobRec->end_perform_date = date('Y-m-d H:i:s');
        $jobRec->failure_message = $exception->getMessage();

        self::saveStatus($dao, $jobRec);
    }

}
