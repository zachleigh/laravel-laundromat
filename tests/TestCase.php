<?php

namespace LaravelLaundromat\tests;

use Hash;
use Illuminate\Contracts\Console\Kernel;
use LaravelLaundromat\tests\testdata\User;
use LaravelLaundromat\tests\testdata\Family;
use LaravelLaundromat\tests\testdata\Business;
use LaravelLaundromat\LaundromatServiceProvider;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use LaravelLaundromat\tests\testdata\migrations\CreateUsersTable;
use LaravelLaundromat\tests\testdata\migrations\CreateFamilyTable;
use LaravelLaundromat\tests\testdata\migrations\CreateBusinessTable;

abstract class TestCase extends IlluminateTestCase
{
    /**
     * Testing property bag register.
     *
     * @var Collection
     */
    protected $registered;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(LaundromatServiceProvider::class);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Setup DB and test variables before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');

        $this->app['config']->set(
            'database.connections.sqlite.database',
            ':memory:'
        );

        $this->migrate();

        $this->user = $this->createUser();
    }

    /**
     * Run the migrations for testing.
     */
    protected function migrate()
    {
        (new CreateFamilyTable())->up();
        (new CreateBusinessTable())->up();
        (new CreateUsersTable())->up();
    }

    /**
     * Create a new user.
     *
     * @return User
     */
    protected function createUser()
    {
        $family = $this->createFamily();

        $this->createBusiness();

        $this->createSibling($family);

        return User::create([
            'username'       => 'bettylou',
            'email'          => 'bettylou@example.com',
            'ssn'            => '123-45-6789',
            'password'       => Hash::make('randomstring'),
            'favorite_color' => 'pink',
            'birthday'       => '1985-07-15',
            'family_id'      => $family->id,
        ]);
    }

    /**
     * Create a new family.
     *
     * @return Family
     */
    protected function createFamily()
    {
        return $this->family = Family::create([
            'last_name' => 'McGraw',
            'location'  => 'Uncanny Creek',
            'state'     => 'Kentucky',
        ]);
    }

    /**
     * Create a family business.
     *
     * @return Business
     */
    protected function createBusiness()
    {
        return $this->business = Business::create([
            'name'         => 'Uncanny Creek Banjo and Moonshine Emporium',
            'address'      => '123 S. River Road',
            'phone_number' => '12345678',
            'family_id'    => $this->family->id,
        ]);
    }

    protected function createSibling($family)
    {
        return User::create([
            'username'       => 'littlejohny',
            'email'          => 'littlejohny@example.com',
            'ssn'            => '234-56-7890',
            'password'       => Hash::make('randomstring'),
            'favorite_color' => 'black',
            'birthday'       => '1987-10-25',
            'family_id'      => $family->id,
        ]);
    }
}
