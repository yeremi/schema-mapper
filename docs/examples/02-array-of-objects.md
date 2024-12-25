## Array of Objects Case

This example demonstrates how to handle an array of objects in an external API response and map it to a structured format for your application.

### External Response Example
Below is an example of an external JSON response containing an array of objects:

```json
{
  "users": [
    {
      "id": 1,
      "user_name": "John Doe",
      "user_email": "john@doe.com"
    },
    {
      "id": 2,
      "user_name": "Jane Smith",
      "user_email": "jane@smith.com"
    },
    {
      "id": 3,
      "user_name": "Alice Johnson",
      "user_email": "alice@johnson.com"
    }
  ]
}
```

### UserList Entity Definition
The `UserList` entity maps the external response to a format suitable for your application:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserList {
    
    #[ApiSchema('users', User::class, isArray: true)] // Maps the external response as an array of User objects
    private array $users;

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
```

### User Entity Definition
The `User` entity maps individual objects within the array:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class User {
    
    #[ApiSchema('id')]
    private int $id;
    
    #[ApiSchema('user_name')]
    private string $name;
    
    #[ApiSchema('user_email')]
    private string $email;

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
}
```

### Implementation Example
Here’s how to use the `NormalizerInterface` to map the external response to your `UserList` entity:

```php
use Yeremi\SchemaMapper\Normalizer\NormalizerInterface;

class UserListUseCase {

    public function __construct(
        private NormalizerInterface $normalizer
    ) {}
    
    public function fetchUsers(): void
    {
        // $json is the example JSON response
        $response = json_decode($json, true);

        $userList array_map(
            fn (array $data): object => $this->normalizer->normalize( $data, UserList::class),
            $response
        );
        
        foreach ($userList->getUsers() as $user) {
            echo $user->getName() . "\n";
        }
    }
}
```

### Key Advantage
If the external API response changes—for example, the `user_name` key is updated to `username`—you only need to update the `#[ApiSchema('user_name')]` attribute in the `User` entity. The library handles the mapping seamlessly, ensuring your application continues to function correctly without requiring extensive code updates.

---

This approach simplifies integration with external APIs, reduces maintenance overhead, and aligns with clean code principles.
