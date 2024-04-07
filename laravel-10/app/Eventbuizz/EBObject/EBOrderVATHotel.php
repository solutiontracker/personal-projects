<?php
namespace App\Eventbuizz\EBObject;

class EBOrderVATHotel implements Contracts\OrderVATTypeInterface
{
    protected $_vat_amount;
    protected $_type;
    protected $_vat_rate;
    protected $_hotel_subtotal;

    // Set hotel level vat with price
    protected $_vat = [];

    public function __construct(EBOrderVAT $vat)
    {
        $order = $vat->getOrder();

        $apply_multi_vat = false;
        if ($order->isOrderPlaced() && $order->getPaymentSettingAttribute('item_level_vat') == 1) {
            $apply_multi_vat = true;
        } elseif ($order->isFree() == false && $order->getPaymentSettingAttribute('eventsite_apply_multi_vat') == 1) {
            $apply_multi_vat = true;
        }

        // Calculate hotel wise vat amount
        if ($apply_multi_vat) {
            $total_amount = 0;
            $hotels = $order->getHotel();
            foreach ($hotels as $hotel) {
                // EBOrderHotel
                if ($hotel->getVAT() > 0) {
                    $this->_vat_rate = $hotel->getVAT();
                    $this->_hotel_subtotal = $hotel->getHotelSubtotal();
                    $vat_amount = ($this->_hotel_subtotal * $this->_vat_rate) / 100;
                    $total_amount += $vat_amount;
                    if (array_key_exists(trim($hotel->getVAT()), $this->_vat)) {
                        $this->_vat[trim($hotel->getVAT())] += $vat_amount;
                    } else {
                        $this->_vat[trim($hotel->getVAT())] = $vat_amount;
                    }
                }
            }
            $this->_vat_amount = $total_amount;
        } else {
            $this->_vat_rate = $this->_getApplicableVatRate($order);
            $this->_hotel_subtotal = $order->getHotelSubTotal();
            $this->_vat_amount = ($this->_hotel_subtotal * $this->_vat_rate) / 100;
        }
    }

    private function _getApplicableVatRate(EBOrder $order)
    {
        //Free with hotel vat applicable
        if ($order->isFree() && $order->isHotelVatEnabledForFreeOrder()) {
            return $order->getPaymentSettingAttribute('hotel_vat');
        } else if ($order->isFree() == false) {
            return $order->getVatPercentage();
        } else {
            return 0;
        }
    }

    public function getApplicableVATRate(EBOrder $order)
    {
        return $this->_getApplicableVatRate($order);
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getVATAmount()
    {
        return $this->_vat_amount;
    }

    public function getVAT()
    {
        return $this->_vat;
    }
}
