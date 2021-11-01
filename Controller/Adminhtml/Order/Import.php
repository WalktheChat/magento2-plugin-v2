<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Order;

/**
 * Class Import
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Order
 */
class Import extends \Magento\Backend\App\Action
{
    /**
     * @var \Walkthechat\Walkthechat\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Walkthechat\Walkthechat\Service\OrdersRepository
     */
    protected $ordersRepository;

    /**
     * @var \Walkthechat\Walkthechat\Model\OrderImport
     */
    protected $orderImport;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Ui\Component\MassAction\Filter                 $filter
     * @param \Walkthechat\Walkthechat\Api\OrderRepositoryInterface   $orderRepository
     * @param \Walkthechat\Walkthechat\Service\OrdersRepository       $ordersRepository
     * @param \Walkthechat\Walkthechat\Model\OrderImport              $orderImport
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Magento\Store\Model\App\Emulation                      $emulation
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Walkthechat\Walkthechat\Api\OrderRepositoryInterface $orderRepository,
        \Walkthechat\Walkthechat\Service\OrdersRepository $ordersRepository,
        \Walkthechat\Walkthechat\Model\OrderImport $orderImport,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\Store\Model\App\Emulation $emulation
    ) {
        $this->orderRepository  = $orderRepository;
        $this->ordersRepository = $ordersRepository;
        $this->orderImport      = $orderImport;
        $this->helper           = $helper;
        $this->emulation        = $emulation;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->orderRepository->getById($id);

        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
        } elseif ($model->getStatus() == \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS) {
            $this->messageManager->addErrorMessage(__('This order was already imported.'));
        } else {
            try {
                $data = $this->ordersRepository->get($model->getWalkthechatId());

                $this->emulation->startEnvironmentEmulation($this->helper->getStore()->getStoreId(), 'frontend');

                $response = $this->orderImport->import(
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
                    isset($data['customerIdCard']) ? $data['customerIdCard'] : [],
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

                $response = json_decode($response, true);
                if ($response['order_id']) {
                    $this->messageManager->addSuccessMessage(__('Order imported'));
                } else {
                    $this->messageManager->addErrorMessage(__('Order not imported'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('walkthechat/dashboard/orders');
    }
}
