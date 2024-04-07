<?php

namespace App\Eventbuizz\Repositories;

class MediaLibraryRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function InsertDirectoryImage($image_name, $type, $hub_organizer_id = null)
    {
        if ($hub_organizer_id) $organizer_id = $hub_organizer_id;
        else $organizer_id = organizer_id();

        $count = \App\Models\OrganizerMediaLibrary::where('organizer_id', $organizer_id)
            ->where('file_name', $image_name)
            ->where('type', $type)
            ->count();
        if ($count == 0) {
            $library = new \App\Models\OrganizerMediaLibrary();
            $library->organizer_id = $organizer_id;
            $library->file_name = $image_name;
            $library->type = $type;
            $library->save();
        }
    }
}
