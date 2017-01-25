<?php

namespace LaravelLaundromat\tests\testdata;

use LaravelLaundromat\Cleaner;

class CleanFamily extends Cleaner
{
    /**
     * Properties allowed on the clean object.
     *
     * @var array
     */
    protected $allowed = [
        'state',
        'users.username',
        'users.birthday',
    ];

    /**
     * Methods will run and snake case property set on object.
     *
     * @var array
     */
    protected $methods = [
        'users.readableBirthday',
    ];
}
