<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\NewsRepository;
use App\Http\Resources\News;
use App\Http\Resources\NewsCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EventNewsSetting;

class NewsController extends Controller
{
    public $successStatus = 200;

    protected $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
         $this->newsRepository = $newsRepository;
    }

    /**
     * This function return event news & also return pagination of news.
     */
    public function getNews(Request $request)
    {
        $news_limit = $request->limit;
        if($request->limit == "default_setting"){
            $event_news_setting = EventNewsSetting::whereEventId($request->event_id)->firstOrFail();
            $news_limit = $event_news_setting->registration_site_limit;
        }
        $request->merge(['limit' => $news_limit ?? 10]);
        $news = $this->newsRepository->getFrontNews($request->all());
        return new NewsCollection($news);
    }

    /**
     * This function return news details.
     */
    public function details(Request $request, $event_url ,$id){
        $news = $this->newsRepository->getNewsDetail($request->all(), $id);
        return new News($news);
    }

}
