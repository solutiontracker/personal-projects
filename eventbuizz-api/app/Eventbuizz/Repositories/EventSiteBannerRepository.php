<?php

namespace App\Eventbuizz\Repositories;

use \App\Models\EventSiteBanner;

class EventSiteBannerRepository extends AbstractRepository
{
    protected $model;

    public function __construct(EventSiteBanner $model)
    {
        $this->model = $model;
    }

    /**
     *Destroy eventsite banner
     *
     * @param array
     * @param int
     */

    public function destroy($id)
    {
        \App\Models\EventSiteBanner::where('id', $id)->delete($id);
        \App\Models\EventSiteBannerInfo::where('banner_id', $id)->delete();
    }
}
