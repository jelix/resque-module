<?php
/**
 * @author   Laurent Jouanneau
 * @copyright 2022 Laurent Jouanneau
 * @link     http://jelix.org
 * @licence MIT
 */

class testListenerListener extends jEventListener
{
    public function onResqueWorkerRegisterJobClasses($event)
    {
        /** @var  \Jelix\Resque\Worker\ResqueFactory $factory */
        $factory = $event->factory;
        $factory->addAlias('Addition', '\Jelix\ResqueTest\Jobs\Addition');
        $factory->addAlias('LongJob', '\Jelix\ResqueTest\Jobs\LongJob');
    }

    public function onResqueWorkerJobStatus($event)
    {
        /** @var Jelix\Resque\Job $job */
        $job = $event->job;
        if ($job->class != 'LongJob') {
            return;
        }

        \Jelix\ResqueTest\Jobs\LongJob::$history[] =
            array(
                'status' => $job->status,
                'failure_message' => $job->failure_message,
                'progress_count' => $job->progress_count,
                'progress_message' => $job->progress_message
            );

    }

    public function onResqueWorkerJobProgress($event)
    {
        $this->onResqueWorkerJobStatus($event);
    }
}