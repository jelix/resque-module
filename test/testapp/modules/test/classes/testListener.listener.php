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
    }
}