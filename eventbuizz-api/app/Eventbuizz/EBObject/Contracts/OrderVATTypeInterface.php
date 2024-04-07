<?php
namespace App\Eventbuizz\EBObject\Contracts;

interface OrderVATTypeInterface
{
    public function getVATAmount();
    public function getType();
}
