<?php
// ─── Variables & Data Types ───────────────────────────────────────────────────
$name    = "Rahul";
$age     = 25;
$price   = 9.99;
$isAdmin = true;
$nothing = null;

echo "<h2>Variables</h2>";
echo "Name: $name <br>";
echo "Age: $age <br>";
echo "Price: $price <br>";
echo "Admin: " . ($isAdmin ? "Yes" : "No") . "<br>";
echo "Nothing: " . var_export($nothing, true) . "<br>";

// ─── Type Checking ────────────────────────────────────────────────────────────
echo "<h2>Type Checking</h2>";
var_dump($name);
var_dump($age);
var_dump($isAdmin);
var_dump($nothing);

// ─── Type Casting ─────────────────────────────────────────────────────────────
echo "<h2>Type Casting</h2>";
$strNum = "42abc";
echo (int)    $strNum . "<br>"; // 42
echo (float)  "3.14"  . "<br>"; // 3.14
echo (bool)   0       . "<br>"; // (empty = false)
echo (string) true    . "<br>"; // "1"

// ─── Constants ────────────────────────────────────────────────────────────────
echo "<h2>Constants</h2>";
define("SITE_NAME", "Learn PHP");
const VERSION = "1.0";

echo SITE_NAME . " v" . VERSION . "<br>";

// ─── Operators ────────────────────────────────────────────────────────────────
echo "<h2>Arithmetic Operators</h2>";
$a = 10; $b = 3;
echo "$a + $b = " . ($a + $b)  . "<br>";
echo "$a - $b = " . ($a - $b)  . "<br>";
echo "$a * $b = " . ($a * $b)  . "<br>";
echo "$a / $b = " . ($a / $b)  . "<br>";
echo "$a % $b = " . ($a % $b)  . "<br>";
echo "$a ** $b = " . ($a ** $b) . "<br>";

echo "<h2>Comparison Operators</h2>";
var_dump(0 == "0");   // true  (loose)
var_dump(0 === "0");  // false (strict)
var_dump(1 <=> 2);    // -1
var_dump(2 <=> 2);    // 0
var_dump(3 <=> 2);    // 1
