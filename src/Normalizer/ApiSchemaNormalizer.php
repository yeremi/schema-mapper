<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Normalizer;

use ReflectionClass;
use ReflectionException;
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
     * @throws ReflectionException
     */
    public function normalize(array $data, string $targetClass): object
    {
        /** @var class-string $targetClass */
        $reflectionClass = new ReflectionClass($targetClass);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $promotedDefaults = $this->getPromotedPropertyDefaults($reflectionClass);

        foreach ($reflectionClass->getProperties() as $property) {
            $apiSchemaAttribute = $this->getApiSchemaAttribute($property);

            if (! $apiSchemaAttribute) {
                continue;
            }
            $key = $apiSchemaAttribute->getKey();

            if (! array_key_exists($key, $data)) {
                $this->applyPromotedDefault($object, $property, $promotedDefaults);

                continue;
            }

            $value = $data[$key];
            $nestedClass = $apiSchemaAttribute->getTargetClass();

            // Handle nested objects
            if ($nestedClass && is_array($value)) {
                // If it's an array, check if the property is a collection or an object.
                if ($apiSchemaAttribute->isArray()) {
                    $property->setValue($object, $this->normalizeList($value, $nestedClass, $property));
                } else {
                    // If it's a nested object instead of an array of objects.
                    $property->setValue($object, $this->normalize($value, $nestedClass));
                }

                continue;
            }

            $this->ensureValidType($object, $property, $value, $promotedDefaults);
        }

        return $object;
    }

    /**
     * Map a list of nested objects. `null` items are skipped and the result is re-indexed.
     *
     * @param array<mixed> $items
     * @return list<object>
     * @throws InvalidMappingException
     * @throws ReflectionException
     */
    private function normalizeList(array $items, string $targetClass, ReflectionProperty $property): array
    {
        $nestedObjects = [];
        foreach ($items as $index => $item) {
            if ($item === null) {
                continue;
            }

            if (! is_array($item)) {
                throw new InvalidMappingException(
                    sprintf(
                        "Invalid item at index %s for property '%s'. Expected array, got '%s'.",
                        $index,
                        $property->getName(),
                        gettype($item)
                    )
                );
            }

            /** @var array<string, mixed> $item JSON objects decode to string-keyed arrays. */
            $nestedObjects[] = $this->normalize($item, $targetClass);
        }

        return $nestedObjects;
    }

    /**
     * Default values of promoted constructor parameters, keyed by property name.
     *
     * `newInstanceWithoutConstructor()` skips the constructor, so these defaults
     * are never applied unless the normalizer applies them itself.
     *
     * @param ReflectionClass<object> $reflectionClass
     * @return array<string, mixed>
     * @throws ReflectionException
     */
    private function getPromotedPropertyDefaults(ReflectionClass $reflectionClass): array
    {
        $defaults = [];
        foreach ($reflectionClass->getConstructor()?->getParameters() ?? [] as $parameter) {
            if ($parameter->isPromoted() && $parameter->isDefaultValueAvailable()) {
                $defaults[$parameter->getName()] = $parameter->getDefaultValue();
            }
        }

        return $defaults;
    }

    /**
     * Assign the declared default of a promoted property, if it has one and is still uninitialised.
     *
     * @param array<string, mixed> $promotedDefaults
     */
    private function applyPromotedDefault(object $object, ReflectionProperty $property, array $promotedDefaults): void
    {
        $name = $property->getName();
        if (! array_key_exists($name, $promotedDefaults) || $property->isInitialized($object)) {
            return;
        }

        $property->setValue($object, $promotedDefaults[$name]);
    }

    private function getApiSchemaAttribute(ReflectionProperty $property): ?ApiSchema
    {
        $attributes = $property->getAttributes(ApiSchema::class);

        return $attributes ? $attributes[0]->newInstance() : null;
    }

    /**
     * @param array<string, mixed> $promotedDefaults
     */
    private function ensureValidType(object $object, ReflectionProperty $property, mixed $value, array $promotedDefaults): void
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
                    $type::class
                )
            );
        }

        $typeName = $type->getName();
        $isNullable = $type->allowsNull();

        // Handle null values for nullable properties
        if ($value === null) {
            if (! $isNullable) {
                $this->applyPromotedDefault($object, $property, $promotedDefaults);

                return;
            }

            // Assign the default value of the property, based on its type
            $defaultValue = $this->getDefaultValueForType($typeName);
            $property->setValue($object, $defaultValue);

            return;
        }

        // JSON doesn't tell 20 from 20.0: widen int to float, as PHP does without strict types.
        if ($typeName === 'float' && is_int($value)) {
            $value = (float) $value;
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
