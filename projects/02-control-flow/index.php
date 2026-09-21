<?php
// ─── if / elseif / else ───────────────────────────────────────────────────────
echo "<h2>if / elseif / else</h2>";
$score = 75;

if ($score >= 90) {
    echo "Grade: A";
} elseif ($score >= 75) {
    echo "Grade: B";
} elseif ($score >= 60) {
    echo "Grade: C";
} else {
    echo "Grade: F";
}

// ─── Ternary & Null Coalescing ────────────────────────────────────────────────
echo "<h2>Ternary & Null Coalescing</h2>";
$age = 20;
echo ($age >= 18 ? "Adult" : "Minor") . "<br>";

$username = $_GET['user'] ?? "Guest";
echo "Hello, $username!<br>";

// ─── match (PHP 8+) ───────────────────────────────────────────────────────────
echo "<h2>match Expression</h2>";
$status = 2;
$label = match($status) {
    1 => "Active",
    2 => "Inactive",
    3 => "Banned",
    default => "Unknown",
};
echo "Status: $label<br>";

// ─── switch ───────────────────────────────────────────────────────────────────
echo "<h2>switch</h2>";
$day = date("l"); // current day name
switch ($day) {
    case "Saturday":
    case "Sunday":
        echo "Weekend!";
        break;
    case "Monday":
        echo "Start of the week.";
        break;
    case "Friday":
        echo "Almost there!";
        break;
    default:
        echo "Midweek: $day";
}

// ─── Loops ────────────────────────────────────────────────────────────────────
echo "<h2>for loop — multiplication table</h2>";
$n = 5;
for ($i = 1; $i <= 10; $i++) {
    echo "$n × $i = " . ($n * $i) . "<br>";
}

echo "<h2>while — countdown</h2>";
$count = 5;
while ($count > 0) {
    echo "$count... ";
    $count--;
}
echo "Go!<br>";

echo "<h2>foreach — fruits</h2>";
$fruits = ["Apple", "Banana", "Cherry", "Durian"];
foreach ($fruits as $i => $fruit) {
    echo "$i: $fruit<br>";
}

echo "<h2>break & continue — skip evens, stop at 9</h2>";
for ($i = 1; $i <= 15; $i++) {
    if ($i === 9) break;
    if ($i % 2 === 0) continue;
    echo "$i ";
}
