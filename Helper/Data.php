<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Helper;

/**
 * Class Data
 *
 * @package Walkthechat\Walkthechat\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Code of attribute used in orders and products
     */
    const ATTRIBUTE_CODE = 'walkthechat_id';

    /**
     * Code of order name attribute used in orders
     */
    const ATTRIBUTE_NAME_CODE = 'walkthechat_name';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBackendBuilder;

    /**
     * @var string
     */
    protected $baseCurrencyCode;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\UrlInterface $urlBackendBuilder
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\UrlInterface $urlBackendBuilder,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig       = $scopeConfig;
        $this->urlBackendBuilder = $urlBackendBuilder;
        $this->regionFactory     = $regionFactory;
        $this->storeManager      = $storeManager;

        parent::__construct($context);
    }

    /**
     * Return x-token-access from configuration
     *
     * @return string
     */
    public function getToken()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/token');
    }

    /**
     * Return project id from configuration
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/project_id');
    }

    /**
     * Validates Project ID with instance configuration Project ID
     *
     * @param string $projectId
     *
     * @throws \Walkthechat\Walkthechat\Exception\InvalidMagentoInstanceException
     */
    public function validateProjectId($projectId)
    {
        if ($projectId !== $this->getProjectId()) {
            throw new \Walkthechat\Walkthechat\Exception\InvalidMagentoInstanceException(
                __('Invalid instance request. Project ID is not supported for current Magento instance.')
            );
        }
    }

    /**
     * Return shop name from configuration
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/shop_name');
    }

    /**
     * Return app ID from configuration
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/app_id');
    }

    /**
     * Return app key from configuration
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/app_key');
    }

    /**
     * Check if integration connected
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->getToken() ? true : false;
    }

    /**
     * Checks if all necessary data was set to try to connect
     *
     * @return boolean
     */
    public function canConnect()
    {
        return $this->getAppId() && $this->getAppKey();
    }

    /**
     * Get data source store view
     *
     * @return mixed
     */
    public function getStore()
    {
        $storeId = $this->scopeConfig->getValue('walkthechat_settings/sync/store');

        return $this->storeManager->getStore($storeId);
    }

    /**
     * Get data source website
     *
     * @return mixed
     */
    public function getWebsite()
    {
        $websiteId = $this->getStore()->getWebsiteId();

        return $this->storeManager->getWebsite($websiteId);
    }

    /**
     * Checks if product synchronisation is enabled
     *
     * @return boolean
     */
    public function isEnabledProductSync()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/sync/product_sync_active') ? true : false;
    }

    /**
     * Checks if order synchronisation is enabled
     *
     * @return boolean
     */
    public function isEnabledOrderSync()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/sync/order_sync_active') ? true : false;
    }

    /**
     * Checks if currency conversation is enabled
     *
     * @return boolean
     */
    public function isCurrencyConversionActive()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/currency/conversion_active') ? true : false;
    }

    /**
     * Checks if table rate shipping is enabled
     *
     * @return boolean
     */
    public function isTableRateActive()
    {
        return $this->scopeConfig->getValue('carriers/tablerate/active') ? true : false;
    }

    /**
     * Return table rate condition name
     *
     * @return string
     */
    public function getTableRateConditionName()
    {
        return $this->scopeConfig->getValue('carriers/tablerate/condition_name');
    }

    /**
     * Return table rate condition rate
     *
     * @return string
     */
    public function getCurrencyConversionRate()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/currency/exchange_rate');
    }

    /**
     * Return table rate condition method
     *
     * @return string
     */
    public function getCurrencyConversionMethod()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/currency/round_method');
    }

    /**
     * Return product queue batch from configuration
     *
     * @return string
     */
    public function getProductQueueBatch()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/sync/product_queue_batch');
    }

    /**
     * Return image queue batch from configuration
     *
     * @return string
     */
    public function getImageQueueBatch()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/sync/image_queue_batch');
    }

    /**
     * Get URL for authorization
     *
     * @return string
     */
    public function getAuthUrl()
    {
        $redirectUrl = $this->urlBackendBuilder->getUrl('walkthechat/auth/confirm');
        $appKey      = $this->scopeConfig->getValue('walkthechat_settings/general/app_id');

        return $this->scopeConfig->getValue('walkthechat_settings/general/auth_url').'?redirectUri='.$redirectUrl.'&appId='.$appKey;
    }

    /**
     * Return API url
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue('walkthechat_settings/general/api_url');
    }

    /**
     * Convert price depending on method set in configuration
     *
     * @param float   $price
     * @param boolean $export
     *
     * @return float
     */
    public function convertPrice($price, $export = true)
    {
        if ($this->isCurrencyConversionActive()) {
            $rate = $this->getCurrencyConversionRate();

            if ($price && $rate) {
                if ($export) {
                    if ($this->getCurrencyConversionMethod() == 2) {
                        if ($price * $rate < 1) {
                            $price = round($price * $rate, 1) * 10;
                            $digit = (int)substr($price, -1);
                            if ($digit < 8) {
                                $price += 8 - $digit;
                            } elseif ($digit == 9) {
                                $price += 9;
                            }
                            $price = $price / 10;
                        } else {
                            $price = ceil($price * $rate);
                            $digit = (int)substr($price, -1);
                            if ($digit < 8) {
                                $price += 8 - $digit;
                            } elseif ($digit == 9) {
                                $price += 9;
                            }
                        }
                    } else {
                        $price = round($price * $rate);
                    }
                } else {
                    $price = round($price / $rate, 2);
                }
            }
        }

        return $price;
    }

    /**
     * Return Walk the chat ID form product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return string|null
     */
    public function getWalkTheChatAttributeValue(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $value = null;

        $walkTheChatIdAttribute = $product->getCustomAttribute(
            \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE
        );

        // try to fetch from loaded data first, if noting then make a separate request
        if ($walkTheChatIdAttribute instanceof \Magento\Framework\Api\AttributeValue) {
            $value = $walkTheChatIdAttribute->getValue();
        } else {
            /** @var \Magento\Catalog\Model\ResourceModel\Product $productResource */
            $productResource = $product->getResource();

            $value = $productResource->getAttributeRawValue(
                $product->getId(),
                \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
                $product->getStore()->getId()
            );
        }

        return is_string($value) ? $value : null;
    }

    /**
     * Checks if base store currency is differs to order currency
     *
     * @param string $orderCurrency
     *
     * @return bool
     */
    public function isDifferentCurrency($orderCurrency)
    {
        if (null === $this->baseCurrencyCode) {
            $this->baseCurrencyCode = $this->scopeConfig->getValue(
                \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                'default'
            );
        }

        return strtolower($orderCurrency) !== strtolower($this->baseCurrencyCode);
    }

    /**
     * Convert region name to region ID
     *
     * @param $name
     * @return null|integer
     */
    public function convertRegionNameToRegionId($name)
    {
        $regionId = null;

        $regions = [
            '北京市' => 'CN-BJ',
            '上海市' => 'CN-SH',
            '天津市' => 'CN-TJ',
            '重庆市' => 'CN-CQ',
            '广东省' => 'CN-GD',
            '江苏省' => 'CN-JS',
            '浙江省' => 'CN-ZJ',
            '福建省' => 'CN-FJ',
            '黑龙江省' => 'CN-HL',
            '吉林省' => 'CN-JL',
            '辽宁省' => 'CN-LN',
            '山东省' => 'CN-SD',
            '山西省' => 'CN-SX',
            '河北省' => 'CN-HE',
            '河南省' => 'CN-HN',
            '安徽省' => 'CN-AH',
            '江西省' => 'CN-JX',
            '陕西省' => 'CN-SN',
            '湖北省' => 'CN-HB',
            '湖南省' => 'CN-HN',
            '四川省' => 'CN-SC',
            '甘肃省' => 'CN-GS',
            '贵州省' => 'CN-GZ',
            '青海省' => 'CN-QH',
            '云南省' => 'CN-YN',
            '海南省' => 'CN-HI',
            '内蒙古自治区' => 'CN-NM',
            '广西壮族自治区' => 'CN-GX',
            '西藏自治区' => 'CN-XZ',
            '宁夏回族自治区' => 'CN-NX',
            '新疆维吾尔自治区' => 'CN-XJ',
            '台湾省' => 'CN-TW',
            '香港特别行政区' => 'CN-HK',
            '澳门特别行政区' => 'CN-MO'
        ];

        if (isset($regions[$name])) {
            $regionId = $this->regionFactory->create()->loadByCode($regions[$name], 'CN')->getId();
        }

        return $regionId;
    }
}
