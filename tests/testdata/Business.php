<?php

namespace LaravelLaundromat\tests\testdata;

use Illuminate\Database\Eloquent\Model;
use LaravelLaundromat\tests\testdata\CleanUser;

class Business extends Model
{
    protected $table = 'businesses';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'family_id'
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function contactString()
    {
        return $this->address.', '.$this->phone_number;
    }

    public function addressString()
    {
        return $this->name.', Located at: '.$this->address;
    }
}
