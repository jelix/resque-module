<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2021 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\Resque\Command;


use Jelix\Resque\Worker\ResqueFactory;

class ResqueCommand extends ResqueCommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('resque:server')
            ->setDescription('Launch the Resque server')
            ->setHelp('')
            /*->addArgument(
                '',
                InputArgument::REQUIRED,
                ''
            )*/
        ;
        parent::configure();
    }


    protected function getResqueFactory()
    {
        // initialize our own factory to jobs
        $factory = new ResqueFactory();
        $factory->registerModuleJobs($this->resqueConfig->getQueue());
        return $factory;
    }

}
