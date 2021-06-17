<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Cron;

/**
 * Class SyncOrders
 *
 * @package Walkthechat\Walkthechat\Cron
 */
class SyncOrders
{
    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Service\OrdersRepository
     */
    protected $ordersRepository;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * @var \Walkthechat\Walkthechat\Model\OrderImport
     */
    protected $orderImport;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * SyncOrders constructor.
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param \Walkthechat\Walkthechat\Service\OrdersRepository $ordersRepository
     * @param \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\App\Emulation $emulation
     */
    public function __construct(
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Service\OrdersRepository $ordersRepository,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Walkthechat\Walkthechat\Model\OrderImport $orderImport,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\App\Emulation $emulation
    ) {
        $this->helper           = $helper;
        $this->ordersRepository = $ordersRepository;
        $this->orderFactory     = $orderFactory;
        $this->orderImport      = $orderImport;
        $this->date             = $date;
        $this->emulation        = $emulation;
    }

    /**
     *
     * @throws \Magento\Framework\Exception\CronException
     * @throws \Exception
     */
    public function execute()
    {
        $orders = $this->ordersRepository->find($this->date->gmtDate('Y-m-d\TH:00:00\Z', time() - 3600), $this->date->gmtDate('Y-m-d\TH:00:00\Z'));
        foreach ($orders as $data) {
            $order = $this->orderFactory->create()->load($data['id'], 'walkthechat_id');

            if (!$order->getId()) {
                $this->emulation->startEnvironmentEmulation($this->helper->getStore()->getStoreId(), 'frontend');

                $this->orderImport->import(
                    $data['id'],
                    $data['name'],
                    isset($data['email']) ? $data['email'] : '',
                    $data['projectId'],
                    $data['customerId'],
                    $data['reference'],
                    $data['status'],
                    isset($data['fulfillmentStatus']) ? $data['fulfillmentStatus'] : '',
                    $data['financialStatus'],
                    $data['draft'],
                    $data['refundable'],
                    $data['created'],
                    $data['modified'],
                    '',
                    isset($data['payment']) ? $data['payment'] : [],
                    $data['itemsToFulfill'],
                    $data['items'],
                    $data['deliveryAddress'],
                    $data['shippingRate'],
                    $data['tax'],
                    $data['total'],
                    isset($data['coupon']) ? $data['coupon'] : [],
                    false
                );

                $this->emulation->stopEnvironmentEmulation();
            }
        }
    }

    /**
     * Initialize area code
     */
    protected function initAreaCode()
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        } catch (\Exception $exception) {
            // if area code was already set, then just continue work...
        }
    }
}
