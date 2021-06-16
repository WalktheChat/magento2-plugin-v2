<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class OrderImport
 *
 * @package Walkthechat\Walkthechat\Model
 */
class OrderImport implements \Walkthechat\Walkthechat\Api\OrderImportInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\Import\RequestValidator
     */
    protected $requestValidator;

    /**
     * @var \Walkthechat\Walkthechat\Model\OrderService
     */
    protected $orderService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * OrderImport constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\Import\RequestValidator  $requestValidator
     * @param \Walkthechat\Walkthechat\Model\OrderService             $orderService
     * @param \Psr\Log\LoggerInterface                                $logger
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory
     * @param \Walkthechat\Walkthechat\Api\OrderRepositoryInterface   $orderRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\Import\RequestValidator $requestValidator,
        \Walkthechat\Walkthechat\Model\OrderService $orderService,
        \Psr\Log\LoggerInterface $logger,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory,
        \Walkthechat\Walkthechat\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->requestValidator = $requestValidator;
        $this->orderService     = $orderService;
        $this->logger           = $logger;
        $this->helper           = $helper;
        $this->orderFactory     = $orderFactory;
        $this->orderRepository  = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function import(
        $id,
        $name,
        $email = '',
        $projectId,
        $customerId,
        $reference,
        $status,
        $fulfillmentStatus = '',
        $financialStatus,
        $draft,
        $refundable,
        $created,
        $modified,
        $sign,
        $payment = [],
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon = [],
        $checkSignature = true
    ) {
        $logError = false;

        try {
            $this->helper->validateProjectId($projectId);

            $data = $this->requestValidator->validate(
                $id,
                $name,
                $email,
                $projectId,
                $customerId,
                $reference,
                $status,
                $fulfillmentStatus,
                $financialStatus,
                $draft,
                $refundable,
                $created,
                $modified,
                $sign,
                $payment,
                $itemsToFulfill,
                $items,
                $deliveryAddress,
                $shippingRate,
                $tax,
                $total,
                $coupon,
                $checkSignature
            );

            $order = $this->orderService->processImport($data);

            $this->_saveOrder($id, $name, __('Order Imported'), \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS, $order->getEntityId());

            return json_encode([
                'error'    => false,
                'order_id' => $order->getEntityId(),
            ]);
        } catch (\Magento\Framework\Exception\ValidatorException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Walkthechat\Walkthechat\Exception\NotSynchronizedProductException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Walkthechat\Walkthechat\Exception\InvalidMagentoInstanceException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            $logError = true;
        }

        if (strtolower($financialStatus) === 'paid') {
            $this->_saveOrder($id, $name, $errorMessage, \Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS);
        }

        if ($logError) {
            $this->logger->error('Error during the WalkTheChat order import | ' . $errorMessage);
            $errorMessage = 'An error has been occurred. Please contact administrator for more information.';
        }

        return json_encode(
            [
                'error'    => false,
                'message'  => $errorMessage,
                'order_id' => null,
            ]
        );
    }

    private function _saveOrder($id, $name, $message, $status, $orderId = null)
    {
        try {
            $model = $this->orderRepository->getByWalkthechatId($id);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $model = $this->orderFactory->create();
            $model->setWalkthechatId($id);
            $model->setWalkthechatName($name);
        }

        $model->setMessage($message);
        $model->setStatus($status);
        if ($orderId) {
            $model->setOrderId($orderId);
        }

        $this->orderRepository->save($model);
    }
}
