<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2021-2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\Resque\Command;

use Jelix\Resque\ResqueConfig;
use Jelix\Resque\Worker\JobStatusManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ResqueCommandAbstract extends \Jelix\Scripts\ModuleCommandAbstract
{
    protected function configure()
    {
        $this
            ->addOption(
                'profile',
                'p',
                InputOption::VALUE_REQUIRED,
                'The resque profile name to use, if it is not the default one',
                'default'
            )
        ;
        parent::configure();
    }

    /**
     * @var ResqueConfig
     */
    protected $resqueConfig;

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $profile = \jProfiles::get('resque', $input->getOption('profile'));

        $this->resqueConfig = new ResqueConfig($profile);
        $this->resqueConfig->prepareEnvironment();

        $factory = $this->getResqueFactory();

        // set our own factory to jobs
        \Resque_Event::listen('afterFork', function($job) use($factory) {
            $job->setJobFactory($factory);
        });

        \Resque_Event::listen('beforePerform', function(\Resque_Job $job)  {
            JobStatusManager::updateStatusToStart($job);
        });

        \Resque_Event::listen('afterPerform', function(\Resque_Job $job)  {
            JobStatusManager::updateStatusToSuccess($job);
        });

        \Resque_Event::listen('onFailure', function($exception, \Resque_Job $job)  {
            JobStatusManager::updateStatusToFail($job, $exception);
        });

        $a = new \ReflectionClass('Resque');
        $resqueBin = dirname($a->getFileName()).'/../bin/resque';
        include($resqueBin);
    }

    /**
     * Create an instance of a resque factory
     * @return \Resque_Job_FactoryInterface
     */
    abstract protected function getResqueFactory();
}
