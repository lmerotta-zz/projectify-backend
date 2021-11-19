<?php

namespace App\Tests\Helpers;

trait ReflectionTrait
{
    private array $reflectedObjects = [];
    private array $reflectedProperties = [];

    /**
     * @after
     */
    public function resetReflection(): void
    {
        $this->reflectedObjects = [];
        $this->reflectedProperties = [];
    }

    public function getReflection(object $object): \ReflectionObject
    {
        $id = spl_object_hash($object);
        if (empty($this->reflectedObjects[$id])) {
            $reflected = new \ReflectionObject($object);

            $this->reflectedObjects[$id] = $reflected;
        }


        return $this->reflectedObjects[$id];
    }

    public function getReflectedProperty(\ReflectionObject $object, string $propertyName): \ReflectionProperty
    {
        $id = spl_object_hash($object);
        $arrayKey = sprintf("%s_%s", $id, $propertyName);

        if (empty($this->reflectedProperties[$arrayKey])) {
            $property = $object->getProperty($propertyName);
            $property->setAccessible(true);
            $this->reflectedProperties[$arrayKey] = $property;
        }

        return $this->reflectedProperties[$arrayKey];
    }

    public function setFieldValue(object $object, string $propertyName, $value): void
    {
        $ref = $this->getReflection($object);
        $this->getReflectedProperty($ref, $propertyName)->setValue($object, $value);
    }
}