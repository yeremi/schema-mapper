<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Normalizer;

use ReflectionClass;
use ReflectionProperty;
use Yeremi\SchemaMapper\Attributes\ApiSchema;
use Yeremi\SchemaMapper\Exceptions\InvalidMappingException;
use Yeremi\SchemaMapper\Exceptions\TypeMismatchException;

class ApiSchemaNormalizer implements NormalizerInterface
{
    public function normalize(array $data, string $targetClass): object
    {
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
                        $nestedObject = $this->normalize($value, $apiSchemaAttribute->getTargetClass());
                        $property->setValue($object, $nestedObject);
                    } else {
                        $this->ensureValidType($property, $value);
                        $property->setValue($object, $value);
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

    private function ensureValidType(ReflectionProperty $property, $value): void
    {
        $type = $property->getType();
        if (! $type) {
            return;
        }

        $typeName = $type->getName();
        $isNullable = $type->allowsNull();

        // Handle null values for nullable properties
        if ($value === null) {
            if (! $isNullable) {
                throw new TypeMismatchException(
                    sprintf("Property '%s' does not allow null values", $property->getName())
                );
            }

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
    }
}
