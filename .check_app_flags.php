<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->instance('env', 'testing');
var_export(['runningInConsole' => $app->runningInConsole(), 'runningUnitTests' => $app->runningUnitTests()]);
