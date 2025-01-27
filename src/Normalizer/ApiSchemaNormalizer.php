<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Normalizer;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Yeremi\SchemaMapper\Attributes\ApiSchema;
use Yeremi\SchemaMapper\Exceptions\InvalidMappingException;

class ApiSchemaNormalizer implements NormalizerInterface
{
    /**
     * Normalize the given data into the target class.
     *
     * @param array<string, mixed> $data The data to normalize.
     * @param string $targetClass The target class to normalize the data into.
     * @return object The normalized object.
     * @throws InvalidMappingException
     * @throws \ReflectionException
     */
    public function normalize(array $data, string $targetClass): object
    {
        /** @var class-string $targetClass */
        $reflectionClass = new ReflectionClass($targetClass);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($reflectionClass->getProperties() as $property) {
            $apiSchemaAttribute = $this->getApiSchemaAttribute($property);

            if ($apiSchemaAttribute) {
                $property->setAccessible(true);
                $key = $apiSchemaAttribute->getKey();

                if (array_key_exists($key, $data)) {
                    $value = $data[$key];

                    // Handle nested objects
                    if ($apiSchemaAttribute->getTargetClass() && is_array($value)) {
                        // If it's an array, check if the property is a collection or an object.
                        if ($apiSchemaAttribute->isArray()) {
                            // Map an array of objects.
                            $nestedObjects = [];
                            foreach ($value as $item) {
                                $nestedObjects[] = $this->normalize($item, $apiSchemaAttribute->getTargetClass());
                            }
                            $property->setValue($object, $nestedObjects);
                        } else {
                            // If it's a nested object instead of an array of objects.
                            $nestedObject = $this->normalize($value, $apiSchemaAttribute->getTargetClass());
                            $property->setValue($object, $nestedObject);
                        }
                    } else {
                        $this->ensureValidType($object, $property, $value);
                    }
                }
            }
        }

        return $object;
    }

    private function getApiSchemaAttribute(ReflectionProperty $property): ?ApiSchema
    {
        $property->setAccessible(true);
        $attributes = $property->getAttributes(ApiSchema::class);

        return $attributes ? $attributes[0]->newInstance() : null;
    }

    private function ensureValidType(object $object, ReflectionProperty $property, mixed $value): void
    {
        $type = $property->getType();
        if (! $type) {
            return;
        }

        if (! $type instanceof ReflectionNamedType) {
            throw new InvalidMappingException(
                sprintf(
                    "Unsupported type for property '%s'. Expected named type, got '%s'.",
                    $property->getName(),
                    get_class($type)
                )
            );
        }

        $typeName = $type->getName();
        $isNullable = $type->allowsNull();

        // Handle null values for nullable properties
        if ($value === null) {
            if (! $isNullable) {
                return;
            }

            // Assign the default value of the property, based on its type
            $defaultValue = $this->getDefaultValueForType($typeName);
            $property->setValue($object, $defaultValue);

            return;
        }

        // For non-null values, check the type
        $valid = match($typeName) {
            'int' => is_int($value),
            'string' => is_string($value),
            'float' => is_float($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            default => $value instanceof $typeName
        };

        if (! $valid) {
            throw new InvalidMappingException(
                sprintf(
                    "Invalid type for parameter '%s'. Expected '%s', got '%s'.",
                    $property->getName(),
                    $typeName,
                    gettype($value)
                )
            );
        }

        $property->setValue($object, $value);
    }

    private function getDefaultValueForType(string $typeName): mixed
    {
        return match($typeName) {
            'int' => 0,
            'string' => '',
            'float' => 0.0,
            'bool' => false,
            'array' => [],
            default => null,
        };
    }
}
