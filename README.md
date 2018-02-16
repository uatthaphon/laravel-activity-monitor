# laravel-activity-mornitor
Activity Monitor Log is an activity logger for monitoring user activity and eloquent models events activity.

**Note:** This package use laravel 5.5+ it require php 7.0+

## Setup
Add package dependency to your project

```bash
composer require uatthaphon/laravel-activity-monitor
```
As the package build for laravel 5.5+, I use [Auto-Discovery](https://laravel-news.com/package-auto-discovery) 
so we don't need to add service provider in `config\app.php` anymore

Run publishing to get the database migration for table `activity_monitors`

```bash
php artisan vendor:publish --tag=migrations
```

After published, we can create table by running the migrations

```bash
php artisan migrate
```

## Usage

### Logger
This package have 2 aliases `AMLog` and `AMView` for us to easily use to save the log and view the logs.

We don't need to add those 2 aliases to `config/app.php` neither. 
It already added for us by [Auto-Discovery](https://laravel-news.com/package-auto-discovery).

Example, log the user updated their post.
```php
use AMLog;

$post = Post::where('user_id', $id)->firstOrFail();
$post->body = 'update body content';
$post->save();

AMLog::logName('custom log name')               // Declare log name
    ->description('user updated post content')  // Log description
    ->happenTo($post)                           // Model of the event happen to
    ->actBy(\Auth::user())                      // Model that cause this event
    ->meta(['key'=>'value'])                    // Additional pieces of information
    ->save();                                   // Let's Save the log
```
AMlog also prepared some of the log name for us to easily use => `debug`, `error`, `fatal`, `info`, `warning`

```php
use AMLog;
...
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

class ToBelog extends Model
{
    use ModelEventActivity;
    ...
}

```


This feature will record only changes in your application by setting `protected static $loggable` to tell the logger which attributes should be logs.

**Note: It will not log attribute that use database default value... Except you add value to the attribute by your self**

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToBelog extends Model
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

class ToBelog extends Model
{
    use ModelEventActivity;

    protected static $eventsToLog = ['created']
}
```

We can add our meta data to each event by add this to yout model

```php
...
use Uatthaphon\ActivityMonitor\Traits\ModelEventActivity;

class ToBelog extends Model
{
    use ModelEventActivity;

    protected static $createdEventMeta = ['create key' => 'create value'];

    protected static $updatedEventMeta = ['update key' => 'update value'];

    protected static $deletedEventMeta = ['deletd key' => 'delete value'];

    ...
}
```

## View Logs

We can use `AMView` to get our logs it will return as ActivityMonitor

See this example below

```php
use AMView;

...

// Get all
AMView::all();                                    // get all the logs
AMView::get();                                    // also act the same as all()

// With conditions
AMView::logName('your_log_name')                  // get by log name
    ->limit(5)                                    // limit resutls
    ->sortBy('desc')                              // sort By desc or asc
    ->get();

// Get from multiple log names
AMView::logName('info', 'updated')->get();
AMView::logName(['info', 'updated'])->get();


// Get all specific lastest post log From current user
$user = \Auth::user();
$post = $user->post()->last($user);
AMView::happenTo($post)->ActBy($user)->get();

// Or call from providings log name function
AMView::debug()->all();

AMView::error()->all();

AMView::fatal()->all();

AMView::info()->all();

AMView::warning()->all();

...
```

Try and see. It will return collection of `ActivityMonitor` model

```php
use AMView;

...

$am = AMView::info()->all()->last();

$am->log_name;                          // Get log name
$am->description;                       // Get description
$am->agent;                             // Get user browser agent
$am->ip;                                // Get user ip address

$traces = $am->traces;                  // Get traces

foreach ($traces as $key => $value) {
    // do something
}

$meta = $am->meta;                      // Get you custom meta data

foreach ($meta as $key => $value) {
    // do something
}

...
```

## View Log In Specific Model

We can add `ActivityMonitor` to our model

```php
...
use Uatthaphon\ActivityMonitor\Traits\ActivityMonitorRelations;

class User extends Authenticatable
{
  use ActivityMonitorRelations;

  ...
}
```

Now we can use `activity()` polymorphic relations

```php
// Get all activity records of the current user
\Auth::user()->activity()->get();

// Retrived with more specific
// By tell with model record user was interact with
$user = \Auth::user();
$user->activity()->happenTo($user->posts()-last())->get();

// Use the providing log name function
$user->activity()->info()->get();

// Use the providing log name with specific fom model togetter
$user->activity()
    ->info()
    ->happenTo($user->posts()-last())
    ->get();
```
