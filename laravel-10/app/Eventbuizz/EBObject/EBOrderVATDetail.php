<?php
namespace App\Eventbuizz\EBObject;

class EBOrderVATDetail
{
    protected $_model;
    protected $_order;
    protected $_vat = [];
    protected $_is_apply;

    public function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    public function __construct(EBOrder $order)
    {
        $this->_order = $order;
        $type = ($this->_order->isOrderPlaced() && $this->_order->isEdit() == false) ? 'model' : 'input';
        if ($type == 'input') {
            $vats = $this->_order->getVATs();
            foreach ($vats as $vat) {
                // EBOrderVAT
                $this->_constructFromInput($vat->getVAT());
            }
        } else {
            $this->_constructFromModel();
        }
        if (count($this->_vat) > 0) {
            $this->_is_apply = true;
        }
    }

    private function _constructFromModel()
    {
        $vats = $this->_order->getModel()->order_vats()->get();
        foreach ($vats as $vat) {
            $this->_vat[trim($vat->vat)] = $vat->vat_price;
        }
    }

    private function _constructFromInput($vats)
    {
        foreach ($vats as $key => $vat) {
            if (array_key_exists(trim($key), $this->_vat)) {
                $this->_vat[trim($key)] += $vat;
            } else {
                $this->_vat[trim($key)] = $vat;
            }
        }
    }

    public function getVAT()
    {
        return $this->_vat;
    }

    public function isVatApplied()
    {
        if ($this->_order->isVoucherApplied() && $this->_order->getAppliedVoucherType() == 'vat_free') {
            return 1;
        }
        return ($this->_is_apply == true ? 1 : 0);
    }

    public function save()
    {
        if (count($this->_vat) > 0) {
            ksort($this->_vat);
            foreach ($this->_vat as $vat => $vat_price) {
                $order_vat = new \App\Models\BillingOrderVAT();
                $order_vat->order_id = $this->_order->getModelAttribute('id');
                $order_vat->vat = $vat;
                $order_vat->vat_price = round($vat_price, 2);
                $order_vat->save();
            }
        }
    }
}
