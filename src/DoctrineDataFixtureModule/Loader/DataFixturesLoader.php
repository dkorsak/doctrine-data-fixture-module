<?php

namespace DoctrineDataFixtureModule\Loader;

use Doctrine\Common\DataFixtures\Loader;
use DoctrineDataFixtureModule\ContainerAwareInterface;
use Interop\Container\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Class ServiceLocatorAwareLoader
 * @package DoctrineDataFixtureModule\Loader
 */
class DataFixturesLoader extends Loader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ServiceLocatorAwareLoader constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Add a fixture object instance to the loader.
     *
     * @param FixtureInterface $fixture
     */
    public function addFixture(FixtureInterface $fixture)
    {
        if ($fixture instanceof ContainerAwareInterface) {
            $fixture->setContainer($this->container);
        }
        parent::addFixture($fixture);
    }
}
