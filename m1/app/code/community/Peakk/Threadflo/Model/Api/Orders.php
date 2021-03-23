<?php

class Peakk_Threadflo_Model_Api_Orders extends Peakk_Threadflo_Model_Api_Abstract
{

    /**
     * Threadflo API URL constants.
     */
    const API_ORDERS_URI = 'orders';
    const API_ORDER_URI = 'order';

    /**
     * Return API URL for order transactions
     *
     * @return string
     */
    private function getOrdersApiUrl()
    {
        return self::API_CONNECTOR_URL.self::API_ORDERS_URI;
    }

    /**
     * Return API URL for single order transaction.
     *
     * @param string $threadfloOrderId
     * @return string
     */
    private function getOrderApiUrl($threadfloOrderId = '')
    {
        return self::API_CONNECTOR_URL.self::API_ORDER_URI.($threadfloOrderId ? '/'.$threadfloOrderId : '');
    }

    /**
     * Place an order.
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function order($order)
    {
        $helper = Mage::helper('threadflo');
        $orderLines = array();

        foreach ($order->getItemsCollection() as $orderItem) {
            if ($orderItem->getThreadfloItemSku()) {
                $orderLines[] = array(
                    'sku' => (string)$orderItem->getThreadfloItemSku(),
                    'qty' => (int)$orderItem->getQtyOrdered(),
                    'price' => round($orderItem->getPrice(), 2)
                );
            }
        }

        if ($orderLines) {
            $address = $order->getShippingAddress() ? $order->getShippingAddress() : $order->getBillingAddress();
            $region = Mage::getModel('directory/region')->load($address->getRegionId());

            $orderData = array(
                'order_date' => date('Y-m-d'),
                'member_ref_no' => $order->getId(),
                'cust_first_name' => $address->getFirstname(),
                'cust_last_name' => $address->getLastname(),
                'cust_address_1' => $address->getStreet1(),
                'cust_address_2' => $address->getStreet2(),
                'cust_city' => $address->getCity(),
                'cust_state' => $region->getCode(),
                'cust_zipcode' => $address->getPostcode(),
                'cust_country' => $address->getCountry(),
                'platform_id' => $helper->getPlatformId(),
                'domain' => str_replace('index.php/', '', Mage::getUrl()),
                'order_lines' => $orderLines,
                'shipping_methods_id' => 1
            );

            $response = $this->send($this->getOrderApiUrl(), http_build_query($orderData));

            if ($response) {
                if ($response->error) {
                    return false;
                }
                $order->setThreadfloOrderId($response->threadflo_order_id);
                $order->setThreadfloOrderStatus(Peakk_Threadflo_Model_System_Config_Source_Order_Status::STATUS_PENDING);
                $order->save();

                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Import shipping tracking number(s) and create shipment for pending Threadflo orders.
     */
    public function importShippingTracking()
    {
        $unshippedOrders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('threadflo_order_status' , Peakk_Threadflo_Model_System_Config_Source_Order_Status::STATUS_PENDING)
            ->load();

        if ($unshippedOrders) {
            foreach ($unshippedOrders as $unshippedOrder) {
                Mage::getModel('threadflo/api_orders')->importShippingTracking($unshippedOrder);
            }
        }
    }
    
    /**
     * Import shipping tracking number and return true on successful shipment creation.
     */
    public function importShippingTracking($order)
    {
        $threadfloOrder = $this->send($this->getOrderApiUrl($order->getThreadfloOrderId()));
        $trackingUrl = $threadfloOrder ? $threadfloOrder->url_prefix : '';
        $trackingCompany = $threadfloOrder ? $threadfloOrder->shipping_name : '';
        $trackingNumber = $trackingUrl ? $this->getTrackingNumber($trackingUrl) : '';

        if ($trackingNumber) {
            $invoices = Mage::getModel('sales/order_invoice')->getCollection()
                ->setOrderFilter($order->getId())
                ->load();
            $isInvoicesExist = (count($invoices) > 1);

            if ($isInvoicesExist) {
                $shipments = Mage::getModel('sales/order_shipment')->getCollection()
                    ->setOrderFilter($order->getId())
                    ->load();
                $isShipmentsExist = (count($shipments) > 1)
                $threadfloOrderItems = Mage::getModel('sales/order_item')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId())
                    ->addFieldToFilter('threadflo_item_id', array('neq' => null))
                    ->addFieldToFilter('threadflo_item_id', array('neq' => ''))
                    ->load();
                $shipmentQtys = array();

                foreach ($threadfloOrderItems as $threadfloOrderItem) {
                    $itemQty = $threadfloOrderItem->getQtyOrdered() - $threadfloOrderItem->getQtyShipped() - $threadfloOrderItem->getQtyRefunded() - $threadfloOrderItem->getQtyCanceled();

                    $shipmentQtys[$threadfloOrderItem->getId()] = $itemQty;
                }

                $shipment = $order->prepareShipment($shipmentQtys);

                if ($isInvoicesExist && !$isShipmentsExist && $order->canShip() && $shipment) {
                    $shipment->register();

                    try {
                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($shipment)
                            ->addObject($shipment->getOrder())
                            ->save();

                        $shipment->sendEmail(true);
                    } catch (Mage_Core_Exception $e) {
                        Mage::logException($e);
                    }

                    $order->setThreadfloOrderStatus(Peakk_Threadflo_Model_System_Config_Source_Order_Status::STATUS_SHIPPED);

                    $order->save();
                } else {
                    $order->addStatusHistoryComment('Threadflo tracking: '.$trackingCompany.' '.$trackingNumber);
                    $order->setThreadfloOrderStatus(Peakk_Threadflo_Model_System_Config_Source_Order_Status::STATUS_PENDING_MANUAL_SHIPMENT);

                    $order->save();
                }
            }
        }
    }

    /**
     * Return tracking number and shipping company ID.
     *
     * @param string $trackingUrl
     * @return string
     */
    private function getTrackingNumber($trackingUrl)
    {
        // TODO extract tracking number from URL

        return $trackingUrl;
    }
    
}