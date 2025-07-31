<?php
namespace CloudCommerce\DeleteUnusedImages\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use CloudCommerce\DeleteUnusedImages\Model\UnusedImagesCleaner;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class UnusedImagesCleanerTest extends TestCase
{
    private $cleaner;
    private $resourceMock;
    private $fileMock;
    private $dirMock;

    protected function setUp(): void
    {
        $this->resourceMock = $this->createMock(ResourceConnection::class);
        $this->fileMock = $this->createMock(File::class);
        $this->dirMock = $this->createMock(DirectoryList::class);

        $this->cleaner = new UnusedImagesCleaner(
            $this->resourceMock,
            $this->fileMock,
            $this->dirMock
        );
    }

    public function testCleanReturnsCount()
    {
        // Mock DB result
        $images = [['value_id' => 1, 'value' => '/t/e/test.jpg']];
        $mockConn = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);

        $this->resourceMock->method('getConnection')->willReturn($mockConn);
        $mockConn->method('fetchAll')->willReturn($images);
        $mockConn->method('delete')->willReturn(1);

        // Mock file deletion
        $this->fileMock->method('fileExists')->willReturn(true);
        $this->fileMock->method('rm')->willReturn(true);

        $result = $this->cleaner->clean(1);
        $this->assertEquals(1, $result);
    }
}
