# Schema Mapper

A PHP library for mapping external API data to PHP objects using PHP 8 attributes. This library helps you easily transform API responses into strongly-typed PHP objects with minimal boilerplate code.

---

## Index

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Basic Usage](#basic-usage)
4. [Features](#features)
5. [Error Handling](#error-handling)
6. [Examples](#examples)
7. [License](#license)
8. [Support](#support)

---

## Requirements

- PHP 8.0+

## Installation

Install the package via Composer:

```bash
composer require yeremi/schema-mapper
```

## Basic Usage

1. Define your data class with attributes:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class User
{
    #[ApiSchema(key: 'first_name')]
    private string $firstName;

    #[ApiSchema(key: 'last_name')]
    private string $lastName;

    #[ApiSchema(key: 'email')]
    private string $email;

    // Getters
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
```

2. Use the normalizer to map your data:

```php
use Yeremi\SchemaMapper\Normalizer\NormalizerInterface;

class MyUseCase {
    public function __construct(
        NormalizerInterface $normalizer
    ) {}
    
    public function fetchSomeData(){
        $response = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com'
        ];

        $user = $this->normalizer->normalize($response, User::class);
        echo $user->getFirstName(); // Outputs: John
    }
}
```

---

## Features

- Map API responses to PHP objects using attributes
- Support for nested objects
- Type validation
- Nullable properties support
- Dependency injection ready with interfaces

## Error Handling

The library throws specific exceptions:

- `TypeMismatchException`: When the data type doesn't match the property type
- `ReflectionException`: When there are issues with class reflection

## Examples

For more examples and advanced use cases, refer to the [examples directory](./docs/examples) in the documentation.

## License

This project is open source and licensed under the MIT License - see the LICENSE file for details.

## Support

If you encounter any problems or have any questions, please [open an issue](https://github.com/yeremi/schema-mapper/issues).
