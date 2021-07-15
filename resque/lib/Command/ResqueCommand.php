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

use Jelix\Resque\ResqueConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResqueCommand extends \Jelix\Scripts\ModuleCommandAbstract
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $profile = \jProfiles::get('resque', 'default');

        $config = new ResqueConfig($profile);
        $config->prepareEnvironment();

        // FIXME load tasks classes

        $a = new \ReflectionClass('Resque');
        $resqueBin = dirname($a->getFileName()).'/../bin/resque';
        include($resqueBin);
    }
}
