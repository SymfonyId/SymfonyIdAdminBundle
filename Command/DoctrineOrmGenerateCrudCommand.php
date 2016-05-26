<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use SymfonyId\AdminBundle\Annotation\Driver;
use SymfonyId\AdminBundle\Doctrine\Generator\ControllerGenerator;
use SymfonyId\AdminBundle\Doctrine\Generator\FormGenerator;
use SymfonyId\AdminBundle\Model\ModelMetadataAwareTrait;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DoctrineOrmGenerateCrudCommand extends GenerateDoctrineCommand
{
    use ModelMetadataAwareTrait;

    /**
     * Command configuration.
     */
    protected function configure()
    {
        $this
            ->addArgument('model', InputArgument::REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite any existing controller or form class when generating the CRUD contents')
            ->setName('symfonyid:generate:crud')
            ->setAliases(array('symfonyid:generate', 'symfonyid:crud:generate'))
            ->setDescription('Generate CRUD from Model using SymfonyId Admin Bundle style')
            ->setHelp(<<<EOT
The <info>siab:generate:crud</info> command generates a CRUD based on a Doctrine ORM or ODM using SymfonyId Admin Bundle style.

<info>php bin/console siab:generate:crud --model=AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overriden by overriding config parameters.
EOT
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setManagerFactory($this->getContainer()->get('symfonyid.admin.manager.manager_factory'));

        $questionHelper = $this->getQuestionHelper();

        /*
         * Question helper
         */
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $model = Validators::validateEntityName($input->getArgument('model'));
        $forceOverwrite = $input->getOption('overwrite');
        list($bundle, $model) = $this->parseShortcutNotation($model);

        $driver = new Driver(array('value' => Driver::ORM));
        try {
            $modelClass = $this->getAliasNamespace($driver, $bundle).'\\'.$model;
        } catch (\Exception $exception) {
            $driver = new Driver(array('value' => Driver::ODM));
            $modelClass = $this->getAliasNamespace($driver, $bundle).'\\'.$model;
        }

        $metadata = $this->getClassMetadata($driver, $modelClass);
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        /** @var FormGenerator $formGenerator */
        $formGenerator = $this->getGenerator($bundle);
        $formGenerator->generate($bundle, $model, $metadata, $forceOverwrite);

        $output->writeln(sprintf('<info>Form type for entity %s has been generated</info>', $modelClass));

        $controllerGenerator = $this->getControllerGenerator($bundle);
        $controllerGenerator->generate($bundle, $modelClass, $metadata, $forceOverwrite);

        $output->writeln(sprintf('<info>Controller for entity %s has been generated</info>', $modelClass));

        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $cacheClearCommand = $this->getApplication()->find('cache:clear');
        $cacheClearCommand->run(new ArrayInput(array('--env' => $kernel->getEnvironment())), $output);

        $output->writeln(sprintf('<info>CRUD Generation is successfully!</info>', $modelClass));
    }

    /**
     * @return FormGenerator
     */
    protected function createGenerator()
    {
        return new FormGenerator();
    }

    /**
     * Lookup in priority.
     *
     * - <Bundle>/Resources/SymfonyIdAdminBundle/skeleton
     * - app/Resources/SymfonyIdAdminBundle/skeleton
     * - <ThisBundleDir>/Resources/skeleton
     * - <ThisBundleDir/Resources
     *
     * @param BundleInterface|null $bundle
     *
     * @return array
     */
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SymfonyIdAdminBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        if (is_dir($dir = $kernel->getRootDir().'/Resources/SymfonyIdAdminBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $reflClass = new \ReflectionObject($this);
        $skeletonDirs[] = dirname($reflClass->getFileName()).'/../Resources/skeleton';
        $skeletonDirs[] = dirname($reflClass->getFileName()).'/../Resources';

        return $skeletonDirs;
    }

    /**
     * @param null $bundle
     *
     * @return ControllerGenerator
     */
    private function getControllerGenerator($bundle = null)
    {
        $generator = new ControllerGenerator();
        $generator->setSkeletonDirs($this->getSkeletonDirs($bundle));

        return $generator;
    }
}
