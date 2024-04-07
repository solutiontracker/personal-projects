<?php
namespace App\Eventbuizz\EBObject;

use App\Events\RegistrationFlow\Event;

class EBOrderItem
{
    protected $_model;

    protected $_order;

    protected $_itemData;

    protected $_is_persisted;

    protected $_base_price; //Original item price

    protected $_vat; //Item vat

    protected $_price_unit; //Price considering early bird

    protected $_quantity; //Selected quantity

    protected $_discounts; // Discounts applied to item. voucher and/or quantity rule

    protected $_discount_quantity;

    protected $_price_subtotal; // unit price * quantity

    protected $_price_subtotal_with_discount; // price subtotal - discounts

    protected $_billing_item; // \App\Models\BillingItem

    protected $_billing_item_original_name;

    protected $_billing_item_earlybird_name;

    protected $_voucher_applied;

    protected $_voucher;

    protected $_attendee;

    protected $_discount_amount;

    /**
     * EBOrderItem constructor.
     * @param EBOrder $order
     * @param $itemData Mixed Input array or BillingOrderAddon object //TODO: define input array structure to be followed strictly throughout the system.
     * @param $type string 'model' or 'input'
     * @throws \Exception
     */

    public function __construct(EBOrderAttendee $attendee, $itemData, $type)
    {
        $this->_order = $attendee->getOrder();
        $this->_attendee = $attendee;
        $this->_itemData = $itemData;
        $this->_is_persisted = false;
        if ($type == 'input') {
            $this->_constructFromInput();
        } else if ($type == 'model') {
            $this->_constructFromModel();
            $this->_is_persisted = true;
        } else {throw new \Exception('Item data could not be loaded.');}
        $this->_billing_item_original_name = $this->_getItemNameOriginal();
        $this->_billing_item_earlybird_name = $this->_getItemEarlyBirdName();
    }

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    private function _constructFromModel()
    {
        if (!$this->_itemData instanceof \App\Models\BillingOrderAddon) {
            $this->_model = \App\Models\BillingOrderAddon::findOrFail($this->_itemData['id']);
        } else {
            $this->_model = $this->_itemData;
        }

        $this->_billing_item = $this->_model->addon_detail()->first();
        $this->_base_price = $this->_billing_item->price;
        $this->_vat = ($this->_order->isOrderPlaced() && $this->_order->isEdit() == false) ? $this->_model->vat : $this->_billing_item->vat;
        $this->_quantity = $this->_model->qty;
        $this->_discount_quantity = $this->_model->discount_qty;

        if ($this->getOrder()->isVoucherApplied() && $this->getOrder()->getAppliedVoucherType() == 'billing_items' && array_search($this->getBillingItemId(), $this->_order->getAppliedVoucherAffectedItemIds()) !== false) {
            if ($this->_model->discount > 0) {
                $this->_discounts[] = new EBOrderItemDiscount($this, 'voucher');
            }
        } else if($this->_model->discount > 0 && $this->_model->discount_qty > 0 && $this->_model->discount_type == 3) {
            $this->_discounts[] = new EBOrderItemDiscount($this, 'fix');
        }

        $this->_price_unit = $this->_model->price;
        $this->_discount_amount = $this->getAllDiscountsTotal();
        $this->_price_subtotal = $this->_price_unit * $this->_model->qty;
        $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
    }

    private function _constructFromInput()
    {
        $this->_model = new \App\Models\BillingOrderAddon();
        $this->_billing_item = \App\Models\BillingItem::findOrFail($this->_itemData['id']);
        $this->_base_price = $this->_billing_item->price;
        $this->_vat = $this->_billing_item->vat;
        $this->_quantity = (int) $this->_itemData['qty'];
        $this->_price_unit = ($this->_itemData['price'] != '') ? $this->_itemData['price'] : $this->_getItemEarlyBirdOrBasePrice();
        $this->_price_subtotal = $this->_price_unit * $this->_quantity;

        $this->_model->discount = isset($this->_itemData['discount']) ? $this->_itemData['discount'] : 0;
        $this->_model->discount_qty = isset($this->_itemData['qty']) ? $this->_itemData['qty'] : 0;
        $this->_model->discount_type = isset($this->_itemData['discount_type']) ? $this->_itemData['discount_type'] : 0;

        //Apply discount if applicable
        if ($this->getOrder()->isVoucherApplied() && $this->getOrder()->getAppliedVoucherType() == 'billing_items' && array_search($this->getBillingItemId(), $this->_order->getAppliedVoucherAffectedItemIds()) !== false) {
            $usage = $this->getOrder()->getItemVoucherUsageLimits();
            $usage = isset($usage[$this->getBillingItemId()]) ? $usage[$this->getBillingItemId()] : null;
            if ($usage && $usage['limit'] > 0 && ($usage['limit'] - $usage['used']) < 1) {
                //Do not apply discount on this item because voucher usage limit has exceeded.
            } else {
                $this->_discounts[] = new EBOrderItemDiscount($this, 'voucher');
            }
        } else if($this->_model->discount > 0 && $this->_model->discount_qty > 0 && $this->_model->discount_type == 3) {
            $this->_discounts[] = new EBOrderItemDiscount($this, 'fix');
        }   
        $this->_discount_amount = $this->getAllDiscountsTotal();
        $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
    }

    //Returns Earlybird price if applicable else returns $this->_base_price;
    private function _getItemEarlyBirdOrBasePrice()
    {
        $applicable_rule = $this->_getEarlyBirdAppliedRule();
        if ($applicable_rule instanceof \App\Models\BillingItemRule) {
            return $applicable_rule->price;
        }
        return $this->_base_price;
    }

    public function getBaseOrEarlyBirdPrice()
    {
        return $this->_getItemEarlyBirdOrBasePrice();
    }

    public function isQuantityDiscountApplied()
    {
        $rule = \App\Models\BillingItemRule::where('event_id', $this->getOrder()->getOrderEventId())
            ->where('rule_type', 'qty')
            ->where('item_id', $this->_billing_item->id)
            ->where('qty', '<=', $this->getQuantity())
            ->orderBy('qty', 'DESC')
            ->first();
        return ($rule instanceof \App\Models\BillingItemRule);
    }

    public function getAllDiscountsTotal()
    {
        $discount_subtotal = 0;
        foreach ($this->_discounts as $discount) {
            // $discount EBOrderItemDiscount
            $discount_subtotal += $discount->getDiscountAmount();
        }
        return $discount_subtotal;
    }

    public function getAllDiscountsQty()
    {
        $discount_quantity = 0;
        foreach ($this->_discounts as $discount) {
            // $discount EBOrderItemDiscount
            $discount_quantity += $discount->getDiscountQuantity();
        }
        return $discount_quantity;
    }

    public function getAllDiscountsType()
    {
        $discount_type = 0;
        foreach ($this->_discounts as $discount) {
            // $discount EBOrderItemDiscount
            $discount_type += $discount->getDisType();
        }
        return $discount_type;
    }

    public function getAllDiscounts()
    {
        // $discount EBOrderItemDiscount
        return $this->_discounts;
    }

    public function getPriceUnit()
    {
        return $this->_price_unit;
    }

    public function getBasePrice()
    {
        return $this->_base_price;
    }

    public function getVAT()
    {
        return $this->_vat;
    }

    public function getPriceSubtotalWithDiscount()
    {
        return $this->_price_subtotal_with_discount;
    }

    public function getPriceSubtotal()
    {
        return $this->_price_subtotal;
    }

    public function getQuantity()
    {
        return $this->_quantity;
    }

    public function getDiscountQuantity()
    {
        return $this->_discount_quantity;
    }

    public function getBillingItem()
    {
        return $this->_billing_item;
    }

    public function getAttendee()
    {
        return $this->_attendee;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getBillingItemId()
    {
        return $this->getBillingItem()->id;
    }

    public function getDiscounts()
    {
        return $this->_discounts;
    }

    public function save()
    {
        $this->_model->order_id = $this->getOrder()->getModel()->id;
        $this->_model->attendee_id = $this->getAttendee()->getModel()->id;
        $this->_model->addon_id = $this->getBillingItemId();
        $this->_model->name = $this->getItemName();
        $this->_model->price = round($this->getPriceUnit(), 2);
        $this->_model->vat = $this->getVAT();
        $this->_model->qty = $this->getQuantity();
        $this->_model->discount_qty = $this->getAllDiscountsQty();
        $this->_model->discount_type = $this->getAllDiscountsType();
        $this->_model->discount = round($this->getAllDiscountsTotal(), 2);
        $this->_model->ticket_item_id = $this->getBillingItem()->ticket_item_id;
        $this->_model->link_to = $this->getBillingItem()->link_to;
        $this->_model->link_to_id = $this->getBillingItem()->link_to_id ?: 0;
        $this->_model->group_id = $this->getBillingItem()->group_id ?: 0; // Use billing item group id if set or 0
        $this->_model->save();
        $this->_is_persisted = true;
        if(!$this->_order->getUtility()->isDraft()) {
            event(Event::OrderAttendeeAddonSaveAfterInstaller, [$this->getOrder(), $this]);
        }
    }

    public function cumulate()
    {
        $clone = clone $this;
        foreach ($this->_order->getAttendees() as $attendee) {
            // \Eventbuizz\EBObject\EBOrderAttendee
            if ($attendee->getModel()->email != $this->_attendee->getModel()->email) {
                foreach ($attendee->getItems() as $item) {
                    if ($item->getBillingItemId() == $this->getBillingItemId()) {
                        $clone->_discount_amount = $clone->_discount_amount + $item->getDiscountAmount();
                        $clone->_quantity = $clone->_quantity + $item->getQuantity();
                        $clone->_price_subtotal = $clone->_price_subtotal + $item->getPriceSubtotal();
                        $clone->_price_subtotal_with_discount = $clone->_price_subtotal_with_discount + $item->getPriceSubtotalWithDiscount();
                    }
                }
            }
        }

        return $clone;
    }

    public function updateQtyFromInput(int $qty)
    {
        $this->_quantity = $qty;
        $this->_model->qty = $qty;
        $this->_price_unit = $this->_model->price;
        $this->_price_subtotal = $this->_price_unit * $this->_model->qty;
        $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
    }

    public function updatePriceFromInput(float $price)
    {
        $this->_model->price = $price;
        $this->_price_unit = $this->_model->price;
        $this->_price_subtotal = $this->_price_unit * $this->_model->qty;
        $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
    }

    public function updateDiscounts()
    {
        unset($this->_discounts);
        if ($this->getOrder()->isVoucherApplied() && $this->getOrder()->getAppliedVoucherType() == 'billing_items' && array_search($this->getBillingItemId(), $this->_order->getAppliedVoucherAffectedItemIds()) !== false) {
            $this->_discounts[] = new EBOrderItemDiscount($this, 'voucher');
            $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
        } else if($this->_model->discount > 0 && $this->_model->discount_type == 3) {
            $this->_discounts[] = new EBOrderItemDiscount($this, 'fix');
            $this->_price_subtotal_with_discount = $this->_price_subtotal - $this->getAllDiscountsTotal();
        } else {
            $this->_price_subtotal_with_discount = $this->_price_subtotal;
        }
    }

    public function update($model_attributes)
    {
        $exclude_attributes = ['id', 'addon_id', 'order_id', 'attendee_id', 'created_at', 'updated_at', 'deleted_at'];
        foreach ($model_attributes as $attribute => $value) {
            if (array_search($attribute, $exclude_attributes) === false) {
                $this->_model->{$attribute} = $value;
            }
        }
    }

    public function replicateModel()
    {
        $this->_model = $this->_model->replicate();
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function updateModelAttribute($field, $value)
    {
        $this->_model->{$field} = $value;
    }

    private function _getItemNameOriginal()
    {
        return $this->_billing_item->info()->where('name', '=', 'item_name')->value('value');
    }

    private function _getEarlyBirdAppliedRule()
    {
        return $this->_billing_item->rules()->where('rule_type', '=', 'date')->whereIn('end_date', ['0000-00-00', '1970-01-01'])
            ->orWhere(function ($query) {
                $query->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'));
            })
            ->where('item_id', '=', $this->getBillingItemId())
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }

    private function _getItemEarlyBirdName()
    {
        $applicable_rule = $this->_getEarlyBirdAppliedRule();
        if ($applicable_rule instanceof \App\Models\BillingItemRule) {
            return $applicable_rule->info()->where('name', '=', 'item_name')->value('value');
        }
        return null;
    }

    public function getItemName()
    {
        return $this->_billing_item_earlybird_name != '' ? $this->_billing_item_earlybird_name : $this->_billing_item_original_name;
    }

    public function getDiscountAmount()
    {
        return $this->_discount_amount;
    }
}
