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


abstract class AbstractJob implements JobInterface
{
    /**
     * Set by Php Resque
     * @var \Resque_Job
     */
    public $job;

    protected $arguments;
    protected $queueName;

    public function __construct($args, $queue)
    {
        $this->arguments = $args;
        $this->queueName = $queue;
    }

    abstract public function perform();

    public function getJobId()
    {
        return $this->job->payload['id'];
    }
}