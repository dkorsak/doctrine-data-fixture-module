<?php

namespace DoctrineDataFixtureModule;

use Interop\Container\ContainerInterface;

/**
 * Interface ContainerAwareInterface
 * @package DoctrineDataFixtureModule\Loader
 */
interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $container
     * @return null
     */
    public function setContainer(ContainerInterface $container);
}
