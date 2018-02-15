# laravel-activity-mornitor
Activity Monitor Log is an activity logger for monitoring user activity and eloquent models events activity.


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

### Logger
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
use AMLog;

$post = Post::where('user_id', $id)->firstOrFail();
$post->body = 'update body content';
$post->save();

AMLog::logName('custom log name')              // Declare log name
    ->description('user updated post content')  // Log description 
    ->happenTo($post)                           // Model of the event happen to
    ->actBy(\Auth::user())                      // Model that cause this event
    ->meta(['key'=>'value'])                     // Additional pieces of information
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

**Note: It will not log attribute that use database default value... Except you add value to the attribute**

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
```

## View Logs

We can use `AMView` to get our logs it will return as ActivityMonitor

See this example below

```php
use AMView;

// Get all
AMView::all();      // get all the logs
AMView::get();      // also act the same as all()

// With conditions
AMView::logName('your_log_name')        // get by log name
    ->limit(5)                          // limit resutls
    ->sortBy('desc')                    // sort By desc or asc
    ->get();
    
// Get from multiple log names
AMView::inLogName('info', 'updated')->get();
AMView::inLogName(['info', 'updated'])->get();    // use array or multiple variables

// Or call from providings log name function
AMView::debug()->all();

AMView::error()->all();

AMView::fatal()->all();

AMView::info()->all();

AMView::warning()->all();
```

try and see it return collection of `ActivityMonitor` that you can access 

```
use AMView;

$am = AMView::info()->all()->last();

$am->log_name;                  // Get log name
$am->description;               // Get description
$am->agent;                     // Get user browser agent
$am->ip;                        // Get user ip address

$traces = $am->traces;          // Get traces
// if $traces not null then you can access its
foreach ($traces as $key => $value) {
    // do something
}

$meta = $am->meta;                      // Get you custom meta data
// if $meta not null then you can access its
foreach ($meta as $key => $value) {
    // do something
}
```


