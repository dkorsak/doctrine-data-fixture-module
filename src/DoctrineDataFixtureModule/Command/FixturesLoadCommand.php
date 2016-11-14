<?php

namespace DoctrineDataFixtureModule\Command;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use DoctrineDataFixtureModule\Loader\DataFixturesLoader;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class FixturesLoadCommand
 * @package DoctrineDataFixtureModule\Command
 */
class FixturesLoadCommand extends Command
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * FixturesLoadCommand constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('orm:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->setHelp(
<<<EOT
The command loads data from fixtures files into database for default <info>orm_default</info> connection:

  <info>./app/console doctrine:fixtures:load</info>

If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>./app/console doctrine:fixtures:load --append</info>

By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
the database. If you want to use a TRUNCATE statement instead you can use the <info>--purge-with-truncate</info> flag:

  <info>./app/console doctrine:fixtures:load --purge-with-truncate</info>

EOT
            )
            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.')
            ->addOption('purge-with-truncate', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement')
            ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive() && !$input->getOption('append')) {
            if (!$this->askConfirmation($input, $output, '<question>Careful, database will be purged. Do you want to continue y/N ?</question>', false)) {
                return;
            }
        }

        $em = $this->container->get('doctrine.entitymanager.orm_default');
        $purger = new ORMPurger($em);
        $purgeMethod = $input
            ->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE;
        $purger->setPurgeMode($purgeMethod);
        $loader = new DataFixturesLoader($this->container);
        foreach ($this->getPaths() as $key => $value) {
            $loader->loadFromDirectory($value);
        }

        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($loader->getFixtures(), $input->getOption('append'));
    }

    /**
     * @return array
     */
    private function getPaths()
    {
        $paths = [];
        $options = $this->container->get('config');
        if (isset($options['doctrine']['fixture'])) {
            $paths = $options['doctrine']['fixture'];
        }

        return $paths;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $question
     * @param bool            $default
     *
     * @return bool
     */
    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $question = new ConfirmationQuestion($question, $default);

        return $dialog->ask($input, $output, $question);
    }
}
