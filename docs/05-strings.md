# PHP Strings

## String Syntax

```php
<?php
// Single quotes — no variable interpolation
$str1 = 'Hello World';

// Double quotes — variables are interpolated
$name = "Rahul";
$str2 = "Hello, $name!";        // Hello, Rahul!
$str3 = "Hello, {$name}!";     // same, clearer syntax

// Heredoc — like double quotes, multiline
$text = <<<EOT
Hello, $name!
This spans multiple lines.
EOT;

// Nowdoc — like single quotes, multiline
$raw = <<<'EOT'
No $interpolation here.
EOT;
?>
```

## String Length and Access

```php
<?php
$str = "Hello";

strlen($str);   // 5
$str[0];        // "H"
$str[-1];       // "o" (from end)
?>
```

## Case Functions

```php
<?php
strtoupper("hello");          // HELLO
strtolower("HELLO");          // hello
ucfirst("hello world");       // Hello world
ucwords("hello world");       // Hello World
mb_strtoupper("résumé");      // RÉSUMÉ (multibyte safe)
?>
```

## Trimming

```php
<?php
trim("  hello  ");       // "hello"
ltrim("  hello  ");      // "hello  "
rtrim("  hello  ");      // "  hello"
trim("--hello--", "-");  // "hello" (custom chars)
?>
```

## Searching

```php
<?php
$str = "Hello World";

strpos($str, "World");       // 6 (first occurrence)
strrpos($str, "l");          // 9 (last occurrence)
str_contains($str, "World"); // true  (PHP 8+)
str_starts_with($str, "He"); // true  (PHP 8+)
str_ends_with($str, "ld");   // true  (PHP 8+)
?>
```

## Replacing

```php
<?php
str_replace("World", "PHP", "Hello World"); // Hello PHP
str_ireplace("world", "PHP", "Hello World"); // case-insensitive
substr_replace("Hello World", "PHP", 6, 5); // Hello PHP
?>
```

## Substrings

```php
<?php
$str = "Hello World";

substr($str, 6);       // "World"
substr($str, 0, 5);    // "Hello"
substr($str, -5);      // "World"
substr($str, -5, 3);   // "Wor"
?>
```

## Splitting and Joining

```php
<?php
explode(",", "a,b,c");          // ["a","b","c"]
explode(",", "a,b,c", 2);       // ["a","b,c"] (limit)
implode(", ", ["a","b","c"]);   // "a, b, c"
str_split("Hello", 2);          // ["He","ll","o"]
?>
```

## Padding and Repetition

```php
<?php
str_pad("5", 3, "0", STR_PAD_LEFT);  // "005"
str_pad("hi", 10, "-", STR_PAD_BOTH); // "----hi----"
str_repeat("ab", 3);                   // "ababab"
?>
```

## Comparison

```php
<?php
strcmp("a", "b");     // negative (a < b)
strcasecmp("A", "a"); // 0 (equal, case-insensitive)
similar_text("Hello", "World", $pct); // sets $pct
levenshtein("kitten", "sitting");      // 3
?>
```

## Formatting

```php
<?php
number_format(1234567.891, 2, '.', ','); // "1,234,567.89"
sprintf("%.2f", 3.14159);               // "3.14"
sprintf("%05d", 42);                    // "00042"
sprintf("%-10s|", "left");             // "left      |"
printf("Hello %s, you are %d", "Rahul", 25);
?>
```

## Regular Expressions

```php
<?php
$str = "My phone is 555-1234";

preg_match('/\d{3}-\d{4}/', $str, $matches);
// $matches[0] = "555-1234"

preg_match_all('/\d+/', $str, $matches);
// $matches[0] = ["555","1234"]

preg_replace('/\d/', '#', $str);
// "My phone is ###-####"

preg_split('/[\s,]+/', "one two,three");
// ["one","two","three"]
?>
```

## HTML and URL

```php
<?php
htmlspecialchars("<script>alert('xss')</script>");
// &lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;

htmlspecialchars_decode("&lt;b&gt;Bold&lt;/b&gt;");
// <b>Bold</b>

urlencode("hello world!");   // "hello+world%21"
urldecode("hello+world%21"); // "hello world!"
?>
```
