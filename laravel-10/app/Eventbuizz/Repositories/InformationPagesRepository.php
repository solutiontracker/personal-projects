<?php


namespace App\Eventbuizz\Repositories;

use App\Models\InformationPage;
use Illuminate\Http\Request;

class InformationPagesRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPagebyId($id, $formInput)
    {
        $language_id = $formInput['language_id'];
        $page = InformationPage::where('id', $id)->with(['info'=> function($q) use($language_id) {$q->where('language_id', $language_id);}])->first();

        if($page){
            $page = $page->toArray();
            foreach ($page['info'] as $key => $info) {
                $page[$info['name']] = $info['value'];
            }
        }
        return $page;
    }

    public function listing(array $data, $id)
    {

        $eventId = $data["event_id"];
        $languageId = $data["language_id"];

        $item_type_array = [
            1 => 'folder',
            2 => 'page',
            3 => 'link',
        ];
        
        if(!empty($id)){
            $pages = array();

            $menu_pages = \App\Models\InformationPage::where('section_id', '=', $id)->where('event_id', '=', $eventId)
            ->with(['info'=>function($query) use ($languageId) {
                return $query->where('language_id', '=', $languageId);
            }, 'submenu' => function($query) {
                return $query->orderBy('sort_order');
            } , 'submenu.info'=>function($query) use ($languageId) {
                return $query->where('language_id', '=', $languageId);
            }])
            ->where(function ($q){
                $q->whereNull('parent_id')
                    ->orWhere('parent_id',0);
            })->orderBy('sort_order')->get()->toArray();

            if (count($menu_pages) > 0) {

                foreach ($menu_pages as $key => $menu_page) {
                    
                    unset($menu_page['event']);

                    $rowData = array();
                    $infoData = readArrayKey($menu_page, $rowData, 'info');
                    $rowData['name'] = isset($infoData['name']) ? $infoData['name'] : '';
                    $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
                    $rowData['pdf_title'] = isset($infoData['pdf_title']) ? $infoData['pdf_title'] : '';
 
                    $subItems = array();
                    if(count($menu_page['submenu']) > 0){
                        foreach ($menu_page['submenu'] as $key2 => $submenu) {
                            
                            unset($submenu['event']);

                            $rowDataSub = array();
                            $infoDataSub = readArrayKey($submenu, $rowDataSub, 'info');
                            $rowDataSub['name'] = isset($infoDataSub['name']) ? $infoDataSub['name'] : '';
                            $rowDataSub['description'] = isset($infoDataSub['description']) ? $infoDataSub['description'] : '';
                            $rowDataSub['pdf_title'] = isset($infoDataSub['pdf_title']) ? $infoDataSub['pdf_title'] : '';
                            
                            unset($submenu['info']);
                            
                            $subItems[$key2] = $submenu;
                            $subItems[$key2]['type'] = $item_type_array[(int)$submenu['page_type']];
                            $subItems[$key2]['detail'] = $rowDataSub;
                        } 
                    }

                    unset($menu_page['info']);
                    unset($menu_page['submenu']);
                   

                    $pages[$key] = $menu_page;
                    $pages[$key]['subMenuItems'] = $subItems;
                    $pages[$key]['type'] = $item_type_array[(int) $menu_page['page_type']];
                    $pages[$key]['detail'] = $rowData;
                }

            }
            unset($menu_pages);
        }else{

            $menus = array();
            $menus_query = \App\Models\InformationSection::where('event_id', '=', $eventId)
                ->with(['Info'=>function($query) use ($languageId) {
                    return $query->where('language_id', '=', $languageId);
                }])->orderBy('sort_order')
                ->get()->toArray();
            foreach ($menus_query as $key => $menu) {
                unset($menu['event']);
                $rowData = array();
                $infoData = readArrayKey($menu, $rowData, 'info');
                $rowData['name'] = isset($infoData['name']) ? $infoData['name'] : '';
                unset($menu['info']);
                $menu['type'] = 'folder';
                $menus[$key] = $menu;
                $menus[$key]['detail'] = $rowData;
            }

        }

        $response = array();

        if (!empty($menus) && !empty($pages)) {
            $response = array_merge($menus, $pages);
        } else if (!empty($menus)) {
            $response = $menus;
        } else if (!empty($pages)) {
            $response = $pages;
        }

        usort($response, array($this, "sortBySortOrder"));
        return $response;
    }

    function sortBySortOrder($a, $b) {
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }
        return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
    }
    
}
