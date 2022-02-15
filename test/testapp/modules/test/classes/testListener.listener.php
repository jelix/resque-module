<?php
/**
 * @author   Laurent Jouanneau
 * @copyright 2022 Laurent Jouanneau
 * @link     http://jelix.org
 * @licence MIT
 */

class testListenerListener extends jEventListener
{
    public function onResqueRegisterJobClasses($event)
    {
        /** @var \Jelix\Resque\ResqueFactory */
        $factory = $event->factory;
        $factory->addAlias('Addition', '\Jelix\ResqueTest\Jobs\Addition');
    }
}