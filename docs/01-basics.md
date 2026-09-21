# PHP Basics

## PHP Tags

```php
<?php
  // your code here
?>
```

## Variables

- Start with `$`
- Case-sensitive
- No type declaration needed (loosely typed)

```php
<?php
$name = "Rahul";
$age = 25;
$price = 9.99;
$isActive = true;
$nothing = null;
?>
```

## Data Types

| Type | Example |
|------|---------|
| String | `"Hello"` |
| Integer | `42` |
| Float | `3.14` |
| Boolean | `true` / `false` |
| NULL | `null` |
| Array | `[1, 2, 3]` |
| Object | instance of a class |

## Type Checking

```php
<?php
var_dump($name);       // string(5) "Rahul"
gettype($age);         // "integer"
is_string($name);      // true
is_int($age);          // true
is_null($nothing);     // true
?>
```

## Type Casting

```php
<?php
$str = "42";
$num = (int) $str;       // 42
$float = (float) "3.14"; // 3.14
$bool = (bool) 0;        // false
?>
```

## Constants

```php
<?php
define("SITE_NAME", "Learn PHP");
const VERSION = "1.0";

echo SITE_NAME; // Learn PHP
echo VERSION;   // 1.0
?>
```

## Operators

### Arithmetic
```php
<?php
$a = 10;
$b = 3;

$a + $b;   // 13
$a - $b;   // 7
$a * $b;   // 30
$a / $b;   // 3.333...
$a % $b;   // 1 (modulus)
$a ** $b;  // 1000 (exponentiation)
?>
```

### Comparison
```php
<?php
$a == $b;   // equal (value only)
$a === $b;  // identical (value + type)
$a != $b;   // not equal
$a !== $b;  // not identical
$a > $b;
$a < $b;
$a >= $b;
$a <= $b;
$a <=> $b;  // spaceship: -1, 0, 1
?>
```

### Logical
```php
<?php
$a && $b;   // AND
$a || $b;   // OR
!$a;        // NOT
$a and $b;  // AND (lower precedence)
$a or $b;   // OR (lower precedence)
?>
```

## Output

```php
<?php
echo "Hello World";           // output string
print "Hello";                // same, returns 1
print_r([1, 2, 3]);           // readable array dump
var_dump([1, "two", true]);   // detailed dump with types
?>
```

## Comments

```php
<?php
// single line comment

# also single line

/*
  multi-line
  comment
*/
?>
```
