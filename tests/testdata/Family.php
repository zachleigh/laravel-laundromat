<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Washable;
use Illuminate\Database\Eloquent\Model;
use LaravelLaundromat\tests\testdata\CleanFamily;

class Family extends Model
{
    use Washable;

    protected $fillable = [
        'last_name',
        'location',
        'state'
    ];

    protected $defaultCleaner = CleanFamily::class;

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function placeString()
    {
        return $this->location.', '.$this->state;
    }

    public function businessName()
    {
        return $this->business->name;
    }
}
