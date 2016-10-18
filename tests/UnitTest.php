<?php

namespace LaravelLaundromat\tests;

use LaravelLaundromat\tests\testdata\BrokenCleanUser;
use LaravelLaundromat\tests\testdata\StrictCleanUser;

class UnitTest extends TestCase
{
    /**
     * @test
     */
    public function it_only_puts_allowed_properties_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('username', $clean);

        $this->assertEquals($clean->username, $this->user->username);

        $this->assertObjectHasAttribute('favorite_color', $clean);

        $this->assertEquals($clean->favorite_color, $this->user->favorite_color);

        $this->assertObjectNotHasAttribute('id', $clean);

        $this->assertObjectNotHasAttribute('email', $clean);

        $this->assertObjectNotHasAttribute('ssn', $clean);

        $this->assertObjectNotHasAttribute('password', $clean);
    }

    /**
     * @test
     */
    public function it_puts_snake_cased_method_properties_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('favorite_color_string', $clean);

        $this->assertObjectHasAttribute('readable_birthday', $clean);

        $this->assertEquals(
            'My favorite color is '.$this->user->favorite_color,
            $clean->favorite_color_string
        );

        $this->assertEquals(
            $this->user->birthday->toFormattedDateString(),
            $clean->readable_birthday
        );

        $this->assertObjectNotHasAttribute('last_name', $clean);
    }

    /**
     * @test
     */
    public function it_puts_allowed_properties_from_relationships_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('family', $clean);

        $this->assertObjectHasAttribute('last_name', $clean->family);

        $this->assertEquals($this->user->family->last_name, $clean->family->last_name);

        $this->assertObjectNotHasAttribute('location', $clean->family);
    }

    /**
     * @test
     */
    public function it_puts_snake_cased_method_properties_from_relationships_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('place_string', $clean->family);

        $this->assertEquals(
            $this->family->location.', '.$this->family->state,
            $clean->family->place_string
        );

        $this->assertObjectNotHasAttribute('business_name', $clean->family);
    }

    /**
     * @test
     */
    public function it_puts_allowed_properties_from_multidimensional_relationships_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('business', $clean->family);

        $this->assertObjectHasAttribute('name', $clean->family->business);

        $this->assertEquals($this->user->family->business->name, $clean->family->business->name);

        $this->assertObjectNotHasAttribute('phone_number', $clean->family->business);
    }

    /**
     * @test
     */
    public function it_puts_snake_cased_method_properties_from_multidimensional_relationships_on_clean_object()
    {
        $clean = $this->user->clean();

        $this->assertObjectHasAttribute('contact_string', $clean->family->business);

        $this->assertEquals(
            $this->family->business->address.', '.$this->family->business->phone_number,
            $clean->family->business->contact_string
        );

        $this->assertObjectNotHasAttribute('address_string', $clean->family->business);
    }

    /**
     * @test
     */
    public function it_puts_allowed_properties_from_relationship_collections_on_clean_object()
    {
        $clean = $this->family->clean();

        $user1 = $this->family->users[0];

        $user2 = $this->family->users[1];

        $this->assertObjectHasAttribute('users', $clean);

        $this->assertCount(count($this->family->users), $clean->users);

        $this->assertObjectHasAttribute('username', $clean->users[0]);

        $this->assertObjectNotHasAttribute('ssn', $clean->users[0]);

        $this->assertEquals($user1->username, $this->family->users[0]->username);

        $this->assertObjectHasAttribute('username', $clean->users[1]);

        $this->assertObjectNotHasAttribute('ssn', $clean->users[1]);

        $this->assertEquals($user2->username, $this->family->users[1]->username);
    }

    /**
     * @test
     */
    public function it_puts_snake_cased_method_properties_from_relationship_collections_on_clean_object()
    {
        $clean = $this->family->clean();

        $user1 = $this->family->users[0];

        $user2 = $this->family->users[1];

        $this->assertObjectHasAttribute('users', $clean);

        $this->assertCount(count($this->family->users), $clean->users);

        $this->assertObjectHasAttribute('readable_birthday', $clean->users[0]);

        $this->assertObjectNotHasAttribute('lastname', $clean->users[0]);

        $this->assertEquals($user1->readableBirthday(), $clean->users[0]->readable_birthday);

        $this->assertObjectHasAttribute('readable_birthday', $clean->users[1]);

        $this->assertObjectNotHasAttribute('lastname', $clean->users[1]);

        $this->assertEquals($user2->readableBirthday(), $clean->users[1]->readable_birthday);
    }

    /**
     * @test
     */
    public function clean_method_takes_cleaner_name_as_argument()
    {
        $clean = $this->user->clean(StrictCleanUser::class);

        $this->assertObjectHasAttribute('username', $clean);

        $this->assertEquals($clean->username, $this->user->username);

        $this->assertObjectHasAttribute('name', $clean->family->business);

        $this->assertEquals($this->user->family->business->name, $clean->family->business->name);

        $this->assertObjectHasAttribute('contact_string', $clean->family->business);

        $this->assertEquals(
            $this->family->business->address.', '.$this->family->business->phone_number,
            $clean->family->business->contact_string
        );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Class App\Cleaners\BadCleanerName does not exist. Create the class or use a full namespace.
     */
    public function invalid_cleaner_name_throws_exception()
    {
        $this->user->clean('BadCleanerName');
    }

    /**
     * @test
     */
    public function invalid_property_and_method_names_are_stored_as_null_on_clean_object()
    {
        $clean = $this->user->clean(StrictCleanUser::class);

        $this->assertObjectHasAttribute('invalid_property', $clean);

        $this->assertEquals($clean->invalid_property, null);

        $this->assertObjectHasAttribute('invalid_method', $clean);

        $this->assertEquals($clean->invalid_method, null);
    }

    /**
     * @test
     */
    public function collection_macro_works()
    {
        $cleanUsers = $this->family->users->clean();

        $cleanUser1 = $this->user->clean();

        $this->assertCount(2, $cleanUsers);

        $this->assertTrue(in_array($cleanUser1, $cleanUsers->all()));
    }

    /**
     * @test
     */
    public function collection_macro_takes_cleaner_as_argument()
    {
        $cleanUsers = $this->family->users->clean(StrictCleanUser::class);

        $cleanUser1 = $this->user->clean(StrictCleanUser::class);

        $this->assertCount(2, $cleanUsers);

        $this->assertTrue(in_array($cleanUser1, $cleanUsers->all()));
    }

    /**
     * @test
     */
    public function properties_can_be_called_with_methods()
    {
        $clean = $this->family->clean();

        $withMethod = $clean->users[0]->readableBirthday();

        $withProperty = $clean->users[0]->readable_birthday;

        $this->assertEquals($withProperty, $withMethod);
    }

    /**
     * @test
     */
    public function invalid_relationships_are_stored_as_null()
    {
        $clean = $this->user->clean(BrokenCleanUser::class);

        $this->assertObjectHasAttribute('invalidRelationship', $clean);

        $this->assertEquals($clean->invalidRelationship, null);
    }
}
