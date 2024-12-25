## Object with Objects and Array of Objects Case

This section demonstrates how to map an external response containing nested objects and arrays of objects to your business logic entities.

### External Response Example
Below is an example of a complex external JSON response:

```json
{
    "date": "2024-12-25T19:20:30+01:00",
    "location": {
        "country": "Germany",
        "city": "Münster"
    },
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

### Report Entity Definition
The `Report` entity maps the external response, including nested objects and arrays:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class Report {
    
    #[ApiSchema('date')]
    private string $date = '';
    
    #[ApiSchema('location', Location::class)]
    private ?Location $location = null;
    
    #[ApiSchema('users', UserList::class, isArray: true)]
    private array $users = [];
    
    public function getDate(): string
    {
        return $this->date;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }
    
    public function getUsers(): array
    {
        return $this->users;
    }
}
```

### Location Entity Definition
The `Location` entity represents the nested location data:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class Location {
    
    #[ApiSchema('country')]
    private string $country;
    
    #[ApiSchema('city')]
    private string $city;

    public function getCountry(): string
    {
        return $this->country;
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
}
```

### UserList Entity Definition
The `UserList` entity manages an array of `User` entities:

```php
use Yeremi\SchemaMapper\Attributes\ApiSchema;

class UserList {
    
    #[ApiSchema('users', User::class, isArray: true)]
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
The `User` entity represents individual user data:

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
Here’s how to use the `NormalizerInterface` to map the external response to your `Report` entity:

```php
use Yeremi\SchemaMapper\Normalizer\NormalizerInterface;

class ReportUseCase {

    public function __construct(
        private NormalizerInterface $normalizer
    ) {}
    
    public function fetchUsers(): void
    {
        // $json is the example JSON response
        $json = '{"date": "2024-12-25T19:20:30+01:00", "location": {"country": "Germany", "city": "Münster"}, "users": [{"id": 1, "user_name": "John Doe", "user_email": "john@doe.com"}, {"id": 2, "user_name": "Jane Smith", "user_email": "jane@smith.com"}, {"id": 3, "user_name": "Alice Johnson", "user_email": "alice@johnson.com"}]}';
        $response = json_decode($json, true);

        $report = $this->normalizer->normalize($response, Report::class);
        
        echo $report->getDate(); // Outputs: 2024-12-25T19:20:30+01:00
        echo $report->getLocation()->getCountry(); // Outputs: Germany
        
        foreach ($report->getUsers() as $user) {
            echo $user->getName() . "\n";
        }
    }
}
```

### Key Advantage
By using this library, you can simplify mapping of complex external responses with nested objects and arrays. If the API changes, for example, by renaming `user_name` to `username`, you only need to update the corresponding `#[ApiSchema]` attribute in the entity. This minimizes maintenance effort and ensures your application continues to function correctly.
