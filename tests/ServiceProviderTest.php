<?php

namespace Aplr\Tests\Lectern;

use Aplr\Lectern\Repository;
use Aplr\Lectern\RepositoryInterface;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testRepositoryIsInjectable()
    {
        $this->assertIsInjectable(Repository::class);
        $this->assertIsInjectable(RepositoryInterface::class);
    }

    public function testBindings()
    {
        $repository = $this->app->make('repository');

        $this->assertInstanceOf(Repository::class, $repository);
        $this->assertInstanceOf(RepositoryInterface::class, $repository);
    }
}