<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\ResourceCollection;

    class EventInfoMenuCollection extends ResourceCollection
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            $data = ['data' => []];

            foreach ($this->collection as $item) {

                if ($item->type === 'folder') {
                    $data['data'][] = [
                        'id' => $item->id,
                        'event_id' => $item->id,
                        'name' => $item->name,
                        'parent_id' => $item->parent_id,
                        'sort_order' => $item->sort_order,
                        'status' => $item->status,
                        'type' => $item->type,
                        'info' => $this->when($item->info, new EventInfoMenuInfoCollection($item->info))
                    ];
                } elseif ($item->type === 'page') {
                    $data['data'][] = [
                        'id' => $item->id,
                        'event_id' => $item->event_d,
                        'icon' => $item->icon,
                        'image' => $item->image,
                        'image_position' => $item->image_position,
                        'page_type' => $item->page_type,
                        'pdf' => $item->pdf,
                        'menu_id' => $item->menu_id,
                        'sort_order' => $item->sort_order,
                        'status' => $item->status,
                        'type' => $item->type,
                        'info' => $this->when($item->info, new EventInfoMenuInfoCollection($item->info))
                    ];
                }
            }

            return $data;
        }
    }
