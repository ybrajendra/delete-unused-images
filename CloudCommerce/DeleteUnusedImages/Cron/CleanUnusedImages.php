<?php
namespace CloudCommerce\DeleteUnusedImages\Cron;

use CloudCommerce\DeleteUnusedImages\Model\UnusedImagesCleaner;
use CloudCommerce\DeleteUnusedImages\Helper\Data;
use Psr\Log\LoggerInterface;

class CleanUnusedImages
{
    /**     
     * @var UnusedImagesCleaner
     */
    protected $cleaner;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CleanUnusedImages constructor.
     * @param UnusedImagesCleaner $cleaner
     * @param LoggerInterface $logger
     * @param Data $helper
     */
    public function __construct(UnusedImagesCleaner $cleaner, LoggerInterface $logger, Data $helper)
    {
        $this->cleaner = $cleaner;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Execute the cron job to clean unused images
     */
    public function execute()
    {
        try {
            if (!$this->helper->isModuleEnabled()) {
                return;
            }

            $deleted = $this->cleaner->clean(500);
            $this->logger->info("Cron: Deleted {$deleted} unused product images.");
        } catch (\Exception $e) {
            $this->logger->error("Cron: Image cleanup failed - " . $e->getMessage());
        }
    }
}
