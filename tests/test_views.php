<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    'GET /' => Illuminate\Http\Request::create('/', 'GET'),
    'GET /students/create' => Illuminate\Http\Request::create('/students/create', 'GET'),
    'GET /students/1' => Illuminate\Http\Request::create('/students/1', 'GET'),
    'GET /audit-logs' => Illuminate\Http\Request::create('/audit-logs', 'GET'),
    'GET /api/satdiks/1/programs' => Illuminate\Http\Request::create('/api/satdiks/1/programs', 'GET'),
];

foreach ($routes as $name => $request) {
    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        echo "[$name] -> Status: {$status}\n";
        if ($status !== 200) {
            echo "Non-200 output preview:\n" . substr($response->getContent(), 0, 300) . "\n";
        }
    } catch (\Throwable $e) {
        echo "[$name] -> EXCEPTION: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

echo "\n--- TESTING POST REVEAL SENSITIVE ---\n";
try {
    $revealRequest = Illuminate\Http\Request::create('/students/1/reveal-sensitive', 'POST', [
        'access_reason' => 'Pengujian verifikasi akun dinas ULP'
    ], [], [], ['CONTENT_TYPE' => 'application/json']);
    
    // In Laravel, json payload should be set in content if content-type is json
    $revealRequest->headers->set('Accept', 'application/json');
    $revealRequest->headers->set('Content-Type', 'application/json');
    $revealRequest->initialize([], [], [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
    ], json_encode(['access_reason' => 'Pengujian verifikasi akun dinas ULP']));

    $response = $kernel->handle($revealRequest);
    echo "POST /students/1/reveal-sensitive -> Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "POST EXCEPTION: " . $e->getMessage() . "\n";
}
