<?php
namespace App\Eventbuizz\EBObject\Contracts;

interface OrderDiscountTypeInterface
{
    public function getDiscountAmount();
    public function getModel();
    public function getType();
    public function replicateModel();
}
