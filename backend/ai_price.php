<?php
$price = $_GET['price'] ?? 0;
$category = $_GET['category'] ?? '';
$description = $_GET['description'] ?? '';
$name = $_GET['name'] ?? '';

// Base price suggestion
if ($price < 50) {
    $suggestion = "Increase price (High demand)";
} elseif ($price > 5000) {
    $suggestion = "Price may be too high. Consider lowering for better sales.";
} else {
    $suggestion = "Good pricing";
}

// Quality keyword detection
$qualityKeywords = ['organic', 'premium', 'fresh', 'high quality', 'grade a', 'grade 1', 'export', 'pure', 'natural', 'high yield', 'daily', 'certified'];
$textToCheck = strtolower($name . ' ' . $description);
$foundQuality = false;

foreach ($qualityKeywords as $keyword) {
    if (strpos($textToCheck, $keyword) !== false) {
        $foundQuality = true;
        break;
    }
}

if ($foundQuality) {
    $suggestion .= " | ⭐ Premium quality detected! You can price 15-25% higher.";
}

// Category-specific advice
if ($category == 'animals') {
    $suggestion .= " | 🐄 Livestock: Consider pricing per head or based on weight/age.";
} elseif ($category == 'dairy') {
    $suggestion .= " | 🥛 Dairy: Price per litre is standard.";
}

echo $suggestion;
?>