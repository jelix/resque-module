Launching Jobs
==============


Before launching jobs, they should be [implemented and declared](implementing_jobs.md) into [a worker.
This [worker can then be launched](worker.md) and can wait after launch orders.

Here how to launch a job from a client code.

First configure the Resque client:
```php
$profile = \jProfiles::get('resque', 'default');
$resqueConfig = new ResqueConfig($profile);
$resqueConfig->prepareResqueBackend();
```

Then you can enqueue a job task, by giving the queue, the job name, its parameters:

```php
$token = Resque::enqueue('resquetests', 'Addition', [5, 3], true);
```

It returns a token that you can give to some PHP-Resque API, like `Resque_Job_Status` to know the status of the job:

```php
$status = new Resque_Job_Status($token);
$jobStatus = $status->get(); 
```

See [the PHP-Resque documentation](https://github.com/resque/php-resque/blob/v1.3.6/README.md).

