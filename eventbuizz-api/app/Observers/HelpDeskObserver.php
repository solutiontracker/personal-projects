<?php


    namespace App\Observers;


    use App\Models\HelpDesk;
    use App\Models\HelpDeskAnswer;
    use App\Models\HelpDeskInfo;
    use App\Models\HelpDeskLike;

    class HelpDeskObserver
    {
        /**
         * Handle the User "created" event.
         *
         * @param HelpDesk $help_desk
         * @return void
         */
        public function created(HelpDesk $help_desk)
        {
            //
        }

        /**
         * Handle the User "updated" event.
         *
         * @param HelpDesk $help_desk
         * @return void
         */
        public function updated(HelpDesk $help_desk)
        {
            //
        }



        /**
         * Handle the User "deleted" event.
         *
         * @param HelpDesk $help_desk
         * @return void
         */
        public function deleted(HelpDesk $help_desk)
        {
            HelpDeskAnswer::where('help_desk_id', $help_desk->id)->delete();
            HelpDeskInfo::where('help_desk_id', $help_desk->id)->delete();
            HelpDeskLike::where('help_desk_id', $help_desk->id)->delete();
        }

        /**
         * Handle the User "forceDeleted" event.
         *
         * @param HelpDesk $help_desk
         * @return void
         */
        public function forceDeleted(HelpDesk $help_desk)
        {
            //
        }
    }