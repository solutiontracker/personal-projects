<?php
namespace App\Eventbuizz\EBObject;

class EBOrderVAT
{
    protected $_order;
    protected $_vat_type;
    protected $_vat_object;

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    public function __construct(EBOrder $order, $type)
    {
        $this->_order = $order;
        $this->_vat_type = $type;
        if ($type == 'items') {
            $this->_vat_object = new EBOrderVATBillingItems($this);
        } else if ($type == 'hotel') {
            $this->_vat_object = new EBOrderVATHotel($this);
        } else {
            throw new \Exception('Invalid VAT type applied on order.');
        }
    }

    public function getVatAmount()
    {
        return $this->_vat_object->getVATAmount();
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getVatType()
    {
        return $this->_vat_type;
    }

    public function getApplicableVATRate()
    {
        // For item level vat
        if ($this->_vat_type == 'items') {
            // For items vat rate
            return $this->_order->getVatPercentage();
        } else {
            //For hotel
            return $this->_vat_object->getApplicableVATRate($this->_order);
        }
    }

    public function getVAT()
    {
        return $this->_vat_object->getVAT();
    }
}
