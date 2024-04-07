<?php

namespace App\Eventbuizz\Repositories\Sales;

use App\Eventbuizz\Repositories\AbstractRepository;
use Illuminate\Http\Request;
use App\Models\SaleAgent;


class SaleAgentRepository extends AbstractRepository
{

    protected $model;

    protected $request;

    public function __construct(Request $request, SaleAgent $model)
    {
        $this->request = $request;
        $this->model = $model;
    }


    public function getAgentByColumn($key, $value)
    {
        return $this->model->where($key, $value)->first();
    }

}
