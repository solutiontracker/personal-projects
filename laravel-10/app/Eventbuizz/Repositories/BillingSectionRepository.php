<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class BillingSectionRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
