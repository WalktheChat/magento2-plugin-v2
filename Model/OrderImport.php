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
     * OrderImport constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\Import\RequestValidator $requestValidator
     * @param \Walkthechat\Walkthechat\Model\OrderService            $orderService
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \Walkthechat\Walkthechat\Helper\Data                   $helper
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\Import\RequestValidator $requestValidator,
        \Walkthechat\Walkthechat\Model\OrderService $orderService,
        \Psr\Log\LoggerInterface $logger,
        \Walkthechat\Walkthechat\Helper\Data $helper
    ) {
        $this->requestValidator = $requestValidator;
        $this->orderService     = $orderService;
        $this->logger           = $logger;
        $this->helper           = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function import(
        $id,
        $name,
        $email,
        $projectId,
        $financialStatus,
        $payment,
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon = []
    ) {
        try {
            $this->helper->validateProjectId($projectId);

            $data = $this->requestValidator->validate(
                $id,
                $name,
                $email,
                $financialStatus,
                $payment,
                $itemsToFulfill,
                $items,
                $deliveryAddress,
                $shippingRate,
                $tax,
                $total,
                $coupon
            );

            $order = $this->orderService->processImport($data);

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
            $this->logger->error('Error during the WalkTheChat order import | '.$exception->getMessage());

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
