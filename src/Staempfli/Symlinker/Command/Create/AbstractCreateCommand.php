<?php
/**
 * AbstractCommand
 *
 * Copyright © 2017 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\Symlinker\Command\Create;

use Staempfli\Symlinker\Task\SymlinkTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCreateCommand extends Command
{
    const OPTION_ROOT_DIR = 'root-dir';
    const OPTION_FORCE = 'force';
    const OPTION_DRY_RUN = 'dry-run';
    const OPTION_ENABLE_WILDCARDS = 'enable-wildcards';
    const OPTION_ENABLE_CREATE_PARENT_DIRS = 'enable-create-parent-dirs';

    /**
     * @var SymlinkTask
     */
    protected $symlinkTask;
    /**
     * @var QuestionHelper
     */
    protected $questionHelper;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->symlinkTask = new SymlinkTask();
    }

    protected function configure()
    {
        $this->addOption(
            self::OPTION_ROOT_DIR,
            null,
            InputOption::VALUE_OPTIONAL,
            'Execute command from a different location than current'
        )->addOption(
            self::OPTION_FORCE,
            'f',
            InputOption::VALUE_NONE,
            'Overwrite destination link if already exists'
        )->addOption(
            self::OPTION_DRY_RUN,
            'd',
            InputOption::VALUE_NONE,
            'If specified, no symlinks will be actually generated'
        )->addOption(
            self::OPTION_ENABLE_WILDCARDS,
            null,
            InputOption::VALUE_NONE,
            'Enable wildcards support for source target. "/*" symlinks all content in dir, "/**" symlinks recursively all files in dir and subDirs]'
        )->addOption(
            self::OPTION_ENABLE_CREATE_PARENT_DIRS,
            null,
            InputOption::VALUE_NONE,
            'Enable on the fly recursive creation of missing destination parent directories.');
    }

    /**
     * @inheritdoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');
        $this->symlinkTask->setConsoleOutput($output);
        return parent::run($input, $output);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $input->getOption(self::OPTION_ROOT_DIR);
        if (is_string($rootDir)) {
            chdir($rootDir);
        }
        if ($input->getOption(self::OPTION_FORCE)) {
            $this->symlinkTask->enableOverwrite();
        }
        if ($input->getOption(self::OPTION_DRY_RUN)) {
            $this->symlinkTask->enableDryRun();
        }
        if ($input->getOption(self::OPTION_ENABLE_WILDCARDS)) {
            $this->symlinkTask->enableWildcards();
        }
        if ($input->getOption(self::OPTION_ENABLE_CREATE_PARENT_DIRS)) {
            $this->symlinkTask->enableCreateParentDirs();
        }
    }
}