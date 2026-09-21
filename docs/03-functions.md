# PHP Functions

## Defining and Calling Functions

```php
<?php
function greet($name) {
    return "Hello, $name!";
}

echo greet("Rahul"); // Hello, Rahul!
?>
```

## Default Parameters

```php
<?php
function greet($name = "World") {
    return "Hello, $name!";
}

echo greet();        // Hello, World!
echo greet("Rahul"); // Hello, Rahul!
?>
```

## Type Declarations (PHP 7+)

```php
<?php
function add(int $a, int $b): int {
    return $a + $b;
}

function divide(float $a, float $b): float {
    return $a / $b;
}

function getName(): string {
    return "Rahul";
}

function isActive(): bool {
    return true;
}

// nullable type
function find(int $id): ?string {
    return null; // or a string
}
?>
```

## Variable Number of Arguments

```php
<?php
function sum(...$nums) {
    return array_sum($nums);
}

echo sum(1, 2, 3, 4); // 10
?>
```

## Variable Scope

```php
<?php
$globalVar = "I am global";

function test() {
    // $globalVar not accessible here by default
    global $globalVar;   // bring it in explicitly
    echo $globalVar;
}

test();
?>
```

## Static Variables

```php
<?php
function counter() {
    static $count = 0; // retains value between calls
    $count++;
    return $count;
}

echo counter(); // 1
echo counter(); // 2
echo counter(); // 3
?>
```

## Anonymous Functions (Closures)

```php
<?php
$greet = function($name) {
    return "Hello, $name!";
};

echo $greet("Rahul");

// use variables from outer scope
$prefix = "Mr.";
$formal = function($name) use ($prefix) {
    return "Hello, $prefix $name!";
};

echo $formal("Rahul"); // Hello, Mr. Rahul!
?>
```

## Arrow Functions (PHP 7.4+)

```php
<?php
$multiply = fn($a, $b) => $a * $b;

echo $multiply(3, 4); // 12

// auto-captures outer scope (no `use` needed)
$factor = 5;
$scale = fn($n) => $n * $factor;

echo $scale(3); // 15
?>
```

## First-Class Callable Syntax (PHP 8.1+)

```php
<?php
function double(int $n): int {
    return $n * 2;
}

$fn = double(...);
echo $fn(5); // 10

$numbers = [1, 2, 3, 4];
$doubled = array_map(double(...), $numbers);
// [2, 4, 6, 8]
?>
```

## Recursive Functions

```php
<?php
function factorial(int $n): int {
    if ($n <= 1) return 1;
    return $n * factorial($n - 1);
}

echo factorial(5); // 120
?>
```

## Built-in String Functions

```php
<?php
strlen("hello");           // 5
strtoupper("hello");       // HELLO
strtolower("HELLO");       // hello
str_replace("o", "0", "hello"); // hell0
trim("  hello  ");         // "hello"
explode(",", "a,b,c");     // ["a","b","c"]
implode("-", ["a","b"]);   // "a-b"
substr("hello", 1, 3);     // "ell"
strpos("hello", "l");      // 2
str_contains("hello", "ell"); // true (PHP 8+)
?>
```

## Built-in Math Functions

```php
<?php
abs(-5);       // 5
round(3.6);    // 4
ceil(3.1);     // 4
floor(3.9);    // 3
max(1, 5, 3);  // 5
min(1, 5, 3);  // 1
rand(1, 100);  // random int
sqrt(16);      // 4
pow(2, 8);     // 256
?>
```
