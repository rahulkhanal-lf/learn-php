<?php
// ─── String Syntax ────────────────────────────────────────────────────────────
echo "<h2>String Syntax</h2>";
$name = "Rahul";
echo 'Single quotes: $name is not replaced<br>';
echo "Double quotes: $name is replaced<br>";
echo "Curly: {$name} works too<br>";

$heredoc = <<<EOT
Heredoc: Hello, $name!
This is line two.
EOT;
echo nl2br($heredoc) . "<br>";

// ─── Case & Trim ─────────────────────────────────────────────────────────────
echo "<h2>Case & Trim</h2>";
$raw = "  hello world  ";
echo strtoupper($raw) . "<br>";
echo ucwords(trim($raw)) . "<br>";

// ─── Search ──────────────────────────────────────────────────────────────────
echo "<h2>Search</h2>";
$sentence = "The quick brown fox jumps over the lazy dog";
echo strpos($sentence, "fox")   . "<br>";              // 16
echo (str_contains($sentence, "fox") ? "has fox" : "no fox") . "<br>";
echo (str_starts_with($sentence, "The") ? "starts with The" : "") . "<br>";
echo (str_ends_with($sentence, "dog") ? "ends with dog" : "") . "<br>";

// ─── Replace ─────────────────────────────────────────────────────────────────
echo "<h2>Replace</h2>";
echo str_replace("fox", "cat", $sentence) . "<br>";
echo str_ireplace("THE", "A", $sentence) . "<br>";

// ─── Substring ───────────────────────────────────────────────────────────────
echo "<h2>Substring</h2>";
echo substr($sentence, 4, 5) . "<br>"; // quick
echo substr($sentence, -3)   . "<br>"; // dog

// ─── Split & Join ────────────────────────────────────────────────────────────
echo "<h2>Split & Join</h2>";
$words = explode(" ", $sentence);
echo "Word count: " . count($words) . "<br>";
echo implode(" | ", array_slice($words, 0, 4)) . "<br>";

// ─── Padding & Repeat ────────────────────────────────────────────────────────
echo "<h2>Padding & Repeat</h2>";
echo str_pad("42", 6, "0", STR_PAD_LEFT)   . "<br>"; // 000042
echo str_pad("hi", 10, "-", STR_PAD_BOTH)  . "<br>"; // ----hi----
echo str_repeat("ab", 4)                   . "<br>"; // abababab

// ─── Formatting ──────────────────────────────────────────────────────────────
echo "<h2>Formatting</h2>";
$price = 1234567.891;
echo number_format($price, 2, '.', ',') . "<br>"; // 1,234,567.89
echo sprintf("Name: %-10s Age: %03d", "Rahul", 25) . "<br>";

// ─── Regex ───────────────────────────────────────────────────────────────────
echo "<h2>Regex</h2>";
$text = "Call us at 555-1234 or 800-5678 for support.";

preg_match_all('/\d{3}-\d{4}/', $text, $matches);
echo "Phones found: " . implode(", ", $matches[0]) . "<br>";

echo preg_replace('/\d/', '#', $text) . "<br>";

// ─── HTML Safety ─────────────────────────────────────────────────────────────
echo "<h2>HTML Safety</h2>";
$userInput = "<script>alert('xss')</script>";
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8') . "<br>";
