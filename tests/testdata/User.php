<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Washable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Washable;

    protected $fillable = [
        'username',
        'email',
        'ssn',
        'password',
        'favorite_color',
        'birthday',
        'family_id'
    ];

    protected $casts = [
        'birthday' => 'date'
    ];

    protected $defaultCleaner = CleanUser::class;

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function favoriteColorString()
    {
        return 'My favorite color is '.$this->favorite_color;
    }

    public function readableBirthday()
    {
        return $this->birthday->toFormattedDateString();
    }

    public function lastName()
    {
        return $this->family->last_name;
    }
}
