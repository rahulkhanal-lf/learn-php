# PHP Arrays

## Indexed Arrays

```php
<?php
$fruits = ["apple", "banana", "cherry"];
// or
$fruits = array("apple", "banana", "cherry");

echo $fruits[0]; // apple
echo $fruits[2]; // cherry
?>
```

## Associative Arrays

```php
<?php
$person = [
    "name" => "Rahul",
    "age"  => 25,
    "city" => "Kathmandu",
];

echo $person["name"]; // Rahul
echo $person["age"];  // 25
?>
```

## Multidimensional Arrays

```php
<?php
$users = [
    ["name" => "Rahul", "age" => 25],
    ["name" => "Sita",  "age" => 22],
    ["name" => "Ram",   "age" => 30],
];

echo $users[0]["name"]; // Rahul
echo $users[1]["age"];  // 22
?>
```

## Adding / Modifying Elements

```php
<?php
$arr = [1, 2, 3];

$arr[] = 4;          // append
$arr[1] = 99;        // modify index 1
$arr["key"] = "val"; // add associative key
?>
```

## Removing Elements

```php
<?php
$arr = [1, 2, 3, 4, 5];

unset($arr[2]);       // removes index 2, gaps remain
array_splice($arr, 1, 2); // removes 2 elements from index 1, re-indexes
?>
```

## Common Array Functions

### Inspection
```php
<?php
count($arr);            // number of elements
in_array(3, $arr);      // true if 3 exists
array_key_exists("name", $person); // true
isset($arr[0]);         // true if set and not null
?>
```

### Sorting
```php
<?php
$nums = [3, 1, 4, 1, 5];

sort($nums);            // ascending, re-index
rsort($nums);           // descending, re-index
asort($assoc);          // sort by value, keep keys
arsort($assoc);         // sort by value desc, keep keys
ksort($assoc);          // sort by key
krsort($assoc);         // sort by key desc
usort($arr, fn($a, $b) => $a <=> $b); // custom sort
?>
```

### Transformation
```php
<?php
$nums = [1, 2, 3, 4, 5];

array_map(fn($n) => $n * 2, $nums);
// [2, 4, 6, 8, 10]

array_filter($nums, fn($n) => $n % 2 === 0);
// [2, 4]

array_reduce($nums, fn($carry, $n) => $carry + $n, 0);
// 15

array_reverse($nums);
// [5, 4, 3, 2, 1]
?>
```

### Slicing and Merging
```php
<?php
$arr = [1, 2, 3, 4, 5];

array_slice($arr, 1, 3);         // [2, 3, 4]
array_merge([1, 2], [3, 4]);     // [1, 2, 3, 4]
array_combine(["a","b"], [1, 2]); // ["a"=>1, "b"=>2]
?>
```

### Searching
```php
<?php
$fruits = ["apple", "banana", "cherry"];

array_search("banana", $fruits); // 1 (index)
array_keys($person);             // ["name","age","city"]
array_values($person);           // ["Rahul",25,"Kathmandu"]
?>
```

### Stack and Queue
```php
<?php
$stack = [];
array_push($stack, "a", "b"); // push to end
array_pop($stack);            // remove from end

array_unshift($stack, "z");   // push to start
array_shift($stack);          // remove from start
?>
```

### Unique and Flip
```php
<?php
array_unique([1, 2, 2, 3, 3]); // [1, 2, 3]
array_flip(["a" => 1, "b" => 2]); // [1 => "a", 2 => "b"]
?>
```

## Spread Operator with Arrays

```php
<?php
$first = [1, 2, 3];
$second = [4, 5, 6];
$merged = [...$first, ...$second]; // [1,2,3,4,5,6]
?>
```

## Destructuring

```php
<?php
[$a, $b, $c] = [1, 2, 3];
echo $a; // 1

["name" => $name, "age" => $age] = ["name" => "Rahul", "age" => 25];
echo $name; // Rahul
?>
```
