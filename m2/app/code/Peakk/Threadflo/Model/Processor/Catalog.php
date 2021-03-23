<?php

namespace Peakk\Threadflo\Model\Processor;

class Catalog
{

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * @var ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * Catalog constructor.
     *
     * @param \Peakk\Threadflo\Helper\Data $helper
     * @param \Peakk\Threadflo\Model\ItemFactory $itemFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Peakk\Threadflo\Helper\Data $helper,
        \Peakk\Threadflo\Model\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->_helper = $helper;
        $this->_itemFactory = $itemFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_directoryList = $directoryList;
    }

    /**
     * Create or update Threadflo product catalog.
     *
     * @return bool
     */
    public function createProducts()
    {
        $threadfloItems = $this->_itemFactory->create()->getCollection()->load();
        $simpleProductIds = array();
        $usedProductIds = array();
        $lastSku = '';
        $i = 0;
        $firstThreadfloItem = null;

        if ($threadfloItems) {
            foreach ($threadfloItems as $threadfloItem) {
                $currentSku = $threadfloItem->getParentSku();
                $isFirstItem = ($i == 0);
                $isLastItem = (count($threadfloItems) == ($i + 1));
                $configurableProductCreated = false;
                $configurableProduct = null;
                $simpleProduct = null;

                if (((!$isFirstItem && !$isLastItem) && ($currentSku != $lastSku))) {
                    $configurableProduct = $this->initConfigurableProduct($firstThreadfloItem, $simpleProductIds);
                    $configurableProduct = $this->updateImages($configurableProduct, $this->_helper->getImages($firstThreadfloItem));

                    $configurableProduct->save();

                    $configurableProductCreated = true;
                    $simpleProductIds = array();
                    $firstThreadfloItem = null;
                }

                if (!$firstThreadfloItem) {
                    $firstThreadfloItem = $threadfloItem;
                }

                $simpleProduct = $this->initSimpleProduct($threadfloItem);
                $simpleProduct = $this->updateImages($simpleProduct, $this->_helper->getImages($threadfloItem));

                $simpleProduct->save();

                $simpleProductIds[$simpleProduct->getId()] = $simpleProduct->getId();
                $lastSku = $currentSku;
                $i++;

                if ($isLastItem) {
                    if (!$configurableProductCreated) {
                        $configurableProduct = $this->initConfigurableProduct($firstThreadfloItem, $simpleProductIds);
                        $configurableProduct = $this->updateImages($configurableProduct, $this->_helper->getImages($firstThreadfloItem));

                        $configurableProduct->save();
                    }

                    return true;
                }

                if ($simpleProduct) {
                    $usedProductIds[] = $simpleProduct->getId();
                }

                if ($configurableProduct) {
                    $usedProductIds[] = $configurableProduct->getId();
                }
            }
        }

        $this->deleteProducts($usedProductIds);

        return false;
    }

    /**
     * Return product loaded by SKU or new if product does not exist.
     *
     * @param string $sku
     * @return \Magento\Catalog\Model\Product $product
     */
    private function getProductBySku($sku)
    {
        $product = $this->_productFactory->create()->loadByAttribute('sku', $sku);

        if ($product && $product->getId()) {
            return $this->_productFactory->create()->load($product->getId());
        }

        return $this->_productFactory->create();
    }

    /**
     * Set simple product data.
     *
     * @param \Peakk\Threadflo\Model\Item\ $threadfloItem
     * @return \Magento\Catalog\Model\Product
     */
    private function initSimpleProduct($threadfloItem)
    {
        $sku = $threadfloItem->getSku();
        $simpleProduct = $this->getProductBySku($sku);
        $isProductExists = $simpleProduct->getId() ? true : false;
        $newSimpleProductData = array();

        if (!$isProductExists) {
            $newSimpleProductData = array(
                'attribute_set_id' => 4,
                'type_id' => 'simple',
                'sku' => $sku,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
                'price' => $threadfloItem->getPrice(),
                'description' => $threadfloItem->getCategoryName() . '.',
                'short_description' => $threadfloItem->getItemName() . '.',
                'name' => $threadfloItem->getItemName().': '.$threadfloItem->getColorName().' '.$threadfloItem->getSizeName(),
                'url_key' => $simpleProduct->getUrlKey() ? $simpleProduct->getUrlKey() : $this->getUrlKey($threadfloItem),
                'tax_class_id' => 2,
                'website_ids' => $this->getWebsiteIds(),
                'threadflo_item_id' => $threadfloItem->getThreadfloItemId(),
                'threadflo_item_sku' => $threadfloItem->getSku(),
                'threadflo_item_color' => $this->_helper->getColorCode($threadfloItem->getColorName()),
                'threadflo_item_size' => $this->_helper->getSizeCode($threadfloItem->getSizeName()),
                'weight' => 0,
                'stock_data' => array(
                    'manage_stock' => 0,
                    'use_config_manage_stock' => 0,
                    'min_sale_qty' => 1,
                    'max_sale_qty' => 100,
                    'is_in_stock' => 1,
                    'qty' => 1
                )
            );

            $simpleProduct->setData($newSimpleProductData);
        } else {
            $newSimpleProductData['price'] = $threadfloItem->getPrice();

            $simpleProduct->addData($newSimpleProductData);
        }

        return $simpleProduct;
    }

    /**
     * Set configurable product data.
     *
     * @param \Peakk\Threadflo\Model\Item $firstThreadfloItem
     * @param array $simpleProductIds
     * @return \Magento\Catalog\Model\Product
     */
    private function initConfigurableProduct($firstThreadfloItem, $simpleProductIds)
    {
        $sku = $firstThreadfloItem->getParentSku();
        $configurableProduct = $this->getProductBySku($sku);
        $isProductExists = $configurableProduct->getId() ? true : false;
        $threadfloItemSizeAttr = $this->_productFactory->create()->getResource()->getAttribute('threadflo_item_size');
        $threadfloItemColorAttr = $this->_productFactory->create()->getResource()->getAttribute('threadflo_item_color');
        $newConfigurableProductData = array();

        if (!$isProductExists) {
            $newConfigurableProductData = array(
                'attribute_set_id' => 4,
                'type_id' => 'configurable',
                'sku' => $sku,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
                'price' => $firstThreadfloItem->getPrice(),
                'description' => $firstThreadfloItem->getCategoryName().' item.',
                'short_description' => $firstThreadfloItem->getItemName().'.',
                'name' => $firstThreadfloItem->getItemName(),
                'url_key' => $configurableProduct->getUrlKey() ? $configurableProduct->getUrlKey() : $this->getUrlKey($firstThreadfloItem),
                'threadflo_item_id' => $firstThreadfloItem->getThreadfloItemId(),
                'threadflo_item_sku' => $firstThreadfloItem->getParentSku(),
                'tax_class_id' => 2,
                'website_ids' => $this->getWebsiteIds(),
                'weight' => 0,
                'configurable_attributes_data' => array(
                    array(
                        'attribute_id' => $threadfloItemSizeAttr->getAttributeId()
                    ),
                    array(
                        'attribute_id' => $threadfloItemColorAttr->getAttributeId()
                    )
                ),
                'weight' => 0,
                'stock_data' => array(
                    'manage_stock' => 0,
                    'use_config_manage_stock' => 0
                )
            );

            $configurableProduct->setData($newConfigurableProductData);
        } else {
            $newConfigurableProductData['price'] = $firstThreadfloItem->getPrice();

            $configurableProduct->addData($newConfigurableProductData);
        }

        $configurableProduct->addData(array('associated_product_ids' => $simpleProductIds));

        return $configurableProduct;
    }

    /**
     * Sync product images without deleting existing data.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $images
     * @return \Magento\Catalog\Model\Product
     */
    private function updateImages($product, $images)
    {
        if ($images) {
            $isImagesExistInProduct = $this->isImagesExistInProduct($product);
            $usedImageFileNames = array();
            $i = 0;

            foreach ($images as $ctr => $threadfloItemImage) {
                $imageFileName = $this->getImageFileName($threadfloItemImage);

                if ($imageFileName) {
                    if (!$this->isImageExistsInProduct($product, $imageFileName)) {
                        $imageFile = fopen($threadfloItemImage->getUrl(), 'r');

                        if ($imageFile) {
                            file_put_contents($this->getMediaDir().$imageFileName, $imageFile);
                            $isFirstImage = $i == 0 && !$isImagesExistInProduct;

                            $product->addImageToMediaGallery($this->getMediaDir().$imageFileName, ($isFirstImage ? array('image', 'small_image', 'thumbnail') : null), true, false);

                            $i++;
                        }
                    }

                    $usedImageFileNames[] = $imageFileName;
                }
            }

            if ($isImagesExistInProduct && $usedImageFileNames) {
                $mediaGallery = $product->getMediaGallery();

                foreach ($mediaGallery['images'] as $existingImage) {
                    $existingImageFileName = substr($existingImage['file'], (strrpos($existingImage['file'], '/') + 1));
                    $isExistingImageUsed = false;

                    foreach ($usedImageFileNames as $usedImageFileName) {
                        $usedImageName = substr($usedImageFileName, strrpos($usedImageFileName, '/'), strrpos($usedImageFileName, '.'));

                        if (strpos($existingImageFileName, $usedImageName) !== false) {
                            $isExistingImageUsed = true;
                        }
                    }

                    if (!$isExistingImageUsed) {
                        $this->getMediaGalleryAttribute()->getBackend()->removeImage($product, $existingImage['file']);
                    }
                }
            }
        }

        return $product;
    }

    /**
     * Return true if product has images in gallery.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function isImagesExistInProduct($product)
    {
        $mediaGallery = $product->getMediaGallery();

        if ($mediaGallery) {
            foreach ($mediaGallery['images'] as $existingImage) {
                return $existingImage ? true : false;
            }
        }

        return false;
    }

    /**
     * Return true if image exists in product gallery.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $image
     * @return bool
     */
    private function isImageExistsInProduct($product, $image)
    {
        $imageName = substr($image, strrpos($image, '/'), strrpos($image, '.'));
        $mediaGallery = $product->getMediaGallery();

        if ($mediaGallery) {
            foreach ($mediaGallery['images'] as $existingImage) {
                $existingImageFileName = substr($existingImage['file'], (strrpos($existingImage['file'], '/') + 1));

                if (strpos($existingImageFileName, $imageName) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return media gallery product attribute.
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private function getMediaGalleryAttribute()
    {
        return $this->_productFactory->create()->getResource()->getAttribute('media_gallery');
    }

    /**
     * Return server path to media dir.
     *
     * @return string
     */
    private function getMediaDir()
    {
        return $this->_directoryList->getPath('media').'/';
    }

    /**
     * Remove domain and URI dirs to extract fileName from image URL.
     *
     * @param Item\Image $threadfloItemImage
     * @param string $sku
     * @param string $ctr
     * @return mixed
     */
    private function getImageFileName(\Peakk\Threadflo\Model\Item\Image $threadfloItemImage, $sku, $ctr = '')
    {
        return substr($threadfloItemImage->getUrl(), (strrpos($threadfloItemImage->getUrl(), '/') + 1));
    }

    /**
     * Return unique URL key for a Threadflo item.
     *
     * @param Item $threadfloItem
     * @return string
     */
    private function getUrlKey(\Peakk\Threadflo\Model\Item $threadfloItem)
    {
        $urlName = $threadfloItem->getItemName().' '.$threadfloItem->getThreadfloItemId().rand(11111,99999);

        return strtolower(str_replace(' ', '_', $urlName));
    }

    /**
     * Delete unused products.
     *
     * @param array $usedProductIds
     */
    private function deleteProducts($usedProductIds)
    {
        $threadfloProductsToDelete = $this->_productFactory->create()->getCollection()
            ->addFieldToFilter('threadflo_item_id', array('neq' => 'NULL'))
            ->addFieldToFilter('entity_id', array('nin' => $usedProductIds))
            ->load();

        if ($threadfloProductsToDelete) {
            $threadfloProductsToDelete->delete();
        }
    }

    /**
     * Return all available website IDs.
     *
     * @return array
     */
    private function getWebsiteIds()
    {
        $websites = $this->_storeManager->getWebsites();
        $websiteIds = array();

        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }

}