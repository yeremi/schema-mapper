## Simple Case

This example demonstrates how to map an external API response to a business logic entity in your application.

### External Response Example
Below is an example of an external JSON response:

```json
{"user_name": "John Doe"}
```

### User Entity Definition
The `User` entity is designed to map the external response to a format suitable for your application:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class User {
    
    #[ApiSchema('user_name')] // Maps the external response key
    private string $name;     // Your business logic property

    public function getName(): string
    {
        return $this->name;
    }
}
```

### Implementation Example
Here’s how to use the `NormalizerInterface` to map the external response to your `User` entity:

```php
use Yeremi\SchemaMapper\Normalizer\NormalizerInterface;

class UserUseCase {
    public function __construct(
        private NormalizerInterface $normalizer
    ) {}
    
    public function fetchUser(): void
    {
        // $json is the example JSON response
        $response = json_decode($json, true);

        $user = $this->normalizer->normalize($response, User::class);
        echo $user->getName(); // Outputs: John Doe
    }
}
```

### Key Advantage
If the external API response changes—for example, the `user_name` key is updated to `username`—you only need to update the `#[ApiSchema('user_name')]` attribute in the `User` entity. The library handles the mapping seamlessly, ensuring your application continues to function correctly without requiring extensive code updates.

---

This approach simplifies integration with external APIs, reduces maintenance overhead, and aligns with clean code principles.
