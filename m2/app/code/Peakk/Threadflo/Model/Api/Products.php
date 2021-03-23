<?php

namespace Peakk\Threadflo\Model\Api;

class Products extends \Peakk\Threadflo\Model\Api\ServiceAbstract
{

    /**
     * API URL constants.
     */
    const API_PRODUCTS_URI = 'products';
    const API_PRODUCT_URI = 'product';

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * @var ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Peakk\Threadflo\Model\Item\ImageFactory
     */
    protected $_itemImageFactory;

    /**
     * Products constructor.
     *
     * @param \Peakk\Threadflo\Helper\Data $helper
     * @param \Peakk\Threadflo\Model\ItemFactory $itemFactory
     * @param \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory
     */
    public function __construct(
        \Peakk\Threadflo\Helper\Data $helper,
        \Peakk\Threadflo\Model\ItemFactory $itemFactory,
        \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory
    ) {
        $this->_helper = $helper;
        $this->_itemFactory = $itemFactory;
        $this->_itemImageFactory = $itemImageFactory;
    }

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
     * Return API URL for single product transactions.
     *
     * @param int $threadfloProductId
     * @return string
     */
    private function getProductApiUrl($threadfloProductId)
    {
        return self::API_CONNECTOR_URL.self::API_PRODUCT_URI.'/'.$threadfloProductId;
    }

    /**
     * Import product items.
     *
     * @return bool
     */
    public function importItems()
    {
        $items = $this->send($this->getProductsApiUrl());

        if (isset($items)) {
            foreach ($items->item->item as $item) {
                $threadfloItemId = (int)$item->id;
                $itemName = (string)$item->name;
                $parentSku = (string)$item->parent_sku;
                $threadfloProduct = $this->send($this->getProductApiUrl($threadfloItemId));

                if (!$threadfloProduct) {
                    $threadfloItemModel = $this->_itemFactory->create()->load($threadfloItemId, 'threadflo_item_id');

                    if ($threadfloItemModel) {
                        $threadfloItemModel->delete();

                        $this->_helper->log('Threadflo Item with ID '.$threadfloItemId.' deleted.');

                        continue;
                    }
                } else {
                    foreach ($threadfloProduct->item_variants->item_variant as $threadfloItem) {
                        $sku = (string)$threadfloItem->sku;
                        $categoryName = (string)$threadfloProduct->category_name;
                        $colorName = (string)$threadfloItem->color_name;
                        $sizeName = (string)$threadfloItem->size_name;
                        $price = (double)$threadfloProduct->unit_price;

                        $threadfloItemModel = $this->_itemFactory->create();
                        $threadfloItemModel->load($sku, 'sku');

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

                            $threadfloItemImageModel = $this->_itemImageFactory->create();

                            $newItemImageData = array(
                                'threadflo_item_id' => $threadfloItemModel->getId(),
                                'name' => $imageName,
                                'url' => $imageUrl
                            );

                            $threadfloItemImageModel->setData($newItemImageData)->save();
                        }

                        $this->_helper->log('Product with Threadflo Item ID '.$threadfloItemId.' imported.');
                    }
                }
            }

            $this->_helper->log('Threadflo design sync complete.');

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

    }
    
}