<?php


namespace App\Eventbuizz\Repositories;


use App\Models\PageBuilderPage;
use Illuminate\Http\Request;

class PageBuilderPageRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPagebyId($id)
    {
        return  PageBuilderPage::findOrFail($id);
    }
    
}
