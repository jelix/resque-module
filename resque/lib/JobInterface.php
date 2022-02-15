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


interface JobInterface extends \Resque_JobInterface
{
    /**
     * @param array $args
     * @param string $queue the resque queue
     */
    public function __construct($args, $queue);

}