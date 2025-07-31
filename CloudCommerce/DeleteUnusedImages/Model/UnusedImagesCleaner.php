<?php
namespace CloudCommerce\DeleteUnusedImages\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class UnusedImagesCleaner
 * @package CloudCommerce\DeleteUnusedImages\Model
 */
class UnusedImagesCleaner
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * UnusedImagesCleaner constructor.
     * @param ResourceConnection $resource
     * @param File $file
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ResourceConnection $resource,
        File $file,
        DirectoryList $directoryList
    ) {
        $this->resource = $resource;
        $this->file = $file;
        $this->directoryList = $directoryList;
    }

    /**
     * Get unused images (limit batch size)
     * @param int $limit
     * @return array
     */
    public function getUnusedImages($limit = 500)
    {
        $connection = $this->resource->getConnection();
        $tableGallery = $this->resource->getTableName('catalog_product_entity_media_gallery');
        $tableLink = $this->resource->getTableName('catalog_product_entity_media_gallery_value_to_entity');

        $sql = "SELECT mg.value_id, mg.value 
                FROM {$tableGallery} mg
                LEFT JOIN {$tableLink} mve ON mg.value_id = mve.value_id
                WHERE mve.value_id IS NULL
                LIMIT {$limit}";

        return $connection->fetchAll($sql);
    }

    /**
     * Delete image file + db record
     * @param array $image
     */
    public function deleteImage($image)
    {
        $mediaPath = $this->directoryList->getPath('media') . '/catalog/product' . $image['value'];

        // Delete file if exists
        if ($this->file->fileExists($mediaPath)) {
            $this->file->rm($mediaPath);
        }

        // Delete DB entry
        $connection = $this->resource->getConnection();
        $galleryTable = $this->resource->getTableName('catalog_product_entity_media_gallery');
        $valueTable   = $this->resource->getTableName('catalog_product_entity_media_gallery_value');

        $connection->delete($valueTable, ['value_id = ?' => $image['value_id']]);
        $connection->delete($galleryTable, ['value_id = ?' => $image['value_id']]);
    }

    /**
     * Clean unused images (returns deleted count)
     * @param int $limit
     */
    public function clean($limit = 500)
    {
        $images = $this->getUnusedImages($limit);
        foreach ($images as $img) {
            $this->deleteImage($img);
        }
        return count($images);
    }
}
