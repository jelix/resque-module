Implementing Jobs
=================

Jobs are piece of code that will be executed by a Resque worker.

With Php-resque, Jobs are implemented into classes that should implement the  `Resque_JobInterface` interface
(which declares the `perform()` method).
In the Resque module, you can also implement Jobs into classes that implements the `\Jelix\Resque\Worker\JobInterface`, which inherits from
the  `Resque_JobInterface` interface. The `JobInterface` interface adds a constructor accepting parameters for the job
and the queue name.

Here is an example of a job, which add two numbers, given by the client code, and returns the result.

```php
namespace \My\Module\Jobs;

class Addition implements \Jelix\Resque\Worker\JobInterface
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
```

Declaring Jobs classes
======================

In Php-resque, when the client code wants to launch a job, it must give the class name as job name. This is not always
a convenient way, as the class name could be long (often because of its namespace).

The Resque module provides a factory for Php-resque which allows declaring aliases for Job classes. Aliases could be shorter
names than class name. So you can launch jobs using aliases instead of class names.

To declare such aliases, you must implement a jEvent listener, that will call the `addAlias` method of the factory.
The event name is `ResqueRegisterJobClasses`.

First, create a listener in your module, for example `myModuleListener`, into a file `classes/myModule.listener.php`.
It contains this class, which declares a job named `Addition`, implemented into a class `\My\Module\Jobs\Addition`.

```php
class myModuleListener extends jEventListener
{
    public function onResqueRegisterJobClasses($event)
    {
        /** @var \Jelix\Resque\Worker\ResqueFactory */
        $factory = $event->factory;
        $factory->addAlias('Addition', '\My\Module\Jobs\Addition');
    }
}
```

Then declare the listener into the `events.xml` of the module:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<events xmlns="http://jelix.org/ns/events/1.0">
    <listener name="myModule">
        <event name="ResqueRegisterJobClasses" />
    </listener>
</events>
```

When the worker will be started, the event `ResqueRegisterJobClasses` will be triggered, and then
all job classes will be declared into the worker.

You can declare classes, depending on the queue used for the worker. The queue is given on the event object.

```php
class myModuleListener extends jEventListener
{
    public function onResqueRegisterJobClasses($event)
    {
        /** @var \Jelix\Resque\Worker\ResqueFactory */
        $factory = $event->factory;
        
        if ($event->queue == 'calculus') {
            $factory->addAlias('Addition', '\My\Module\Jobs\Addition');
            $factory->addAlias('Substract', '\My\Module\Jobs\Substract');
        }
        else if ($event->queue == 'emails') {
            $factory->addAlias('send_emails', '\My\Module\Jobs\EmailSender');
        }
    }
}
```

Note: all classes you indicate to `addAlias()` should be autoloadable. Else you should use the `require` function
into your listener to load them.

