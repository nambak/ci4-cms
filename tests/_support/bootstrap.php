<?php

$token = getenv('TEST_TOKEN');
$dbPath = $token ? "build/test_p{$token}.db" : 'build/test.db';

$_ENV['database.tests.database']    = $dbPath;
$_SERVER['database.tests.database'] = $dbPath;
putenv("database.tests.database={$dbPath}");

require __DIR__ . '/../../vendor/codeigniter4/framework/system/Test/bootstrap.php';
