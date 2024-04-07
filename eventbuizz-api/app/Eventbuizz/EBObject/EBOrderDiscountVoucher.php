<?php
namespace App\Eventbuizz\EBObject;

class EBOrderDiscountVoucher implements Contracts\OrderDiscountTypeInterface
{
    protected $_model;
    protected $_discount_amount;
    protected $_code;
    protected $_type;
    protected $_price;

    public function __construct(EBOrderDiscount $discount)
    {
        $order = $discount->getOrder();
        if ($order->isOrderPlaced()) {
            $model = \App\Models\BillingVoucher::where('event_id', '=', $order->getOrderEventId())
                ->where('type', '=', 'order')
                ->where('id', '=', $order->getModelAttribute('coupon_id'))
                ->first();
        } else {
            $model = \App\Models\BillingVoucher::where('event_id', '=', $order->getOrderEventId())
                ->where('type', '=', 'order')
                ->where('id', '=', $order->getModelAttribute('coupon_id'))
                ->first();
        }
        if (!$model instanceof \App\Models\BillingVoucher) {
            throw new \Exception('Applied voucher does not exist in system');
        }
        $this->_model = $model;
        $this->_type = $this->_model->discount_type;
        $this->_price = $this->_model->price;
        if ($this->_type == 2) //Percentage discount
        {
            $this->_discount_amount = ($this->_model->price * $discount->getOrder()->getItemsTotal()) / 100;
        } else if ($this->_type == 1) { //Fixed price discount
            $this->_discount_amount = $this->_model->price;
        } else {
            throw new \Exception('unknown discount type of order voucher.');
        }
    }

    public function getDiscountAmount()
    {
        return $this->_discount_amount;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function replicateModel()
    {
        $this->_model = $this->_model->replicate();
    }

    public function getPrice()
    {
        return $this->_price;
    }
}
