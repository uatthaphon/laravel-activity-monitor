# laravel-activity-mornitor
Activity Monitor Log is an activity logger for monitoring user activity and eloquent models events activity.

> ** **Still In Development** *If you would like to try or love to taking a risk please use this repo now* :speak_no_evil::see_no_evil::hear_no_evil:
> *Note:*
> *[1] Right now it only tested on laravel 5.5*
> *[2] Only Eloquent model events feature tested.*

## Setup
Add package dependency to your project

```bash
composer require uatthaphon/laravel-activity-mornitor
```

Before Laravel 5.5, add package's service provider to your project's `config/app.php`

```php
'providers' => [
  ...

  Uatthaphon\ActivityMonitor\ActivityMonitorServiceProvider::class,
],
```


Run publishing get the database migration

```bash
php artisan vendor:publish --tag=migrations
```

After all has been published you can create tables by running the migrations

```bash
php artisan migrate
```

## Usage
###  Eloquent models events log
For you to easy log your eloquent model activities when `created`, `updated`, `deleted`.

After you setting up the package then add `ModelEventActivity` Trait to your model.

```php
<?php

namespace App\Models;

...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToLogged extends Model
{
    use ModelEventActivity;
    ...
}

```


This feature will record only changes in your application by setting `protected static $loggable` to tell the logger which attributes should be logs.

**Note: this feature will log only changed record from setting fields in $loggable**

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToLogged extends Model
{
    use ModelEventActivity;

    protected static $loggable = ['title', 'description']
}
```

If `title` record changed, It will only log title field in the table `activity_monitors`
```
{"title": "has some change"}
```

We can cutomize which eloquent event should be log by `protected static $eventsToLog` in the example below only `created` event for this model will be logged

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToLogged extends Model
{
    use ModelEventActivity;

    protected static $eventsToLog = ['created']
}
```
