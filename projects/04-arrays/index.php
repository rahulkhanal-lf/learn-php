<?php
// ─── Indexed Array ────────────────────────────────────────────────────────────
echo "<h2>Indexed Array</h2>";
$colors = ["red", "green", "blue", "yellow"];
echo "First: {$colors[0]}<br>";
echo "Last: " . end($colors) . "<br>";
echo "Count: " . count($colors) . "<br>";

// ─── Associative Array ────────────────────────────────────────────────────────
echo "<h2>Associative Array</h2>";
$person = [
    "name" => "Rahul",
    "age"  => 25,
    "city" => "Kathmandu",
];
foreach ($person as $key => $value) {
    echo "$key: $value<br>";
}

// ─── Multidimensional ─────────────────────────────────────────────────────────
echo "<h2>Multidimensional Array</h2>";
$students = [
    ["name" => "Alice", "score" => 92],
    ["name" => "Bob",   "score" => 78],
    ["name" => "Carol", "score" => 85],
];
foreach ($students as $s) {
    echo "{$s['name']}: {$s['score']}<br>";
}

// ─── Sorting ──────────────────────────────────────────────────────────────────
echo "<h2>Sorting</h2>";
$nums = [3, 1, 4, 1, 5, 9, 2, 6];
sort($nums);
echo "Ascending: " . implode(", ", $nums) . "<br>";
rsort($nums);
echo "Descending: " . implode(", ", $nums) . "<br>";

// Sort students by score descending
usort($students, fn($a, $b) => $b['score'] <=> $a['score']);
echo "<br>Students ranked:<br>";
foreach ($students as $i => $s) {
    echo ($i + 1) . ". {$s['name']} — {$s['score']}<br>";
}

// ─── Transformation ───────────────────────────────────────────────────────────
echo "<h2>map / filter / reduce</h2>";
$numbers = range(1, 10);

$squares = array_map(fn($n) => $n ** 2, $numbers);
echo "Squares: " . implode(", ", $squares) . "<br>";

$odds = array_filter($numbers, fn($n) => $n % 2 !== 0);
echo "Odds: " . implode(", ", $odds) . "<br>";

$sum = array_reduce($numbers, fn($carry, $n) => $carry + $n, 0);
echo "Sum 1–10: $sum<br>";

// ─── Searching ────────────────────────────────────────────────────────────────
echo "<h2>Searching</h2>";
$fruits = ["apple", "banana", "cherry"];
echo in_array("banana", $fruits) ? "banana found<br>" : "not found<br>";
echo "Index of cherry: " . array_search("cherry", $fruits) . "<br>";

// ─── Merging & Slicing ────────────────────────────────────────────────────────
echo "<h2>Merging & Slicing</h2>";
$a = [1, 2, 3];
$b = [4, 5, 6];
$merged = array_merge($a, $b);
echo "Merged: " . implode(", ", $merged) . "<br>";

$slice = array_slice($merged, 2, 3);
echo "Slice [2,3]: " . implode(", ", $slice) . "<br>";

// ─── Unique & Flip ────────────────────────────────────────────────────────────
echo "<h2>Unique & Flip</h2>";
$dupes = [1, 2, 2, 3, 3, 3, 4];
echo "Unique: " . implode(", ", array_unique($dupes)) . "<br>";

$flipped = array_flip(["a" => 1, "b" => 2, "c" => 3]);
print_r($flipped);
