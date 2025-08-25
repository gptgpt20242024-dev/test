<?php

// Simple length unit converter. Usage example: index.php?value=1&from=km&to=m

$units = [
    'mm' => 0.001,
    'cm' => 0.01,
    'm'  => 1,
    'km' => 1000,
];

$value = isset($_GET['value']) ? (float) $_GET['value'] : null;
$from  = isset($_GET['from']) ? strtolower($_GET['from']) : null;
$to    = isset($_GET['to']) ? strtolower($_GET['to']) : null;

header('Content-Type: text/plain; charset=utf-8');

if ($value === null || $from === null || $to === null) {
    echo "Specify 'value', 'from', and 'to' query parameters.";
    exit;
}

if (!isset($units[$from])) {
    echo "Unknown unit: $from";
    exit;
}

if (!isset($units[$to])) {
    echo "Unknown unit: $to";
    exit;
}

$meters = $value * $units[$from];
$result = $meters / $units[$to];

echo $result;
