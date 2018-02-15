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


Run publishing get the database migration for table `activity_monitors`

```bash
php artisan vendor:publish --tag=migrations
```

After all has been published you can create table by running the migrations

```bash
php artisan migrate
```

## Usage

###  Basic log
Easy to use after you declear aliases in `config/app.php`

```php
'aliases' => [
    ...
    'AMLog' => Uatthaphon\ActivityMonitor\Facades\ActivityMonitorLog::class,
    'AMView' => Uatthaphon\ActivityMonitor\Facades\ActivityMonitorView::class,
]
```

or if you use Laravel 5.5 or above, it will automatic add this 2 aliases.

for example log the user updated their post.
```php
$post = Post::where('user_id, $id)->firstOrFail();
$post->body = 'update body content';
$post->save();

\AMLog::logName('custom log name')              // Declare log name
    ->description('user updated post content')  // Log description
    ->happenTo($post)                           // Model of the event happen to
    ->actBy(\Auth::user())                      // Model that cause this event
    ->meta(['key'=>'value])                     // Additional pieces of information
    ->save();                                   // Let's Save the log
```
AMlog also prepared some of the log name for us to easily use => `debug`, `error`, `fatal`, `info`, `warning`

```php
AMLog::debug('some debug description')->save();
AMLog::error('some error description')->save();
AMLog::fatal('some fatal description')->save();
AMLog::info('some info description')->save();
AMLog::warning('some warning description')->save();
```
That's it :notes:

###  Eloquent Models Events Log


For you to easy log your eloquent model activities when `created`, `updated`, `deleted`.

After you setting up the package then add `ModelEventActivity` Trait to your model.

```php
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

**Note: this feature will log only changed record from setting attributes in `$loggable`, and one last thing it will not log attribute that use database default value... Except you add value to the attribute**

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
```json
{"title": "has some change"}
```

We can cutomize which eloquent event should be log by `protected static $eventsToLog`.

In the example below only `created` event for this model will be logged

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToLogged extends Model
{
    use ModelEventActivity;

    protected static $eventsToLog = ['created']
}
```

We can add our meta data to each event by add this to yout model

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToLogged extends Model
{
    use ModelEventActivity;
    protected static $createdEventMeta = ['create key' => 'create value'];
    protected static $updatedEventMeta = ['update key' => 'update value'];
    protected static $deletedEventMeta = ['deletd key' => 'delete value'];
```

