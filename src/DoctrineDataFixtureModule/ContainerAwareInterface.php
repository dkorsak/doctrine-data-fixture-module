<?php

namespace DoctrineDataFixtureModule;

use Interop\Container\ContainerInterface;

/**
 * Interface ContainerAwareInterface
 * @package DoctrineDataFixtureModule
 */
interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $container
     * @return null
     */
    public function setContainer(ContainerInterface $container);
}
