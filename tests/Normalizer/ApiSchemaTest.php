<?php

declare(strict_types=1);

namespace Yeremi\SchemaMapper\Tests\Normalizer;

use PHPUnit\Framework\TestCase;
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class ApiSchemaTest extends TestCase
{
    public function testConstructorAndGettersWithAllParameters(): void
    {
        $key = 'test_key';
        $targetClass = 'TestClass';
        $isArray = true;

        $apiSchema = new ApiSchema($key, $targetClass, $isArray);

        $this->assertSame($key, $apiSchema->getKey());
        $this->assertSame($targetClass, $apiSchema->getTargetClass());
        $this->assertTrue($apiSchema->isArray());
    }

    public function testConstructorAndGettersWithDefaultParameters(): void
    {
        $key = 'test_key';

        $apiSchema = new ApiSchema($key);

        $this->assertSame($key, $apiSchema->getKey());
        $this->assertNull($apiSchema->getTargetClass());
        $this->assertFalse($apiSchema->isArray());
    }

    public function testGetKey(): void
    {
        $apiSchema = new ApiSchema('test_key');
        $this->assertSame('test_key', $apiSchema->getKey());
    }

    public function testGetTargetClass(): void
    {
        $apiSchema = new ApiSchema('test_key', 'TestClass');
        $this->assertSame('TestClass', $apiSchema->getTargetClass());

        $apiSchemaWithoutTargetClass = new ApiSchema('test_key');
        $this->assertNull($apiSchemaWithoutTargetClass->getTargetClass());
    }

    public function testIsArray(): void
    {
        $apiSchemaTrue = new ApiSchema('test_key', null, true);
        $this->assertTrue($apiSchemaTrue->isArray());

        $apiSchemaFalse = new ApiSchema('test_key');
        $this->assertFalse($apiSchemaFalse->isArray());
    }
}
