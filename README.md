This is a module for Jelix, providing a worker to do async tasks, using
php-resque..

This module is for Jelix 1.7.x and higher.


Installation
============

Install it by hands like any other Jelix modules, or use Composer if you installed
Jelix 1.7+ with Composer.

In your project:

```
composer require "jelix/resque-module"
```

Launch the configurator for your application to enable the module

```bash
php yourapp/dev.php module:configure resque
```

And then launch the installer to activate the module

```bash
php yourapp/install/installer.php
```

Usage
=====

* [Implementing jobs](doc/implementing_jobs.md) which will execute asynchronous tasks.
* [configuring and launching workers](doc/worker.md) which will launch jobs
* [launching and managing jobs](doc/launching_jobs.md) from the client code.

