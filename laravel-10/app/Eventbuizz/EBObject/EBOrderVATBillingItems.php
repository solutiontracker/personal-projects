<?php
namespace App\Eventbuizz\EBObject;

class EBOrderVATBillingItems implements Contracts\OrderVATTypeInterface
{
    protected $_type;
    protected $_vat_amount;
    protected $_vat_percentage;
    protected $_items_subtotal;

    // Set item level vat with price
    protected $_vat = [];

    public function __construct(EBOrderVAT $vat)
    {
        $order = $vat->getOrder();

        if ($order->isVatApplicable()) {

            $apply_multi_vat = false;
            
            if ($order->isOrderPlaced() && $order->getModelAttribute('item_level_vat') == 1) {
                $apply_multi_vat = true;
            } elseif ($order->getPaymentSettingAttribute('eventsite_apply_multi_vat') == 1) {
                $apply_multi_vat = true;
            }

            if ($apply_multi_vat) {
               // Calculate item wise vat amount
                $cal_vat_with_items = true;
                if ($order->getVoucherDiscountType() == 2 && $order->getVoucherDiscountPrice() >= 100) {
                    $cal_vat_with_items = false;
                } elseif ($order->getVoucherDiscountType() == 1) {
                    $cal_vat_with_items = false;
                }
                $total_vat = 0;
                $order->getVoucherDiscountAmount();
                if ($order->getItemsTotal() > 0 && $cal_vat_with_items) {
                    $use_quantity_rule = ($order->getPaymentSettingAttribute('use_qty_rules') == 1) ? true : false;
                    foreach ($order->getAllItems() as $item) {
                        // Get item quantity discount amount
                        $quantity_discount_amount = 0;
                        if ($use_quantity_rule) {
                            $quantity_discount_amount = $order->getItemQuantityDiscountAmount($item);
                        }
                        // EBOrderItem
                        if ($item->getVAT() > 0) {
                            $vat_amount = (($item->getPriceSubtotalWithDiscount() - $quantity_discount_amount) * $item->getVAT()) / 100;
                            if ($vat_amount > 0) {
                                $total_vat += $vat_amount;
                                if (array_key_exists(trim($item->getVAT()), $this->_vat)) {
                                    $this->_vat[trim($item->getVAT())] += $vat_amount;
                                } else {
                                    $this->_vat[trim($item->getVAT())] = $vat_amount;
                                }
                            }
                        }
                    }
                }
                $this->_vat_amount = $total_vat;
            } else {
                $this->_items_subtotal = $order->getItemsTotal();
                $this->_vat_percentage = $order->getVatPercentage();
                $this->_vat_amount = ($this->_items_subtotal * $this->_vat_percentage) / 100;
            }
        } else {
            $this->_vat_amount = 0;
        }
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
