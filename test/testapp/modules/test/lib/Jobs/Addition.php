<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2022 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\ResqueTest\Jobs;


use Jelix\Resque\JobInterface;

class Addition implements JobInterface
{
    protected $firstOperand = 0;
    protected $secondOperand = 0;
    public function __construct($args, $queue)
    {

        if (!is_array($args) || count($args) < 2) {
            throw new \InvalidArgumentException("operands are missing");
        }

        $this->firstOperand = $args[0];
        $this->secondOperand = $args[1];
    }

    public function perform()
    {
        return $this->firstOperand + $this->secondOperand;
    }
}