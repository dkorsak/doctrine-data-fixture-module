<?php

namespace DoctrineDataFixtureModule;

use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleManager;
use DoctrineDataFixtureModule\Command\FixturesLoadCommand;

/**
 * Class Module
 * @package DoctrineDataFixtureModule
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            StandardAutoloader::class => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }

    /**
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager()->getSharedManager();
        $events->attach('doctrine', 'loadCli.post', [$this, 'addFixturesLoadCommand']);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @param EventInterface $event
     */
    public function addFixturesLoadCommand(EventInterface $event)
    {
        /* @var \Symfony\Component\Console\Application $application */
        $application = $event->getTarget();

        /* @var \Interop\Container\ContainerInterface $container */
        $container = $event->getParam('ServiceManager');
        $fixturesLoadCommand = new FixturesLoadCommand($container);
        $application->add($fixturesLoadCommand);
    }
}
