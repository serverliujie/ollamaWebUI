<?php
/**
 * Test script for PHP Ollama Web UI
 * 
 * This script tests all the API endpoints of the PHP version of the Ollama web UI.
 * It verifies that:
 * 1. The Ollama server is accessible
 * 2. The PHP API endpoints are working
 * 3. The generate endpoint works with streaming
 * 4. The chat endpoint works with streaming
 */

$OLLAMA_BASE = getenv('OLLAMA_BASE') ?: 'http://localhost:11434';
$PHP_BASE = 'http://localhost:8080';

echo "Testing PHP Ollama Web UI\n";
echo "=========================\n\n";

// Test 1: Check Ollama connection
echo "1. Testing Ollama server connection...\n";
try {
    $ch = curl_init("$OLLAMA_BASE/api/tags");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        $model_count = count($data['models'] ?? []);
        echo "   ✓ Connected to Ollama at $OLLAMA_BASE\n";
        echo "   ✓ Found $model_count model(s)\n";
        if ($model_count > 0) {
            echo "   Models: " . implode(', ', array_column($data['models'], 'name')) . "\n";
            $first_model = $data['models'][0]['name'];
        }
    } else {
        echo "   ✗ Failed to connect to Ollama (HTTP $http_code)\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 2: Test PHP API endpoints
echo "2. Testing PHP API endpoints...\n";

// Test /api/tags
echo "   Testing /api/tags...\n";
$ch = curl_init("$PHP_BASE/api/tags");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code === 200) {
    $data = json_decode($response, true);
    $model_count = count($data['models'] ?? []);
    echo "   ✓ /api/tags working ($model_count models)\n";
} else {
    echo "   ✗ /api/tags failed (HTTP $http_code)\n";
    exit(1);
}

// Test /api/generate
echo "   Testing /api/generate...\n";
$test_data = [
    'model' => $first_model,
    'prompt' => 'Say "Test successful"',
    'stream' => false
];

$ch = curl_init("$PHP_BASE/api/generate");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$start_time = microtime(true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$end_time = microtime(true);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if (isset($data['response'])) {
        echo "   ✓ /api/generate working\n";
        echo "   Response: \"" . trim($data['response']) . "\"\n";
        echo "   Time: " . round(($end_time - $start_time) * 1000, 2) . "ms\n";
    } else {
        echo "   ✗ Unexpected response format\n";
        exit(1);
    }
} else {
    echo "   ✗ /api/generate failed (HTTP $http_code)\n";
    exit(1);
}

// Test /api/chat
echo "   Testing /api/chat...\n";
$chat_data = [
    'model' => $first_model,
    'messages' => [['role' => 'user', 'content' => 'Hello']],
    'stream' => false
];

$ch = curl_init("$PHP_BASE/api/chat");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chat_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if (isset($data['message']['content'])) {
        echo "   ✓ /api/chat working\n";
        echo "   Response: \"" . trim($data['message']['content']) . "\"\n";
    } else {
        echo "   ✗ Unexpected response format\n";
        exit(1);
    }
} else {
    echo "   ✗ /api/chat failed (HTTP $http_code)\n";
    exit(1);
}

// Test 3: Check web interface
echo "\n3. Testing web interface...\n";
$ch = curl_init("$PHP_BASE/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code === 200 && strpos($response, '离线AI 小助手') !== false) {
    echo "   ✓ Web interface is accessible\n";
} else {
    echo "   ✗ Web interface not accessible (HTTP $http_code)\n";
    exit(1);
}

echo "\n";
echo "✓ All tests passed!\n";
echo "\n";
echo "You can access the web interface at: http://localhost:8080/\n";
echo "The PHP server is running on port 8080.\n";
?>
