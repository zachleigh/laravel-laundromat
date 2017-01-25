# Laravel Laundromat  
[![Build Status](https://travis-ci.org/zachleigh/laravel-laundromat.svg?branch=master)](https://travis-ci.org/zachleigh/laravel-laundromat)
[![Latest Stable Version](https://poser.pugx.org/zachleigh/laravel-laundromat/version.svg)](//packagist.org/packages/zachleigh/laravel-laundromat)   
[![StyleCI](https://styleci.io/repos/64065434/shield?style=flat)](https://styleci.io/repos/64065434)    
[![License](https://poser.pugx.org/zachleigh/laravel-laundromat/license.svg)](//packagist.org/packages/zachleigh/laravel-laundromat)  
##### Take your objects to the cleaners before sending them clientside. 
This package gives you an easy way to filter your objects to remove sensitve data before sending them client-side.

### Contents
  - [Demo](#demo)
  - [Upgrade Information](#upgrade-information)
  - [Install](#install)
  - [Contributing](#contributing)

### Demo
Our user migration looks like this:
```php
Schema::create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->string('username')->unique();
    $table->string('email')->unique();
    $table->string('social_security_number');
    $table->date('birthday');
    $table->string('password');
    $table->integer('family_id')->unsigned();
    $table->rememberToken();
    $table->timestamps();

    $table->foreign('family_id')->references('id')->on('families');
});
```
And our User model looks like this:
```php
class User extends Model
{
    protected $casts = [
        'birthday' => 'date'
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function readableBirthday()
    {
        return $this->birthday->toFormattedDateString();
    }
}
```


We obviously don't want to send sensitive data like `social_security_number` to the front end where it would be viewable by anybody. Maybe we only want to expose `username` from the user model and then `last_name` on the related family model. Also, we want to use the value returned from the `readableBirthday()` method on the model.   

First, make a new Cleaner class. The naming convention is 'Clean' plus the name of the model:
```
php artisan laundromat:create CleanUser
```
This will give you an empty cleaner class in app/Cleaners. Simply register allowed properties in the `allowed` array and register any methods you wish to run in `methods`. Use dot syntax to indicate properties/methods on relationships.
```
class CleanUser extends Cleaner
{
    /**
     * Properties allowed on the clean object.
     *
     * @var array
     */
    protected $allowed = [
        'username',
        'family.last_name'
    ];

    /**
     * Methods to run. Returned value will be stored as a snake case property
     * on the clean object.
     *
     * @var array
     */
    protected $methods = [
        'readableBirthday'
    ];
}
```
Use the Washable trait in your User model.

```php
use LaravelLaundromat\Washable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Washable;
    
    //
}
```
Then call the `clean()` method on a User object to get a CleanUser instance.
```php
$user = User::find(1);

$clean = $user->clean();

var_dump($clean);

// App\Cleaners\CleanUser {
//  "username" => "bettylou"
//  "family" => LaravelLaundromat\EmptyCleaner {
//    "last_name" => "McGraw"
//  }
//  "readable_birthday" => "Jul 15, 1985"
//}

```
Or, use the Collection macro `clean()`:
```php
$users = User::all(); // $users is a collection

$clean = $users->clean(); // All User objects in the collection are now CleanUser objects
```
Pass `clean()` (either the normal method or the collection macro) an optional cleaner name to override default behavior.
```php
$user = User::find(1);

$clean = $user->clean('App/MyDirectory/MyCustomCleaner'); // $clean is now an instance of MyCustomCleaner
```
Or, add the property `defaultCleaner` to the model to permanently set override the conventional behavior.
```php
class User extends Model
{
    use Washable;

    protected $defaultCleaner = MyCustomCleaner::class;
    
    //
}
```

### Upgrade Information
##### From 1.1.* to 1.2.0
Version 1.2.0 adds Laravel 5.4 support. For Laravel 5.3, please use [Version 1.1.0](https://github.com/zachleigh/laravel-laundromat/tree/v1.1.0):
```
composer require zachleigh/laravel-laundromat:1.1.*
```

##### From 1.0.* to 1.1.0
Version 1.1.0 adds Laravel 5.3 support. For Laravel 5.2, please use [Version 1.0.2](https://github.com/zachleigh/laravel-laundromat/tree/v1.0.2):
```
composer require zachleigh/laravel-laundromat:1.0.*
```

### Install
Install via composer:
```
composer require zachleigh/laravel-laundromat
```
Register the service provider in config/app.php:
```
'providers' => [
    ...
    LaravelLaundromat\LaundromatServiceProvider::class,
];
```

### Contributing
Contributions are more than welcome. Fork, improve, add tests, and make a pull request.