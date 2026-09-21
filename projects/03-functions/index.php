<?php
// ─── Basic Function ───────────────────────────────────────────────────────────
function greet(string $name = "World"): string {
    return "Hello, $name!";
}
echo "<h2>Basic Function</h2>";
echo greet() . "<br>";
echo greet("Rahul") . "<br>";

// ─── Typed Function ───────────────────────────────────────────────────────────
function add(int $a, int $b): int {
    return $a + $b;
}
echo "<h2>Typed Function</h2>";
echo add(3, 7) . "<br>";

// ─── Variadic ─────────────────────────────────────────────────────────────────
function sum(int ...$nums): int {
    return array_sum($nums);
}
echo "<h2>Variadic</h2>";
echo sum(1, 2, 3, 4, 5) . "<br>";

// ─── Recursive ────────────────────────────────────────────────────────────────
function factorial(int $n): int {
    return $n <= 1 ? 1 : $n * factorial($n - 1);
}
echo "<h2>Recursive — Factorial</h2>";
for ($i = 1; $i <= 7; $i++) {
    echo "$i! = " . factorial($i) . "<br>";
}

// ─── Closure ──────────────────────────────────────────────────────────────────
echo "<h2>Closure</h2>";
$multiplier = 3;
$multiply = function(int $n) use ($multiplier): int {
    return $n * $multiplier;
};
echo $multiply(5) . "<br>"; // 15

// ─── Arrow Function ───────────────────────────────────────────────────────────
echo "<h2>Arrow Function</h2>";
$factor = 10;
$scale  = fn(int $n) => $n * $factor;
echo $scale(7) . "<br>"; // 70

// ─── Higher-Order Functions ───────────────────────────────────────────────────
echo "<h2>Higher-Order Functions</h2>";
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

$evens   = array_filter($numbers, fn($n) => $n % 2 === 0);
$doubled = array_map(fn($n) => $n * 2, $evens);
$total   = array_reduce($doubled, fn($carry, $n) => $carry + $n, 0);

echo "Evens doubled and summed: $total<br>";

// ─── Static Variable ──────────────────────────────────────────────────────────
echo "<h2>Static Variable</h2>";
function counter(): int {
    static $count = 0;
    return ++$count;
}
echo counter() . "<br>"; // 1
echo counter() . "<br>"; // 2
echo counter() . "<br>"; // 3
