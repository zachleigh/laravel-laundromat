<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Cleaner;

class BrokenCleanUser extends Cleaner
{
    /**
     * Properties allowed on the clean object.
     *
     * @var array
     */
    protected $allowed = [
        'invalidRelationship.property'
    ];

    /**
     * Methods will run and snake case property set on object.
     *
     * @var array
     */
    protected $methods = [

    ];
}
