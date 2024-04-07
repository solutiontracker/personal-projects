<?php
namespace App\Eventbuizz\EBObject;

/**
 * Class EBOrderItemVoucherDiscount
 * @package App\Eventbuizz\EBObject
 */
class EBOrderItemVoucherDiscount implements Contracts\BillingItemDiscountTypeInterface
{
    /**
     * @var \App\Models\BillingVoucher|\Illuminate\Database\Eloquent\Model|null
     */
    protected $_model;
    /**
     * @var float|int
     */
    protected $_discount_amount;
    /**
     * @var int
     */
    protected $_discount_quantity;
    /**
     * @var int
     */
    protected $_discount_type;
    /**
     * @var mixed
     */
    protected $_code;
    /**
     * @var
     */
    protected $_type;

    public $t;

    /**
     * EBOrderItemVoucherDiscount constructor.
     * @param EBOrderItemDiscount $discount
     * @throws \Exception
     */
    public function __construct(EBOrderItemDiscount $discount)
    {
        $coupon_id = $discount->getItem()->getOrder()->getModelAttribute('coupon_id');

        $event_id = $discount->getItem()->getOrder()->getOrderEventId();

        if ($discount->getItem()->getOrder()->isOrderPlaced()) {
            // In case of placed order and voucher Deleted/Expired after order placed. will search event in deleted records hence used withTrashed method here
            $voucher = $this->_model = \App\Models\BillingVoucher::where('event_id', $event_id)->where('id', $coupon_id)->where('type', 'billing_items')->withTrashed()->first();
        } else {
            $voucher = $this->_model = \App\Models\BillingVoucher::where('event_id', $event_id)->where('id', $coupon_id)->where('type', 'billing_items')->first();
        }

        if (!$voucher instanceof \App\Models\BillingVoucher) {
            throw new \Exception('voucher does not exists in this event.');
        }

        $this->_model = $voucher;

        $limits = $discount->getItem()->getOrder()->getItemVoucherUsageLimits();

        $this->_code = $this->_model->code;

        $itemRules = $this->_model->load('items')->items;

        $voucher_item = new \stdClass();

        foreach ($itemRules as $rule) {
            if ($rule->item_id == $discount->getItem()->getBillingItemId()) {
                $voucher_item = $rule;
                break;
            }
        }

        if (!isset($rule->item_id)) {
            throw new \Exception('rule for this item does not exist.');
        }

        $this->_type = $voucher_item->discount_type;

        $itemId = $discount->getItem()->getBillingItemId();

        $voucherItemUsage = 1;

        if ((int)$voucher_item->useage == 0) {
            $remaining = $discount->getItem()->getQuantity();
            $voucherItemUsage = 0;
        } else {
            $remaining = $limits[$itemId]['limit'] - $limits[$itemId]['used'];
        }

        // Default discount_type = 0
        // If discount apply with quantity assigned value then we set value in discount_type
        $this->_discount_type = 0;
        
        if ($this->_type == 2) //Percentage type discount
        {
            if ($discount->getItem()->getOrder()->isEdit() && $remaining > 0) {
                if ($voucherItemUsage) {
                    $this->_discount_type = 2;
                    $remaining = (($remaining - $discount->getItem()->getQuantity()) <= 0) ? $remaining : $discount->getItem()->getQuantity();
                    $discount->getItem()->getOrder()->updateUsedCountForItemsVoucher($itemId, $remaining);
                }
                $this->_discount_amount = (($discount->getItem()->getBaseOrEarlyBirdPrice() * $voucher_item->price) / 100) * $remaining;
                $this->_discount_quantity = $remaining;
            } elseif ($discount->getItem()->getOrder()->isOrderPlaced()) {
                $this->_discount_amount = (($discount->getItem()->getBaseOrEarlyBirdPrice() * $voucher_item->price) / 100) * $discount->getItem()->getDiscountQuantity();
                $this->_discount_quantity = $discount->getItem()->getDiscountQuantity();
            } elseif ($remaining > 0) {
                if ($voucherItemUsage) {
                    $this->_discount_type = 2;
                    $remaining = (($remaining - $discount->getItem()->getQuantity()) <= 0) ? $remaining : $discount->getItem()->getQuantity();
                    $discount->getItem()->getOrder()->updateUsedCountForItemsVoucher($itemId, $remaining);
                }
                $this->_discount_amount = (($discount->getItem()->getBaseOrEarlyBirdPrice() * $voucher_item->price) / 100) * $remaining;
                $this->_discount_quantity = $remaining;
            } else {
                $this->_discount_amount = 0;
                $this->_discount_quantity = 0;
            }
        } else if ($this->_type == 1) //Price type discount
        {
            if ($discount->getItem()->getOrder()->isEdit() && $remaining > 0) {
                if ($voucherItemUsage) {
                    $this->_discount_type = 1;
                    $remaining = (($remaining - $discount->getItem()->getQuantity()) <= 0) ? $remaining : $discount->getItem()->getQuantity();
                    $discount->getItem()->getOrder()->updateUsedCountForItemsVoucher($itemId, $remaining);
                }
                $this->_discount_amount = $voucher_item->price * $remaining;
                $this->_discount_quantity = $remaining;
            } elseif ($discount->getItem()->getOrder()->isOrderPlaced()) {
                $this->_discount_amount = $voucher_item->price * $discount->getItem()->getDiscountQuantity();
                $this->_discount_quantity = $discount->getItem()->getDiscountQuantity();
            } elseif ($remaining > 0) {
                if ($voucherItemUsage) {
                    $this->_discount_type = 1;
                    $remaining = (($remaining - $discount->getItem()->getQuantity()) <= 0) ? $remaining : $discount->getItem()->getQuantity();
                    $discount->getItem()->getOrder()->updateUsedCountForItemsVoucher($itemId, $remaining);
                }
                $this->_discount_amount = $voucher_item->price * $remaining;
                $this->_discount_quantity = $remaining;
            } else {
                $this->_discount_amount = 0;
                $this->_discount_quantity = 0;
            }
        } else {
            throw new \Exception('unknown billing item voucher discount type.');
        }
    }

    /**
     * @return \App\Models\BillingVoucher|\Illuminate\Database\Eloquent\Model|null
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return float|int
     */
    public function getDiscountAmount()
    {
        return $this->_discount_amount;
    }

    /**
     * @return int
     */
    public function getDiscountQuantity()
    {
        return $this->_discount_quantity;
    }

    public function replicateModel()
    {
        $this->_model = $this->_model->replicate();
    }

    /**
     * @return \App\Models\BillingVoucher|\Illuminate\Database\Eloquent\Model|null
     */
    public function getVoucher()
    {
        return $this->_model;
    }

    /**
     * @return int|0|1|2
     */
    public function getDiscountType()
    {
        return $this->_discount_type;
    }
}
