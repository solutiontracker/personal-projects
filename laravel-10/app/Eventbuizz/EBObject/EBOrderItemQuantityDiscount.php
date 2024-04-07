<?php
namespace App\Eventbuizz\EBObject;

class EBOrderItemQuantityDiscount implements Contracts\OrderDiscountTypeInterface
{
    protected $_model; // \App\Models\BillingItemRule
    protected $_rule_log_model; //array of \App\Models\BillingOrderRuleLog Object
    protected $_discount_amount;
    protected $_billing_item_id;
    protected $_type;
    protected $_item;
    protected $_price;

    /**
     * EBOrderDiscount discount.
     * @param EBOrderItem $item
     * @throws \Exception
     */
    public function __construct(EBOrderDiscount $discount, EBOrderItem $item)
    {
        //In case of placed order, Check below query for deleted/expired rules after order placed
        //In case of placed order, We are re calculating the discount from database.
        //Verify data against DB discount.
        $order = $discount->getOrder();
        $event_id = $order->getOrderEventId();
        $this->_item = $item;

        if ($order->isOrderPlaced()) {
            $qty_rule = \App\Models\BillingItemRule::where('event_id', '=', $event_id)
                ->where('rule_type', '=', 'qty')
                ->where('item_id', '=', $item->getBillingItemId())
                ->where('qty', '<=', $item->getQuantity())
                ->orderBy('qty', 'desc')
                ->with('info')
                ->first();
            $this->_model = $qty_rule;
            $this->_rule_log_model = \App\Models\BillingOrderRuleLog::where('item_id', '=', $item->getBillingItemId())
                ->where('order_id', '=', $item->getOrder()->getModelAttribute('id'))
                ->first();
            $this->_type = $this->getModel()->discount_type;
            if ($this->_type == 'percentage') {
                $this->_discount_amount = ($item->getPriceSubtotal() * $this->getModel()->discount) / 100;
            } else if ($this->_type == 'price') {
                $this->_discount_amount = $this->getModel()->discount;
            } else {
                throw new \Exception('Invalid quantity discount type.');
            }
        } else {
            $qty_rule = \App\Models\BillingItemRule::where('event_id', '=', $event_id)->where('rule_type', '=', 'qty')->where('item_id', '=', $item->getBillingItemId())->where('qty', '<=', $item->getQuantity())->orderBy('qty', 'desc')->with('info')->first();
            if (!$qty_rule instanceof \App\Models\BillingItemRule) {
                throw new \Exception('billing item rule does not exist.');
            }
            $this->_model = $qty_rule;
            $this->_type = $this->getModel()->discount_type;
            $this->_billing_item_id = $this->getModel()->item_id;
            if ($this->_type == 'percentage') {
                $this->_discount_amount = ($item->getPriceSubtotal() * $this->getModel()->discount) / 100;
            } else if ($this->_type == 'price') {
                $this->_discount_amount = $this->getModel()->discount;
            } else {
                throw new \Exception('Invalid quantity discount type.');
            }
        }

        $this->_setupRuleLogModel();
    }

    private function _setupRuleLogModel()
    {
        $this->_rule_log_model = new \App\Models\BillingOrderRuleLog();
        $this->_rule_log_model->rule_id = $this->_model->id;
        $this->_rule_log_model->item_id = $this->_item->getBillingItemId();
        $this->_rule_log_model->item_qty = $this->_item->getQuantity();
        $this->_rule_log_model->rule_qty = $this->_model->qty;
        $this->_rule_log_model->discount_type = $this->_model->discount_type;
        $this->_rule_log_model->item_price = round($this->_item->getPriceSubtotal(), 2);
        $this->_rule_log_model->rule_discount = $this->_model->discount;
        $this->_rule_log_model->item_discount = round($this->getDiscountAmount(), 2);
    }

    public function getModel()
    {
        // \App\Models\BillingItemRule
        return $this->_model;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getDiscountAmount()
    {
        return $this->_discount_amount;
    }

    public function getVoucher()
    {
        return $this->_model;
    }

    public function getItem()
    {
        return $this->_item;
    }

    public function replicateModel()
    {
        $this->_model = $this->_model->replicate();
        $this->_rule_log_model = $this->_rule_log_model->replicate();
    }

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    public function saveQtyRuleLog()
    {
        $this->_rule_log_model->order_id = $this->_item->getOrder()->getModelAttribute('id');
        $this->_rule_log_model->save();
    }

    public function getPrice()
    {
        return $this->_price;
    }
}
