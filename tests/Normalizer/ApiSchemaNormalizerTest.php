<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Normalizer;

use PHPUnit\Framework\TestCase;
use Yeremi\SchemaMapper\Exceptions\InvalidMappingException;
use Yeremi\SchemaMapper\Normalizer\ApiSchemaNormalizer;
use Yeremi\SchemaMapper\Normalizer\NormalizerInterface;
use Yeremi\SchemaMapper\Tests\Fixtures\Address;
use Yeremi\SchemaMapper\Tests\Fixtures\ComplexUser;
use Yeremi\SchemaMapper\Tests\Fixtures\SimpleUser;
use Yeremi\SchemaMapper\Tests\Fixtures\UserWithAddress;
use Yeremi\SchemaMapper\Tests\Fixtures\UserWithAge;
use Yeremi\SchemaMapper\Tests\Fixtures\UserWithNullable;
use Yeremi\SchemaMapper\Tests\Fixtures\UserWithOptional;

class ApiSchemaNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ApiSchemaNormalizer();
    }

    public function testBasicNormalization(): void
    {
        $data = ['name' => 'John Doe'];
        $result = $this->normalizer->normalize($data, SimpleUser::class);

        $this->assertInstanceOf(SimpleUser::class, $result);
        $this->assertEquals('John Doe', $result->getName());
    }

    public function testNestedObjectNormalization(): void
    {
        $data = [
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
            ],
        ];

        $result = $this->normalizer->normalize($data, UserWithAddress::class);

        $this->assertInstanceOf(UserWithAddress::class, $result);
        $address = $result->getAddress();
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('123 Main St', $address->getStreet());
        $this->assertEquals('New York', $address->getCity());
    }

    public function testInvalidMappingException(): void
    {
        $this->expectException(InvalidMappingException::class);
        $this->expectExceptionMessage("Invalid type for parameter 'age'. Expected 'int', got 'string'.");

        $data = ['age' => '25']; // String instead of int
        $this->normalizer->normalize($data, UserWithAge::class);
    }

    public function testNullableProperty(): void
    {
        // Test with null value
        $data1 = ['description' => null];
        $result1 = $this->normalizer->normalize($data1, UserWithNullable::class);
        $this->assertNull($result1->getDescription());

        // Test with string value
        $data2 = ['description' => 'test description'];
        $result2 = $this->normalizer->normalize($data2, UserWithNullable::class);
        $this->assertEquals('test description', $result2->getDescription());
    }

    public function testMissingProperty(): void
    {
        // Test with missing property
        $data = []; // No email provided
        $result = $this->normalizer->normalize($data, UserWithOptional::class);
        $this->assertNull($result->getEmail());
    }

    public function testMultiplePropertiesNormalization(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => 30,
            'is_active' => true,
        ];

        $result = $this->normalizer->normalize($data, ComplexUser::class);

        $this->assertInstanceOf(ComplexUser::class, $result);
        $this->assertEquals('John', $result->getFirstName());
        $this->assertEquals('Doe', $result->getLastName());
        $this->assertEquals(30, $result->getAge());
        $this->assertTrue($result->isActive());
    }
}
