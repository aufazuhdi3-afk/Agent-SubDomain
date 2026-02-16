<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app['env'] = 'testing';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\Facade::setFacadeApplication($app);
Illuminate\Container\Container::setInstance($app);
// Create a test user
App\Models\User::factory()->create(['email' => 'logintest+' . uniqid() . '@example.com']);
// Build request
$testEmail = 'logintest+' . uniqid() . '@example.com';
App\Models\User::factory()->create(['email' => $testEmail]);
$request = Illuminate\Http\Request::create('/login', 'POST', ['email' => $testEmail, 'password' => 'password']);
$response = $app->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo substr($response->getContent(), 0, 2000) . "\n";
if ($response->exception) {
    echo "Exception: " . get_class($response->exception) . " - " . $response->exception->getMessage() . "\n";
}
