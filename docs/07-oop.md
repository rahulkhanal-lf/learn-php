# PHP Object-Oriented Programming

## Classes and Objects

```php
<?php
class Animal {
    public string $name;
    public int $age;

    public function __construct(string $name, int $age) {
        $this->name = $name;
        $this->age  = $age;
    }

    public function speak(): string {
        return "{$this->name} makes a sound.";
    }
}

$animal = new Animal("Dog", 3);
echo $animal->name;      // Dog
echo $animal->speak();   // Dog makes a sound.
?>
```

## Access Modifiers

| Modifier | Class | Subclass | Outside |
|----------|-------|----------|---------|
| `public` | Yes | Yes | Yes |
| `protected` | Yes | Yes | No |
| `private` | Yes | No | No |

```php
<?php
class BankAccount {
    private float $balance = 0;

    public function deposit(float $amount): void {
        if ($amount > 0) $this->balance += $amount;
    }

    public function getBalance(): float {
        return $this->balance;
    }
}

$acc = new BankAccount();
$acc->deposit(100);
echo $acc->getBalance(); // 100
// $acc->balance;        // Error! private
?>
```

## Constructor Property Promotion (PHP 8+)

```php
<?php
class User {
    public function __construct(
        public readonly int $id,
        public string $name,
        private string $email,
    ) {}
}

$user = new User(1, "Rahul", "rahul@example.com");
echo $user->name; // Rahul
echo $user->id;   // 1
?>
```

## Inheritance

```php
<?php
class Animal {
    public function __construct(public string $name) {}

    public function speak(): string {
        return "{$this->name} makes a sound.";
    }
}

class Dog extends Animal {
    public function speak(): string {
        return "{$this->name} barks!";
    }

    public function fetch(): string {
        return "{$this->name} fetches the ball!";
    }
}

class Cat extends Animal {
    public function speak(): string {
        return "{$this->name} meows!";
    }
}

$dog = new Dog("Rex");
echo $dog->speak();  // Rex barks!
echo $dog->fetch();  // Rex fetches the ball!

$cat = new Cat("Whiskers");
echo $cat->speak();  // Whiskers meows!
?>
```

## Abstract Classes

```php
<?php
abstract class Shape {
    abstract public function area(): float;

    public function describe(): string {
        return "This shape has area: " . $this->area();
    }
}

class Circle extends Shape {
    public function __construct(private float $radius) {}

    public function area(): float {
        return M_PI * $this->radius ** 2;
    }
}

class Rectangle extends Shape {
    public function __construct(
        private float $width,
        private float $height
    ) {}

    public function area(): float {
        return $this->width * $this->height;
    }
}

$c = new Circle(5);
echo $c->area();      // 78.539...
echo $c->describe();  // This shape has area: 78.539...
?>
```

## Interfaces

```php
<?php
interface Printable {
    public function print(): void;
}

interface Exportable {
    public function export(string $format): string;
}

// A class can implement multiple interfaces
class Report implements Printable, Exportable {
    public function __construct(private string $title) {}

    public function print(): void {
        echo "Printing: {$this->title}";
    }

    public function export(string $format): string {
        return "Exported {$this->title} as $format";
    }
}
?>
```

## Traits

```php
<?php
trait Timestamps {
    private string $createdAt;
    private string $updatedAt;

    public function setCreatedAt(): void {
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }
}

trait SoftDelete {
    private bool $deleted = false;

    public function delete(): void {
        $this->deleted = true;
    }

    public function isDeleted(): bool {
        return $this->deleted;
    }
}

class Post {
    use Timestamps, SoftDelete;

    public function __construct(public string $title) {
        $this->setCreatedAt();
    }
}

$post = new Post("My Post");
echo $post->getCreatedAt();
$post->delete();
var_dump($post->isDeleted()); // bool(true)
?>
```

## Static Methods and Properties

```php
<?php
class Counter {
    private static int $count = 0;

    public static function increment(): void {
        self::$count++;
    }

    public static function getCount(): int {
        return self::$count;
    }
}

Counter::increment();
Counter::increment();
echo Counter::getCount(); // 2
?>
```

## Magic Methods

```php
<?php
class MagicDemo {
    private array $data = [];

    // Called when accessing undefined property
    public function __get(string $name): mixed {
        return $this->data[$name] ?? null;
    }

    // Called when setting undefined property
    public function __set(string $name, mixed $value): void {
        $this->data[$name] = $value;
    }

    // Called when checking isset on undefined property
    public function __isset(string $name): bool {
        return isset($this->data[$name]);
    }

    // Called when object used as string
    public function __toString(): string {
        return json_encode($this->data);
    }

    // Called when object used as function
    public function __invoke(string $msg): void {
        echo $msg;
    }

    // Called on clone
    public function __clone(): void {
        $this->data = array_map(fn($v) => $v, $this->data);
    }
}

$obj = new MagicDemo();
$obj->name = "Rahul";       // __set
echo $obj->name;            // __get → "Rahul"
echo $obj;                  // __toString → {"name":"Rahul"}
$obj("Hello!");             // __invoke → "Hello!"
?>
```

## Enums (PHP 8.1+)

```php
<?php
enum Status {
    case Active;
    case Inactive;
    case Banned;
}

enum Color: string {
    case Red   = 'red';
    case Green = 'green';
    case Blue  = 'blue';
}

$status = Status::Active;
$color  = Color::Red;

echo $color->value;         // "red"
Color::from('green');       // Color::Green
Color::tryFrom('purple');   // null (no exception)

// Enums in match
$label = match($status) {
    Status::Active   => "Active",
    Status::Inactive => "Inactive",
    Status::Banned   => "Banned",
};
?>
```

## Nullsafe Operator (PHP 8+)

```php
<?php
class User {
    public ?Address $address = null;
}

class Address {
    public ?City $city = null;
}

class City {
    public string $name = "Kathmandu";
}

$user = new User();

// Without nullsafe — verbose
$cityName = null;
if ($user->address !== null && $user->address->city !== null) {
    $cityName = $user->address->city->name;
}

// With nullsafe — clean
$cityName = $user?->address?->city?->name; // null (no exception)
?>
```
