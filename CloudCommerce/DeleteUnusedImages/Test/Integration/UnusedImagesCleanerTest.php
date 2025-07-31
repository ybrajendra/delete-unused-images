<?php
namespace CloudCommerce\DeleteUnusedImages\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use CloudCommerce\DeleteUnusedImages\Model\UnusedImagesCleaner;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Filesystem\DirectoryList;

class UnusedImagesCleanerTest extends TestCase
{
    /** @var UnusedImagesCleaner */
    private $cleaner;

    /** @var ResourceConnection */
    private $resource;

    /** @var DirectoryList */
    private $dirList;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->cleaner  = $objectManager->create(UnusedImagesCleaner::class);
        $this->resource = $objectManager->get(ResourceConnection::class);
        $this->dirList  = $objectManager->get(DirectoryList::class);
    }

    public function testCleanerDeletesUnusedImage()
    {
        $mediaPath = $this->dirList->getPath('media') . '/catalog/product';
        @mkdir($mediaPath, 0777, true);
        $dummyFile = $mediaPath . '/dummy.jpg';
        file_put_contents($dummyFile, 'test image');

        $this->assertFileExists($dummyFile, 'Dummy image must be created in sandbox media dir');

        // Insert dummy entry into DB (unlink it from any product)
        $connection = $this->resource->getConnection();
        $table = $connection->getTableName('catalog_product_entity_media_gallery');
        $connection->insert($table, [
            'attribute_id' => 1,
            'value'        => '/dummy.jpg',
            'media_type'   => 'image'
        ]);
        $valueId = $connection->lastInsertId();

        // Run the cleaner to delete this image
        $deletedCount = $this->cleaner->clean(10);

        // Assert cleaner removed the file and db record
        $this->assertEquals(1, $deletedCount, 'Cleaner should delete 1 unused image');
        $this->assertFileDoesNotExist($dummyFile, 'Dummy image must be deleted by cleaner');

        $remaining = $connection->fetchOne("SELECT COUNT(*) FROM {$table} WHERE value_id = ?", [$valueId]);
        $this->assertEquals(0, $remaining, 'DB record must be deleted as well');
    }
}
