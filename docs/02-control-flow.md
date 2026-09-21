# PHP Control Flow

## if / elseif / else

```php
<?php
$age = 20;

if ($age < 13) {
    echo "Child";
} elseif ($age < 18) {
    echo "Teenager";
} else {
    echo "Adult";
}
?>
```

## Ternary Operator

```php
<?php
$status = ($age >= 18) ? "Adult" : "Minor";

// Null coalescing (PHP 7+)
$name = $_GET['name'] ?? "Guest";
?>
```

## Match Expression (PHP 8+)

```php
<?php
$status = 2;

$label = match($status) {
    1 => "Active",
    2 => "Inactive",
    3 => "Banned",
    default => "Unknown",
};
echo $label; // Inactive
?>
```

## switch

```php
<?php
$day = "Monday";

switch ($day) {
    case "Monday":
    case "Tuesday":
        echo "Early week";
        break;
    case "Friday":
        echo "End of week";
        break;
    default:
        echo "Midweek";
}
?>
```

## while Loop

```php
<?php
$i = 1;

while ($i <= 5) {
    echo $i;
    $i++;
}
// 12345
?>
```

## do...while Loop

```php
<?php
$i = 1;

do {
    echo $i;
    $i++;
} while ($i <= 5);
// always runs at least once
?>
```

## for Loop

```php
<?php
for ($i = 0; $i < 5; $i++) {
    echo $i;
}
// 01234
?>
```

## foreach Loop

```php
<?php
$fruits = ["apple", "banana", "cherry"];

foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}

// with key
$person = ["name" => "Rahul", "age" => 25];
foreach ($person as $key => $value) {
    echo "$key: $value\n";
}
?>
```

## break and continue

```php
<?php
for ($i = 0; $i < 10; $i++) {
    if ($i === 5) break;     // stop loop
    if ($i % 2 === 0) continue; // skip even
    echo $i;
}
// 13
?>
```
