<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\Resque\Client;

use Jelix\Resque\Job;
use Jelix\Resque\Worker\JobStatusManager;

class Jobs
{

    /**
     * Enqueue a job that will be tracked and its status stored permanently
     * @param string $queue
     * @param string $class
     * @param array $args
     * @param array $metadata
     * @param string $prefix
     * @return integer|false
     */
    static public function enqueue($queue,
                            $class,
                            $args = array(),
                            $metadata = array(),
                            $prefix = ""
    )
    {
        $jobId = \Resque::enqueue($queue, $class, $args, true, $prefix);
        if (!$jobId) {
            return false;
        }

        $dao = \jDao::get('resque~resque_jobs');
        $rec = $dao->createRecord();
        $rec->job_id = $jobId;
        $rec->class = $class;
        $rec->queue = $queue;
        $rec->args = json_encode($args);
        $rec->status = JobStatusManager::STATUS_WAITING;
        $rec->enqueue_date = date('Y-m-d H:i:s');
        $rec->delayed_date = null;
        $rec->start_perform_date = null;
        $rec->failure_message = '';
        $rec->metadata = json_encode($metadata);
        $rec->progress_count = 0;
        $rec->progress_message = '';
        $rec->progress_date = null;

        $dao->insert($rec);
        return $jobId;
    }

    /**
     * @param $jobId
     * @return Job|null
     * @throws \jException
     */
    static public function getJob($jobId)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $jobRec = $dao->get($jobId);
        if (!$jobRec) {
            return null;
        }

        return new Job($jobRec);
    }

    /**
     * @param $jobId
     * @return null
     * @throws \jException
     */
    static public function getStatus($jobId)
    {
        $jobRec = self::getJob($jobId);
        if (!$jobRec) {
            return null;
        }

        return $jobRec->job_id;
    }

    static public function deleteJobHistory($jobId)
    {
        $dao = \jDao::get('resque~resque_jobs');
        $dao->delete($jobId);
    }

}
