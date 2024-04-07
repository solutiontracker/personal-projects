<?php
namespace App\Http\Controllers\Organizer\ContentManagement;
use App\Models\PageBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageBuilderController extends Controller
{    
    /**
     * contentStore
     *
     * @param  mixed $request
     * @return void
     */
    public function contentStore(Request $request){
        $user = PageBuilder::firstOrNew(array('id' => $request->page_id));
        $user->html = json_encode($request->html);
        $user->components = json_encode($request->components);
        $user->css = json_encode($request->css);
        $user->save();
    }
    
    /**
     * contentLoad
     *
     * @param  mixed $id
     * @return void
     */
    public function contentLoad($id) {
        $page = PageBuilder::where('id','=',$id)->first();
        $page->redirect = config('app.eventcenter_url').'/_admin/event_site/contentManagement?module=content_management_list';
        return $page;
    }
}
