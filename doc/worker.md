
Configuring the worker
======================

You should create a profile into the `profiles.ini.php` of your application. Here is a default profile:

```ini
[resque]

[resque:default]
redis_host=redis
;redis_port=6379
;redis_password=
;redis_user=
redis_db=0
;redis_prefix=
worker_count=1
queue=resquetests
logging=1
verbose=2
;blocking=off
;interval=5

```

You can create different profile if you want to launch several workers for different queues or redis server.


Launching a worker
==================

You should use the `resque:server` command available via the console.php script of your Jelix application.

```bash
php console.php resque:server
```

It will launch Php-resque with a custom factory, and with parameters indicated into the default profile.
You can indicate the profile to use with the `--profile` option:

```bash
php console.php resque:server --profile=other
```

Listening jobs statuses
=======================

Some events are triggered when the status of a job change.

You can create a listener to react to these events.
Both events have a `job` property which is a `Jelix\Resque\Job` object
representing the job.

- `ResqueWorkerJobStatus`: the given job have a new status. See the property `status` of the job.
- `ResqueWorkerJobProgress`: The given job still have the `JobStatusManager::STATUS_RUNNING`
  status, but it is in progress. See the properties `progress_date`,
  `progress_count`, `progress_message` of the job.

