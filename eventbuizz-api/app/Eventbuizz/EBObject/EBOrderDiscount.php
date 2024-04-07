<?php
namespace App\Eventbuizz\EBObject;

class EBOrderDiscount
{
    protected $_order;
    protected $_discount_object;
    protected $_type;

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    public function __construct(EBOrder $order, $type, $data = null)
    {
        $this->_order = $order;
        $this->_type = $type;
        if ($type == 'voucher') {
            $this->_discount_object = new EBOrderDiscountVoucher($this);
        } else if ($type == 'quantity') {
            $this->_discount_object = new EBOrderItemQuantityDiscount($this, $data);
        } else {
            throw new \Exception('Invalid order discount type');
        }
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getDiscountObject()
    {
        return $this->_discount_object;
    }

    public function getDiscountAmount()
    {
        return $this->_discount_object->getDiscountAmount();
    }

    public function getDiscountType()
    {
        return $this->_type;
    }

    public function replicateModel()
    {
        $this->getDiscountObject()->replicateModel();
    }
}
