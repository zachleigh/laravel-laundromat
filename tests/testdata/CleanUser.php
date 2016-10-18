<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Cleaner;

class CleanUser extends Cleaner
{
    /**
     * Properties allowed on the clean object.
     *
     * @var array
     */
    protected $allowed = [
        'username',
        'favorite_color',
        'family.last_name',
        'family.business.name',
    ];

    /**
     * Methods will run and snake case property set on object.
     *
     * @var array
     */
    protected $methods = [
        'favoriteColorString',
        'readableBirthday',
        'family.placeString',
        'family.business.contactString',
    ];
}
