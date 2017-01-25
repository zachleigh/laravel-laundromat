<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Cleaner;

class StrictCleanUser extends Cleaner
{
    /**
     * Properties allowed on the clean object.
     *
     * @var array
     */
    protected $allowed = [
        'username',
        'family.business.name',
        'invalid_property',
    ];

    /**
     * Methods will run and snake case property set on object.
     *
     * @var array
     */
    protected $methods = [
        'family.business.contactString',
        'invalidMethod',
    ];
}
