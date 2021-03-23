<?php

class Peakk_Threadflo_Model_Processor_Catalog
{

    /**
     * Create or update existing products.
     *
     * @return bool
     */
    public function createProducts()
    {
        $helper = Mage::helper('threadflo');
        $threadfloItems = Mage::getModel('threadflo/item')->getCollection()->load();
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
                    $configurableProduct = $this->updateImages($configurableProduct, $helper->getImages($firstThreadfloItem));

                    $configurableProduct->save();

                    $configurableProductCreated = true;
                    $usedProductIds = array();
                    $firstThreadfloItem = null;
                }

                if (!$firstThreadfloItem) {
                    $firstThreadfloItem = $threadfloItem;
                }

                $simpleProduct = $this->initSimpleProduct($threadfloItem);
                $simpleProduct = $this->updateImages($simpleProduct, $helper->getImages($threadfloItem));

                $simpleProduct->save();

                $simpleProductIds[$simpleProduct->getId()] = $simpleProduct->getId();
                $lastSku = $currentSku;
                $i++;

                if ($isLastItem) {
                    if (!$configurableProductCreated) {
                        $configurableProduct = $this->initConfigurableProduct($firstThreadfloItem, $simpleProductIds);
                        $configurableProduct = $this->updateImages($configurableProduct, $helper->getImages($firstThreadfloItem));

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
     * @return Mage_Catalog_Model_Product
     */
    private function getProductBySku($sku)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        if ($product && $product->getId()) {
            return Mage::getModel('catalog/product')->load($product->getId());
        }

        return Mage::getModel('catalog/product');
    }

    /**
     * Set simple product data.
     *
     * @param Peakk_Threadflo_ModelItem $threadfloItem
     * @return Mage_Catalog_Model_Product
     */
    private function initSimpleProduct($threadfloItem)
    {
        $helper = Mage::helper('threadflo');
        $sku = $threadfloItem->getSku();
        $simpleProduct = $this->getProductBySku($sku);
        $isProductExists = $simpleProduct->getId() ? true : false;
        $newSimpleProductData = array();

        if (!$isProductExists) {
            $newSimpleProductData = array(
                'attribute_set_id' => 4,
                'type_id' => 'simple',
                'sku' => $sku,
                'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,
                'price' => $threadfloItem->getPrice(),
                'description' => $threadfloItem->getCategoryName().'.',
                'short_description' => $threadfloItem->getItemName().'.',
                'name' => $threadfloItem->getItemName().': '.$threadfloItem->getColorName().' '.$threadfloItem->getSizeName(),
                'url_key' => $simpleProduct->getUrlKey() ? $simpleProduct->getUrlKey() : $this->getUrlKey($threadfloItem),
                'tax_class_id' => 2,
                'website_ids' => $this->getWebsiteIds(),
                'threadflo_item_id' => $threadfloItem->getThreadfloItemId(),
                'threadflo_item_sku' => $threadfloItem->getSku(),
                'threadflo_item_color' => $helper->getColorCode($threadfloItem->getColorName()),
                'threadflo_item_size' => $helper->getSizeCode($threadfloItem->getSizeName()),
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
     * @param Peakk_Threadflo_Model_Item $firstThreadfloItem
     * @param array $simpleProductIds
     * @return Mage_Catalog_Model_Product
     */
    private function initConfigurableProduct($firstThreadfloItem, $simpleProductIds)
    {
        $sku = $firstThreadfloItem->getParentSku();
        $configurableProduct = $this->getProductBySku($sku);
        $isProductExists = $configurableProduct->getId() ? true : false;
        $threadfloItemColorAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('threadflo_item_color');
        $threadfloItemSizeAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('threadflo_item_size');
        $newConfigurableProductData = array();
        $configurableProductsData = array();

        if (!$isProductExists) {
            $newConfigurableProductData = array(
                'attribute_set_id' => 4,
                'type_id' => 'configurable',
                'sku' => $sku,
                'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
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
                $threadfloItemSizeAttr->getAttributeId(),
                'configurable_attributes_data' => array(
                    array(
                        'attribute_id' => $threadfloItemColorAttr->getAttributeId()
                    ),
                    array(
                        'attribute_id' => $threadfloItemSizeAttr->getAttributeId()
                    )
                ),
                'stock_data' => array(
                    'manage_stock' => 0,
                    'use_config_manage_stock' => 0,
                    'is_in_stock' => 1
                )
            );

            $configurableProduct->setData($newConfigurableProductData);

            $configurableProduct->getTypeInstance()->setUsedProductAttributeIds(array($threadfloItemColorAttr->getAttributeId(), $threadfloItemSizeAttr->getAttributeId()));
            $configurableAttributesData = $configurableProduct->getTypeInstance()->getConfigurableAttributesAsArray();
            $configurableProduct->setCanSaveConfigurableAttributes(true);
            $configurableProduct->setConfigurableAttributesData($configurableAttributesData);
        } else {
            $newConfigurableProductData['price'] = $firstThreadfloItem->getPrice();

            $configurableProduct->addData($newConfigurableProductData);
        }

        foreach ($simpleProductIds as $simpleProductId) {
            $simpleProduct = Mage::getModel('catalog/product')->load($simpleProductId);

            $configurableProductsData[$simpleProductId] = array(
                '0' => array(
                    'label' => $threadfloItemSizeAttr->getLabel(),
                    'attribute_id' => $threadfloItemColorAttr->getAttributeId(),
                    'value_index' => (int)$simpleProduct->getThreadfloColor(),
                    'is_percent' => '0',
                    'pricing_value' => $simpleProduct->getPrice()
                ),
                '1' => array(
                    'label' => $threadfloItemSizeAttr->getLabel(),
                    'attribute_id' => $threadfloItemSizeAttr->getAttributeId(),
                    'value_index' => (int)$simpleProduct->getThreadfloSize(),
                    'is_percent' => '0',
                    'pricing_value' => $simpleProduct->getPrice()
                )
            );
        }

        $configurableProduct->setConfigurableProductsData($configurableProductsData);

        return $configurableProduct;
    }

    /**
     * Sync product images without deleting existing data.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $images
     * @return Mage_Catalog_Model_Product
     */
    private function updateImages($product, $images)
    {
        if ($images) {
            $isImagesExistInProduct = $this->isImagesExistInProduct($product);
            $galleryAttribute = $this->getMediaGalleryAttribute($product);
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
                        $galleryAttribute->getBackend()->removeImage($product, $existingImage['file']);
                    }
                }
            }
        }

        return $product;
    }

    /**
     * Return true if product has images in gallery.
     *
     * @param Mage_Catalog_Model_Product $product
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
     * @param Mage_Catalog_Model_Product $product
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
     * Return media gallery product attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Eav_Model_Attribute
     */
    private function getMediaGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance()->getSetAttributes();

        if (isset($attributes['media_gallery'])) {
            return $attributes['media_gallery'];
        }

        return null;
    }

    /**
     * Return server path to media dir.
     *
     * @return string
     */
    private function getMediaDir()
    {
        return Mage::getBaseDir('media').'/';
    }

    /**
     * Remove domain and URI dirs to extract fileName from image URL.
     *
     * @param Peakk_Threadflo_Model_Item_Image $threadfloItemImage
     * @return string
     */
    private function getImageFileName($threadfloItemImage)
    {
        return substr($threadfloItemImage->getUrl(), (strrpos($threadfloItemImage->getUrl(), '/') + 1));
    }

    /**
     * Return unique URL key for a Threadflo item.
     *
     * @param Peakk_Threadflo_Model_Item $threadfloItem
     * @return string
     */
    private function getUrlKey($threadfloItem)
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
        $threadfloProductsToDelete = Mage::getModel('catalog/product')->getCollection()
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
        $websites = Mage::app()->getWebsites();
        $websiteIds = array();

        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }

}