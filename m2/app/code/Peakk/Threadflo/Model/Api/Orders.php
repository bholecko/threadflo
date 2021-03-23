<?php

namespace Peakk\Threadflo\Model\Api;

class Orders extends \Peakk\Threadflo\Model\Api\ServiceAbstract
{

    /**
     * API URL constants.
     */
    const API_ORDERS_URI = 'orders';
    const API_ORDER_URI = 'order';

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * Orders constructor.
     *
     * @param \Peakk\Threadflo\Helper\Data $helper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Peakk\Threadflo\Helper\Data $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->_helper = $helper;
        $this->_orderFactory = $orderFactory;
        $this->_regionFactory = $regionFactory;
    }

    /**
     * Return API URL for order transactions.
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
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function order($order)
    {
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

            $region = $this->_regionFactory->create()->load($address->getRegionId());

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
                'platform_id' => $this->_helper->getPlatformId(),
                'domain' => $this->_helper->getDomain(),
                'order_lines' => $orderLines,
                'shipping_methods_id' => 1
            );

            $response = $this->send($this->getOrderApiUrl(), http_build_query($orderData));

            if ($response) {
                $order->setThreadfloOrderId($response->threadflo_order_id);
                $order->setThreadfloOrderStatus(\Peakk\Threadflo\Model\System\Config\Source\Order\Status::STATUS_PENDING);
                $order->save();

                return true;
            } else {
                return false;
            }
        }

        return true;
    }
    /**
     * Import order status values for threadflo_order_status.
     */
    public function importOrderStatus()
    {
        $orders = $this->send($this->getOrdersApiUrl());

        if (isset($orders)) {
            foreach ($orders->item->item as $order) {
                $threadfloOrderId = $order->threadflo_order_id;
                $threadfloOrderStatus = $order->order_status;
                $order = $this->_orderFactory->create()->load($threadfloOrderId, 'threadflo_order_id');

                if ($order && $order->getId() && $order->getThreadfloOrderStatus() == \Peakk\Threadflo\Model\System\Config\Source\Order\Status::STATUS_PENDING && $threadfloOrderStatus == \Peakk\Threadflo\Model\System\Config\Source\Order\Status::STATUS_SHIPPED) {
                    $order->setThreadfloOrderStatus('Shipped');
                    $order->addStatusHistoryComment('Threadflo order shipped.');

                    $order->save();
                }
            }
        }
    }

    /**
     * Import shipping tracking number and return true on successful shipment creation.
     *
     * @return bool
     */
    public function importShippingTracking($order)
    {

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