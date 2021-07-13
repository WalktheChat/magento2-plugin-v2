<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */
namespace Walkthechat\Walkthechat\Model\Quote\Item\QuantityValidator;

/**
 * Class QuoteItemQtyList
 * @package Walkthechat\Walkthechat\Model\Quote\Item\QuantityValidator
 */
class QuoteItemQtyList extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList
{
    /**
     * Get product qty includes information from all quote items.
     *
     * Need be used only in singleton mode.
     *
     * @param int $productId
     * @param int $quoteItemId
     * @param int $quoteId
     * @param float $itemQty
     *
     * @return int
     */
    public function getQty($productId, $quoteItemId, $quoteId, $itemQty)
    {
        $qty = $itemQty;
        if (isset(
                $this->_checkedQuoteItems[$quoteId][$productId]['qty']
            ) && !in_array(
                $quoteItemId,
                $this->_checkedQuoteItems[$quoteId][$productId]['items']
            )
        ) {
            $qty += $this->_checkedQuoteItems[$quoteId][$productId]['qty'];
        }

        // Fix https://github.com/magento/magento2/issues/25675
        if ($quoteItemId !== null) {
            $this->_checkedQuoteItems[$quoteId][$productId]['qty'] = $qty;
            $this->_checkedQuoteItems[$quoteId][$productId]['items'][] = $quoteItemId;
        }

        return $qty;
    }
}
