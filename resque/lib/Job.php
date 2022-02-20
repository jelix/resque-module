<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */
namespace Jelix\Resque;

/**
 * Encapsulate a job record from the resque_jobs table
 *
 * @property-read $job_id
 * @property-read $class
 * @property-read $queue
 * @property-read $args
 * @property-read $status
 * @property-read $enqueue_date
 * @property-read $delayed_date
 * @property-read $start_perform_date
 * @property-read $end_perform_date
 * @property-read $failure_message
 * @property-read $metadata
 * @property-read $progress_count
 * @property-read $progress_message
 * @property-read $progress_date
 */
class Job
{

    /**
     * @var \jDaoRecordBase
     */
    protected $jobRecord;

    /** @var null|array  */
    protected $_args = null;

    /** @var null|array  */
    protected $_metadata = null;

    /**
     * @param \jDaoRecordBase $record a record from the resque_jobs dao
     */
    function __construct(\jDaoRecordBase $record)
    {
        $this->jobRecord = $record;
    }

    function __get($name)
    {
        if ($name == 'args') {
            if ($this->_args === null) {
                $this->_args = json_decode($this->jobRecord->args, true);
            }
            return $this->_args;
        }
        if ($name == 'metadata') {
            if ($this->_metadata === null) {
                $this->_metadata = json_decode($this->jobRecord->metadata, true);
            }
            return $this->_metadata;
        }
        return $this->jobRecord->$name;
    }


}