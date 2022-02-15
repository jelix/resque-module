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


class ResqueFactory implements \Resque_Job_FactoryInterface
{

    /**
     * @var array key are alias, values are real class name
     */
    protected $classAliases = array();


    /**
     * @param array $classAliases key are alias, values are real class name
     */
    public function __construct(array $classAliases = [])
    {
        $this->classAliases = $classAliases;
    }


    public function AddAlias($alias, $className)
    {
        if ($className == '') {
            unset($this->classAliases[$alias]);
        }
        else {
            $this->classAliases[$alias] = $className;
        }
    }

    /**
     * Register jobs classes given by modules, listening the event ResqueRegisterJobClasses
     *
     * Each listener should return a list, with key being an alias, and values being class names
     * @return void
     */
    public function registerModuleJobs($queue)
    {
        \jEvent::notify('ResqueRegisterJobClasses', array('factory'=>$this, 'queue'=>$queue));
    }

    /**
     * @param $className
     * @param array $args
     * @param $queue
     * @return \Resque_JobInterface
     * @throws \Resque_Exception
     */
    public function create($className, $args, $queue)
    {
        // the name is an alias
        if (isset($this->classAliases[$className])) {
            $className = $this->classAliases[$className];
        }
        // the name is a Jelix class selector
        else if (preg_match('/^[a-zA-Z0-9_\\.]+~?[a-zA-Z0-9_\\.\\/]+$/', $className)) {
            $sel = new \jSelectorClass($className);
            require_once $sel->getPath();
            $className = $sel->className;
        }

        if (!class_exists($className)) {
            throw new \Resque_Exception(
                'Could not find job class ' . $className . '.'
            );
        }

        if (is_subclass_of($className, '\Jelix\Resque\JobInterface')) {
            $instance = new $className($args, $queue);
        }
        else {
            // classic job classes for PHPResque
            $instance = new $className();
            if (!method_exists($className, 'perform')) {
                throw new \Resque_Exception(
                    'Job class ' . $className . ' does not contain a perform method.'
                );
            }
            $instance->args = $args;
            $instance->queue = $queue;
        }

        return $instance;
    }
}