<?php

namespace LaravelLaundromat;

use Illuminate\Support\Collection;
use LaravelLaundromat\EmptyCleaner;

class Cleaner
{
    /**
     * Clean the dirty object.
     *
     * @param object $dirty
     *
     * @return this
     */
    public function clean($dirty)
    {
        $this->setAllowed($dirty);

        $this->setMethods($dirty);

        return $this;
    }

    /**
     * Call snake_cased property names with studlyCased methods.
     *
     * @param  string $name      [Name of method called]
     * @param  array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $name = snake_case($name);

        return $this->{$name};
    }

    /**
     * Set allowed properties on cleaner object.
     *
     * @param object $dirty
     */
    protected function setAllowed($dirty)
    {
        collect($this->allowed)->each(function ($property) use ($dirty) {
            if ($this->isNested($property)) {
                return $this->setNestedProperty($property, $dirty, 'property');
            }

            $this->setProperty($dirty, $property, $this, 'property');
        });
    }

    /**
     * Set properties from methods on cleaner object.
     *
     * @param object $dirty
     */
    protected function setMethods($dirty)
    {
        collect($this->methods)->each(function ($method) use ($dirty) {
            if ($this->isNested($method)) {
                return $this->setNestedProperty($method, $dirty, 'method');
            }

            $this->setProperty($dirty, $method, $this, 'method');
        });
    }

    /**
     * Property is a nested property.
     *
     * @param string $property
     *
     * @return bool
     */
    protected function isNested($property)
    {
        return strpos($property, '.') !== false;
    }

    /**
     * Set the nested property on the cleaner object.
     *
     * @param string $propString [Property string]
     * @param object $dirty      [Original dirty object]
     * @param string $type       [Type of nested property: 'property', 'method']
     * @param object $object     [Object to set property on]
     */
    protected function setNestedProperty($propString, $dirty, $type, $object = null)
    {
        $object = is_null($object) ? $this : $object;

        $relation = $this->setRelation($propString, $object);

        $property = $this->getProperty($propString);

        if ($this->isNested($property)) {
            $newDirty = $dirty->{$relation};

            $newObject = $this->{$relation};

            $this->setNestedProperty($property, $newDirty, $type, $newObject);
        } else {
            $this->setProperty($dirty, $property, $object, $type, $relation);
        }
    }

    /**
     * Create relation on object.
     *
     * @param string $property
     * @param object $object
     *
     * @return string
     */
    protected function setRelation($property, $object)
    {
        $relation = explode('.', $property)[0];

        $this->createRelation($relation, $object);

        return $relation;
    }

    /**
     * Get property name from nested property.
     *
     * @param string $property
     *
     * @return string
     */
    protected function getProperty($property)
    {
        return collect(explode('.', $property))->forget(0)->implode('.');
    }

    /**
     * If relation doesnt not exist on cleaner object, create it.
     *
     * @param string $relation
     * @param object $object   [Object to create relation on]
     */
    protected function createRelation($relation, $object)
    {
        if (!isset($object->{$relation})) {
            $class = new EmptyCleaner();

            $object->{$relation} = $class;
        }
    }

    /**
     * Set property value on clean object.
     *
     * @param object $dirty    [Original, dirty object]
     * @param string $property [Property name]
     * @param object $object   [Clean object]
     * @param string $type     [Type of property: 'property', 'method']
     * @param string $relation [Relationship name]
     */
    protected function setProperty($dirty, $property, $object, $type, $relation = null)
    {
        $key = snake_case($property);

        if (is_null($relation)) {
            return $this->{$key} = $this->getValue($dirty, $property, $type);
        }

        if (!$dirty->{$relation} instanceof Collection) {
            $value = $this->getValue($dirty->{$relation}, $property, $type);

            return $object->{$relation}->{$key} = $value;
        }

        if ($object->{$relation} instanceof EmptyCleaner) {
            $object->{$relation} = [];
        }

        foreach ($dirty->{$relation} as $index => $dirtyObject) {
            if (!isset($object->{$relation}[$index])) {
                $object->{$relation}[$index] = new EmptyCleaner();
            }

            $value = $this->getValue($dirtyObject, $property, $type);

            $object->{$relation}[$index]->{$key} = $value;
        }
    }

    /**
     * Get the value from the named property/method off object.
     *
     * @param object $object [Object which contains property/method]
     * @param string $name   [Name of property/method]
     * @param string $type   ['method' or 'property']
     *
     * @return mixed
     */
    protected function getValue($object, $name, $type)
    {
        if ($type === 'method') {
            if (method_exists($object, $name)) {
                return $object->{$name}();
            }

            return null;
        }

        return $object->{$name};
    }
}
