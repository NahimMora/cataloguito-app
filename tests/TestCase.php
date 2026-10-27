<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");
        if (! $app->environment('testing') || ! is_string($database) || ! str_ends_with($database, '_test')) {
            throw new \RuntimeException('Las pruebas solo pueden ejecutarse sobre una base terminada en _test y APP_ENV=testing.');
        }

        return $app;
    }
}
