#!/usr/bin/env php
<?php
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

use Commands\Seed\SeedCommand;
use Commands\Create\CreateCommand;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new Application();

$app->add(new SeedCommand());
$app->add(new CreateCommand());

$app->run();
