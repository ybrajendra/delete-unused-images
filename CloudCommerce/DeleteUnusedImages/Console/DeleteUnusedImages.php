<?php
namespace CloudCommerce\DeleteUnusedImages\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use CloudCommerce\DeleteUnusedImages\Model\UnusedImagesCleaner;
use Magento\Framework\App\State;
use CloudCommerce\DeleteUnusedImages\Helper\Data;

/**
 * Class DeleteUnusedImages
 * @package CloudCommerce\DeleteUnusedImages\Console
 */
class DeleteUnusedImages extends Command
{
    const OPTION_LIMIT = 'limit';

    /** @var UnusedImagesCleaner */
    protected $cleaner;

    /** @var State */
    protected $state;

    /** @var Data */
    protected $helper;

    /**
     * @var string
     */
    protected static $defaultName = 'catalog:images:clean-unused';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Delete unused product images from the filesystem and database';

    /**
     * DeleteUnusedImages constructor.
     * @param UnusedImagesCleaner $cleaner
     * @param State $state
     */
    public function __construct(UnusedImagesCleaner $cleaner, State $state, Data $helper)
    {
        $this->cleaner = $cleaner;
        $this->state = $state;
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * Configure the command options
     */
    protected function configure()
    {
        $this->setName(self::$defaultName)
             ->setDescription(self::$defaultDescription)
             ->addOption(self::OPTION_LIMIT, null, InputOption::VALUE_OPTIONAL, 'Number of images to delete per run', 500);
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->helper->isModuleEnabled()) {
            $output->writeln('<info>Module is disabled. Skipping unused images cleanup.</info>');
            return Command::SUCCESS;
        }

        // Set area code to adminhtml to avoid "Area code is not set" error
        $this->state->setAreaCode('adminhtml');
        $limit = (int) $input->getOption(self::OPTION_LIMIT);

        $deletedCount = $this->cleaner->clean($limit);
        $output->writeln("<info>Deleted {$deletedCount} unused images.</info>");

        return Command::SUCCESS;
    }
}
