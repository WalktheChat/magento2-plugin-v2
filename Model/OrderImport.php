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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * OrderImport constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\Import\RequestValidator  $requestValidator
     * @param \Walkthechat\Walkthechat\Model\OrderService             $orderService
     * @param \Psr\Log\LoggerInterface                                $logger
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory
     * @param \Walkthechat\Walkthechat\Api\OrderRepositoryInterface   $orderRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime             $date
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\Import\RequestValidator $requestValidator,
        \Walkthechat\Walkthechat\Model\OrderService $orderService,
        \Psr\Log\LoggerInterface $logger,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory,
        \Walkthechat\Walkthechat\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->requestValidator = $requestValidator;
        $this->orderService     = $orderService;
        $this->logger           = $logger;
        $this->helper           = $helper;
        $this->orderFactory     = $orderFactory;
        $this->orderRepository  = $orderRepository;
        $this->date             = $date;
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
        $customerIdCard = [],
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
        $alreadyImported = false;

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
                $customerIdCard,
                $itemsToFulfill,
                $items,
                $deliveryAddress,
                $shippingRate,
                $tax,
                $total,
                $coupon,
                $checkSignature
            );

            try {
                $syncOrder = $this->orderRepository->getByWalkthechatId($id);
                if ($syncOrder->getStatus() != \Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS) {
                    $alreadyImported = true;
                    throw new \Magento\Framework\Exception\AlreadyExistsException(
                        __('Import already initiated.')
                    );
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $syncOrder = $this->orderFactory->create();
                $syncOrder->setWalkthechatId($id);
                $syncOrder->setWalkthechatName($name);
                $syncOrder->setMessage(__('Import Started'));
                $syncOrder->setStatus(\Walkthechat\Walkthechat\Api\Data\OrderInterface::NEW_STATUS);

                $this->orderRepository->save($syncOrder);
            }

            $orderId = $this->orderService->processImport($data);

            $syncOrder->setMessage(__('Order Imported'));
            $syncOrder->setStatus(\Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS);
            $syncOrder->setOrderId($orderId);

            $this->orderRepository->save($syncOrder);

            return json_encode([
                'error'    => false,
                'order_id' => $orderId,
            ]);
        } catch (\Magento\Framework\Exception\ValidatorException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Walkthechat\Walkthechat\Exception\NotSynchronizedProductException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Walkthechat\Walkthechat\Exception\InvalidMagentoInstanceException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Magento\Framework\Exception\AlreadyExistsException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            $logError = true;
        }

        if ($id && $name && strtolower($financialStatus) === 'paid' && !$alreadyImported) {
            try {
                $syncOrder = $this->orderRepository->getByWalkthechatId($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $syncOrder = $this->orderFactory->create();
                $syncOrder->setWalkthechatId($id);
                $syncOrder->setWalkthechatName($name);
            }

            $syncOrder->setMessage($errorMessage);
            $syncOrder->setUpdatedAt($this->date->gmtDate());
            $syncOrder->setStatus(\Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS);

            $this->orderRepository->save($syncOrder);
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
}
