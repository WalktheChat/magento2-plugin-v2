<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class OrderService
 *
 * @package Walkthechat\Walkthechat\Model
 */
class OrderService
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteManagement \
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterface[]
     */
    protected $preparedQuoteItems;

    /**
     * @var string
     */
    protected $orderCurrencyCode;

    /**
     * @var string
     */
    protected $baseCurrencyCode;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductType;

    /**
     * OrderService constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager
     * @param \Magento\Quote\Model\QuoteFactory               $quoteFactory
     * @param \Magento\Quote\Model\QuoteManagement            $quoteManagement
     * @param \Magento\Sales\Model\OrderRepository            $orderRepository
     * @param \Magento\Catalog\Model\ProductRepository        $productRepository
     * @param \Magento\Framework\Registry                     $registry
     * @param \Walkthechat\Walkthechat\Helper\Data                $helper
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface      $cartRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Registry $registry,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
    ) {
        $this->storeManager        = $storeManager;
        $this->quoteFactory        = $quoteFactory;
        $this->quoteManagement     = $quoteManagement;
        $this->orderRepository     = $orderRepository;
        $this->productRepository   = $productRepository;
        $this->registry            = $registry;
        $this->helper              = $helper;
        $this->orderItemRepository = $orderItemRepository;
        $this->cartRepository      = $cartRepository;
        $this->configurableProductType  = $configurableProductType;
    }

    /**
     * Create/update order
     *
     * @param $data
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Walkthechat\Walkthechat\Exception\NotSynchronizedProductException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processImport($data)
    {
        $quote = $this->initQuote($data);

        $this->addProductsIntoQuote($quote, $data);
        $this->proceedQuote($quote, $data);

        $order = $this->quoteManagement->submit($quote);

        if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
            $this->setOrderTotals($order, $quote);

            $order
                ->setWalkthechatId($data['id'])
                ->setWalkthechatName($data['name'])
                ->setEmailSent(0);

            $this->orderRepository->save($order);
        }

        return $order;
    }

    /**
     * Prepare order data for API
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    public function prepareOrderData(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */

        $data = [
            'is_canceled' => $this->checkCancellation($order),
            'parcels'     => $this->checkShipments($order),
            'refunds'     => $this->checkRefund($order),
        ];

        return $data;
    }

    /**
     * Check shipments and return parcel data if exist
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    protected function checkShipments(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */

        $shipmentCollection = $order->getShipmentsCollection();

        $data = [];

        foreach ($shipmentCollection->getItems() as $parcel) {
            // don't send already sent parcels
            if (!$parcel->getIsSentToWalkTheChat()) {
                // set default values in case tracks were not set
                $data[$parcel->getEntityId()]['data'] = [
                    'id'             => $order->getWalkthechatId(),
                    'trackingNumber' => null,
                    'carrier'        => null,
                ];

                foreach ($parcel->getTracks() as $track) {
                    $data[$parcel->getEntityId()]['data'] = [
                        'id'             => $order->getWalkthechatId(),
                        'trackingNumber' => $track->getTrackNumber(),
                        'carrier'        => $track->getTitle(),
                    ];

                    break; // take only first tracking number
                }

                // prepare parcel items before send to WalkTheChat
                foreach ($parcel->getItems() as $item) {
                    $orderItem                = $this->orderItemRepository->get($item->getOrderItemId());
                    $walkTheChatOrderItemData = json_decode($orderItem->getData('walkthechat_item_data'), true);

                    $walkTheChatOrderItemData['quantity'] = $item->getQty();

                    $data[$parcel->getEntityId()]['data']['items'][] = $walkTheChatOrderItemData;
                }

                $data[$parcel->getEntityId()]['entity'] = $parcel;
            }
        }

        return $data;
    }

    /**
     * Check if order was canceled
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return bool
     */
    protected function checkCancellation(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $order->getState() === \Magento\Sales\Model\Order::STATE_CANCELED;
    }

    /**
     * Check if order was refunded
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    protected function checkRefund(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */

        $data       = [];
        $collection = $order->getCreditmemosCollection();

        foreach ($collection->getItems() as $creditMemo) {
            // don't send already sent parcels
            if (!$creditMemo->getIsSentToWalkTheChat()) {
                $comments = [];

                foreach ($creditMemo->getComments() as $comment) {
                    $comments[] = $comment->getComment();
                }

                $groupComment = implode("\n", $comments);

                $amount = $creditMemo->getBaseGrandTotal();

                if ($this->helper->isDifferentCurrency($order->getOrderCurrencyCode())) {
                    $amount = $creditMemo->getGrandTotal();
                }

                $data[$creditMemo->getEntityId()]['data'] = [
                    'orderId' => $order->getWalkthechatId(),
                    'amount'  => $amount,
                    'comment' => $groupComment,
                ];

                $data[$creditMemo->getEntityId()]['entity'] = $creditMemo;
            }
        }

        return $data;
    }

    /**
     * Initialize quote
     *
     * @param array $data
     *
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initQuote(array $data)
    {
        $store = $this->storeManager->getStore();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();

        $quote->setCheckoutMethod(\Magento\Quote\Api\CartManagementInterface::METHOD_GUEST);

        $quote->setStore($store);
        $quote->setCurrency();

        $quote->setCustomerIsGuest(true);
        $quote->setCustomerEmail($data['id'].'@walkthechat.com');
        $quote->setCustomerTaxvat($data['tax']['rate']);

        $addressData = $this->prepareAddressData($data);

        $quote->getBillingAddress()->addData($addressData);
        $quote->getShippingAddress()->addData($addressData);

        // set shipping price in the shipping career
        $this->registry->register(
            \Walkthechat\Walkthechat\Model\Carrier\WTCShipping::WALKTHECHAT_SHIPPING_PRICE_KEY,
            (float)$data['shippingRate']['rate']
        );

        // make walkthechat payment and shipping available
        $this->registry->register('walkthechat_payment_and_shipping_available', true);

        return $quote;
    }

    /**
     * Add product into the cart
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array                                 $data
     *
     * @throws \Walkthechat\Walkthechat\Exception\NotSynchronizedProductException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addProductsIntoQuote(\Magento\Quote\Api\Data\CartInterface $quote, array $data)
    {
        /** @var \Magento\Quote\Model\Quote $quote */

        $this->orderCurrencyCode = $data['total']['currency'];

        foreach ($data['items']['products'] as $k => $item) {
            try {
                $product = $this->productRepository->get($item['variant']['sku']);

                if ($this->helper->getWalkTheChatAttributeValue($product) !== $item['product']['id']) {
                    $parentIds = $this->configurableProductType->getParentIdsByChild($product->getId());

                    if (count($parentIds)) {
                        $parent = $this->productRepository->getById($parentIds[0]);
                        if ($this->helper->getWalkTheChatAttributeValue($parent) !== $item['product']['id']) {
                            throw new \Magento\Framework\Exception\NoSuchEntityException();
                        }
                    } else {
                        throw new \Magento\Framework\Exception\NoSuchEntityException();
                    }
                }

                $qty            = $item['quantity'];
                $discountAmount = $qty * $item['variant']['discount'];
                $price          = $item['variant']['price'];
                $rowTotal       = $qty * $price;

                $quoteItem = $quote->addProduct($product, $qty);

                $quoteItem->setOriginalPrice($price);

                if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
                    $quoteItem->setBaseOriginalPrice($this->helper->convertPrice($price, false));
                }

                $quoteItem->setPrice($price);

                if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
                    $quoteItem->setBasePrice($this->helper->convertPrice($price, false));
                }

                $quoteItem->setRowTotal($rowTotal);

                if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
                    $quoteItem->setBaseRowTotal($this->helper->convertPrice($rowTotal, false));
                }

                $quoteItem->setDiscountAmount($discountAmount);

                if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
                    $quoteItem->setBaseDiscountAmount($this->helper->convertPrice($discountAmount, false));
                }

                if ((float)$data['total']['grandTotal']['tax']) {
                    $quoteItem->setTaxPercent($data['tax']['rate'] * 100);

                    $taxAmount = $qty * ((float)$item['variant']['priceWithDiscount'] * (float)$data['tax']['rate']);

                    $quoteItem->setTaxAmount($taxAmount);
                    $quoteItem->setBaseTaxAmount($taxAmount);

                    if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
                        $quoteItem->setBaseTaxAmount($this->helper->convertPrice($taxAmount, false));
                    }
                }

                // set array data to save it into the order entity
                // so it can be used when order is canceled, refunded or shipped
                // and Magento -> WalkTheChat request can be filled properly
                $quoteItem->setData(
                    'walkthechat_item_data',
                    json_encode($data['itemsToFulfill'][$k], JSON_UNESCAPED_UNICODE)
                );

                $this->preparedQuoteItems[$product->getSku()] = clone $quoteItem;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                throw new \Walkthechat\Walkthechat\Exception\NotSynchronizedProductException(
                    __(
                        'Not synchronized product was sent. Product with WalkTheChat ID: %1, wasn\'t exported from current Magento instance.',
                        $item['product']['id']
                    )
                );
            }
        }
    }

    /**
     * Proceed payment, shipping, add totals
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array                                 $data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function proceedQuote(\Magento\Quote\Api\Data\CartInterface $quote, array $data)
    {
        $shippingAmount       = $data['total']['grandTotal']['shipping'];
        $subTotal             = $data['total']['grandTotal']['totalWithoutDiscountAndTax'];
        $subTotalWithDiscount = $data['total']['grandTotal']['totalWithoutTax'];
        $taxAmount            = $data['total']['grandTotal']['tax'];
        $discountAmount       = $data['total']['grandTotal']['discount'];
        $grandTotal           = $data['total']['grandTotal']['total'];

        /** @var \Magento\Quote\Model\Quote $quote */

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('walkthechat_walkthechat');

        $quote->setPaymentMethod('walkthechat');

        $quote
            ->getPayment()
            ->setQuote($quote)
            ->importData(['method' => 'walkthechat']);

        $quote->collectTotals();

        $quote->setShippingDescription('WalkTheChat - '.$data['shippingRate']['name']['en']);

        $quote->setShippingAmount($shippingAmount);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseShippingAmount($this->helper->convertPrice($shippingAmount, false));
        }

        $quote->setSubtotal($subTotal);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseSubtotal($this->helper->convertPrice($subTotal, false));
        }

        $quote->setSubtotalWithDiscount($subTotalWithDiscount);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseSubtotalWithDiscount($this->helper->convertPrice($subTotalWithDiscount, false));
        }

        $quote->setTaxAmount($taxAmount);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseTaxAmount($this->helper->convertPrice($taxAmount, false));
        }

        $quote->setDiscountAmount($discountAmount);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseDiscountAmount($this->helper->convertPrice($discountAmount, false));
        }

        if (isset($data['coupon']['amount'])) {
            $quote->setDiscountDescription('WTC Coupon: '.$data['coupon']['code']);
        }

        $quote->setGrandTotal($grandTotal);
        $quote->setBaseGrandTotal($grandTotal);

        if ($this->helper->isDifferentCurrency($this->orderCurrencyCode)) {
            $quote->setBaseGrandTotal($this->helper->convertPrice($grandTotal, false));
        }

        $this->cartRepository->save($quote);
    }

    /**
     * Copy totals from quote
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Quote\Api\Data\CartInterface  $quote
     */
    protected function setOrderTotals(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $rate = $this->helper->getCurrencyConversionRate();

        $order->setOrderCurrencyCode($this->orderCurrencyCode);
        $order->setBaseToOrderRate($rate);

        $order->setShippingAmount($quote->getShippingAmount());
        $order->setBaseShippingAmount($quote->getBaseShippingAmount());
        $order->setShippingDescription($quote->getShippingDescription());

        $order->setSubtotal($quote->getSubtotal());
        $order->setBaseSubtotal($quote->getBaseSubtotal());
        $order->setSubtotalWithDiscount($quote->getSubtotalWithDiscount());
        $order->setBaseSubtotalWithDiscount($quote->getBaseSubtotalWithDiscount());

        $order->setTaxAmount($quote->getTaxAmount());
        $order->setBaseTaxAmount($quote->getBaseTaxAmount());

        $order->setGrandTotal($quote->getGrandTotal());
        $order->setBaseGrandTotal($quote->getBaseGrandTotal());

        $order->setDiscountAmount($quote->getDiscountAmount());
        $order->setBaseDiscountAmount($quote->getBaseDiscountAmount());

        $order->setDiscountDescription($quote->getDiscountDescription());

        $order->setBaseTotalPaid($quote->getBaseGrandTotal());
        $order->setTotalPaid($quote->getGrandTotal());

        foreach ($order->getItems() as $item) {
            $quoteItem = $this->preparedQuoteItems[$item->getSku()];

            $item->setOriginalPrice($quoteItem->getOriginalPrice());
            $item->setBaseOriginalPrice($quoteItem->getBaseOriginalPrice());

            $item->setPrice($quoteItem->getPrice());
            $item->setBasePrice($quoteItem->getBasePrice());

            $item->setRowTotal($quoteItem->getRowTotal());
            $item->setBaseRowTotal($quoteItem->getBaseRowTotal());

            $item->setTaxPercent($quoteItem->getTaxPercent());

            $item->setDiscountAmount($quoteItem->getDiscountAmount());
            $item->setBaseDiscountAmount($quoteItem->getBaseDiscountAmount());

            $item->setTaxAmount($quoteItem->getTaxAmount());
            $item->setBaseTaxAmount($quoteItem->getBaseTaxAmount());

            $item->setData('walkthechat_item_data', $quoteItem->getData('walkthechat_item_data'));

            $this->orderItemRepository->save($item);
        }
    }

    /**
     * Prepare mapping for API address into magento one
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareAddressData(array $data)
    {
        $address = $data['deliveryAddress'];

        return [
            'firstname'            => mb_substr($address['name'], 1),
            'lastname'             => mb_substr($address['name'], 0, 1),
            'street'               => $address['address'].', '.$address['district'],
            'city'                 => $address['city'],
            'country_id'           => $address['countryCode'],
            'region'               => $address['province'],
            'region_id'            => $this->helper->convertRegionNameToRegionId($address['province']),
            'postcode'             => $address['zipcode'],
            'telephone'            => $address['phone'],
            'fax'                  => '',
            'save_in_address_book' => false,
        ];
    }
}
