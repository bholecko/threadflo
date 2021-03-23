<?php

class Peakk_Threadflo_Model_Api_Products extends Peakk_Threadflo_Model_Api_Abstract
{

    /**
     * Threadflo API URL constants.
     */
    const API_PRODUCTS_URI = 'products';
    const API_PRODUCT_URI = 'product';

    /**
     * Return API URL for product transactions.
     *
     * @return string
     */
    private function getProductsApiUrl()
    {
        return self::API_CONNECTOR_URL.self::API_PRODUCTS_URI;
    }

    /**
     * Return API URL for single product transactions
     *
     * @param int $threadfloProductId
     * @return string
     */
    private function getProductApiUrl($threadfloProductId)
    {
        return self::API_CONNECTOR_URL.self::API_PRODUCT_URI.'/'.$threadfloProductId;
    }

    /**
     * Import Threadflo product items.
     *
     * @return bool
     */
    public function importItems()
    {
        $helper = Mage::helper('threadflo');
        $items = $this->send($this->getProductsApiUrl());

        if (isset($items)) {
            foreach ($items->item->item as $item) {
                $threadfloItemId = (int)$item->id;
                $itemName = (string)$item->name;
                $parentSku = (string)$item->parent_sku;
                $threadfloProduct = $this->send($this->getProductApiUrl($threadfloItemId));

                if (!$threadfloProduct) {
                    $threadfloItemModel = Mage::getModel('threadflo/item')->load($threadfloItemId, 'threadflo_item_id');

                    if ($threadfloItemModel) {
                        $threadfloItemModel->delete();

                        $helper->log('Product with Threadflo Item ID '.$threadfloItemId.' deleted.');

                        continue;
                    }
                } else {
                    foreach ($threadfloProduct->item_variants->item_variant as $threadfloItem) {
                        $sku = (string)$threadfloItem->sku;
                        $categoryName = (string)$threadfloProduct->category_name;
                        $colorName = (string)$threadfloItem->color_name;
                        $sizeName = (string)$threadfloItem->size_name;
                        $price = (double)$threadfloProduct->unit_price;

                        $threadfloItemModel = Mage::getModel('threadflo/item')->load($sku, 'sku');

                        $newItemData = array(
                            'entity_id' => $threadfloItemModel->getEntityId() ? $threadfloItemModel->getEntityId() : null,
                            'threadflo_item_id' => $threadfloItemId,
                            'item_name' => $itemName,
                            'category_name' => $categoryName,
                            'parent_sku' => $parentSku,
                            'sku' => $sku,
                            'color_name' => $colorName,
                            'size_name' => $sizeName,
                            'price' => $price
                        );

                        $threadfloItemModel->setData($newItemData)->save();

                        $threadfloItemModel->deleteImages();

                        foreach ($threadfloProduct->item_images->item_image as $threadfloItemImage) {
                            $imageName = (string)$threadfloItemImage->name;
                            $imageUrl = (string)$threadfloItemImage->url;
                            $threadfloItemImageModel = Mage::getModel('threadflo/item_image');

                            $newItemImageData = array(
                                'threadflo_item_id' => $threadfloItemModel->getId(),
                                'name' => $imageName,
                                'url' => $imageUrl
                            );

                            $threadfloItemImageModel->setData($newItemImageData)->save();
                        }

                        $helper->log('Product with Threadflo Item ID '.$threadfloItemId.' imported.');
                    }
                }
            }

            $helper->log('Threadflo design sync complete.');

            return true;
        }

        return false;
    }

    /**
     * Return array indicating if the quote items exist in Threadflo.
     *
     * @param Mage_Sales_Model_Resource_Quote_Item_Collection $quoteItems
     * @return array
     */
    public function itemsExist($quoteItems)
    {
        $itemsExistMapping = array();

        if ($quoteItems) {
            foreach ($quoteItems as $quoteItem) {
                $threadfloItemId = $quoteItem->getThreadfloItemId();

                if ($threadfloItemId) {
                    $threadfloProduct = $this->send($this->getProductApiUrl($threadfloItemId));

                    if ($threadfloProduct && $threadfloProduct->item_variants && $threadfloProduct->item_variants->item_variant && $threadfloProduct->item_variants->item_variant->sku) {
                        $itemsExist[$quoteItem->getItemId()] = true;
                    } else {
                        $itemsExist[$quoteItem->getItemId()] = false;
                    }
                }
            }
        }

        return $itemsExistMapping;
    }

}
