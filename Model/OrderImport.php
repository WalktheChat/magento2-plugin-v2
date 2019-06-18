<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model;

/**
 * Class OrderImport
 *
 * @package WalktheChat\Walkthechat\Model
 */
class OrderImport implements \WalktheChat\Walkthechat\Api\OrderImportInterface
{
    /**
     * @var \WalktheChat\Walkthechat\Model\Import\RequestValidator
     */
    protected $requestValidator;

    /**
     * @var \WalktheChat\Walkthechat\Model\OrderService
     */
    protected $orderService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * OrderImport constructor.
     *
     * @param \WalktheChat\Walkthechat\Model\Import\RequestValidator $requestValidator
     * @param \WalktheChat\Walkthechat\Model\OrderService            $orderService
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \WalktheChat\Walkthechat\Helper\Data                   $helper
     */
    public function __construct(
        \WalktheChat\Walkthechat\Model\Import\RequestValidator $requestValidator,
        \WalktheChat\Walkthechat\Model\OrderService $orderService,
        \Psr\Log\LoggerInterface $logger,
        \WalktheChat\Walkthechat\Helper\Data $helper
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
        $projectId,
        $financialStatus,
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
                $financialStatus,
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
        } catch (\WalktheChat\Walkthechat\Exception\NotSynchronizedProductException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\WalktheChat\Walkthechat\Exception\InvalidMagentoInstanceException $exception) {
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
