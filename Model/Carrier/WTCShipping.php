<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Carrier;

/**
 * Class WTCShipping
 *
 * @package WalktheChat\Walkthechat\Model\Carrier
 */
class WTCShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    const WALKTHECHAT_SHIPPING_PRICE_KEY = 'walkthechat_shipping_price_key';

    /**
     * @var string
     */
    protected $_code = 'walkthechat';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->registry          = $registry;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['walkthechat' => $this->getConfigData('name')];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return bool|\Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!$this->registry->registry('walkthechat_payment_and_shipping_available')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier('walkthechat');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('walkthechat');
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->registry->registry(self::WALKTHECHAT_SHIPPING_PRICE_KEY) ? : 0;

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     * Allow tracking
     *
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }
}
