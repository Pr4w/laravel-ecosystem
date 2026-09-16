<?php

namespace Pr4w\Ecosystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Pr4w\Ecosystem\EcosystemServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [EcosystemServiceProvider::class];
    }
}
