<?php
namespace App\Eventbuizz\EBObject;

/**
 * Class EBOrderItemFixDiscount
 * @package App\Eventbuizz\EBObject
 */
class EBOrderItemFixDiscount
{
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
     * EBOrderItemFixDiscount constructor.
     * @param EBOrderItemDiscount $discount
     * @throws \Exception
     */
    public function __construct(EBOrderItemDiscount $discount)
    {
        $item = $discount->getItem();
        if($item) {
            $this->_discount_amount = $item->getModel()->discount;
            $this->_discount_quantity = $item->getModel()->qty;
            $this->_discount_type = 3;
        } else {
            $this->_discount_amount = 0;
            $this->_discount_quantity = 0;
            $this->_discount_type = 0;
        }
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

    /**
     * @return int|0|1|2
     */
    public function getDiscountType()
    {
        return $this->_discount_type;
    }
}
