<?php
namespace App\Eventbuizz\EBObject;

/**
 * Class EBOrderItemDiscount
 * @package App\Eventbuizz\EBObject
 */

class EBOrderItemDiscount
{
    /**
     * @var EBOrderItem
     */
    protected $_item;
    /**
     * @var string
     */
    protected $_type; // 'voucher' | 'quantity'
    /**
     * @var EBOrderItemQuantityDiscount
     */
    protected $_discount_object; // Either EbOrderDiscountVoucher or EBOrderItemQuantityDiscount
    /**
     * @var
     */
    protected $_quantity_discount_amount;
    /**
     * @var
     */
    protected $_discount_quantity;
    /**
     * @var
     */
    protected $_discount_type;

    /**
     * EBOrderItemDiscount constructor.
     * @param EBOrderItem $item
     * @param $type string 'voucher' or 'quantity'
     * @throws \Exception
     */
    public function __construct(EBOrderItem $item, $type)
    {
        $this->_item = $item;
        $this->_type = $type;
        if ($type == 'voucher') {
            $this->_discount_object = new EBOrderItemVoucherDiscount($this);
            $this->_discount_quantity = $this->_discount_object->getDiscountQuantity();
            $this->_discount_type = $this->_discount_object->getDiscountType();
        } else if ($type == 'fix') {
            $this->_discount_object = new EBOrderItemFixDiscount($this);
            $this->_discount_quantity = $this->_discount_object->getDiscountQuantity();
            $this->_discount_type = $this->_discount_object->getDiscountType();
        } else {
            throw new \Exception('Unknown item discount type.');
        }
    }

    /**
     * @return EBOrderItemQuantityDiscount|null
     */
    public function getDiscountObject()
    {
        if ($this->_type != 'quantity' && $this->_type != 'voucher') {
            return null;
        }

        return $this->_discount_object;
    }

    /**
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->_discount_object->getDiscountAmount();
    }

    /**
     * @return string
     */
    public function getDiscountQuantity()
    {
        return $this->_discount_object->getDiscountQuantity();
    }

    /**
     * for item addons
     * @return string
     */
    public function getDisType()
    {
        return $this->_discount_object->getDiscountType();
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->_type;
    }

    /**
     * @return EBOrderItem
     */
    public function getItem()
    {
        return $this->_item;
    }

    public function replicateModel()
    {
        $this->getDiscountObject()->replicateModel();
    }
}
