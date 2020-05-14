<?php

namespace Aplr\Lectern;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function register()
    {
        $this->registerRepository();
    }

    protected function registerRepository()
    {
        $this->app->bind('repository', function (Container $app, array $params) {
            return new Repository(
                $params['model'] ?? null,
                $params['transformer'] ?? null
            );
        });

        $this->app->alias('repository', Repository::class);
        $this->app->alias('repository', RepositoryInterface::class);
    }
}
