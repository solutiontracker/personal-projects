<?php


    namespace App\Observers;

    use App\Models\QA;
    use App\Models\QAAnswer;
    use App\Models\QAInfo;
    use App\Models\QALike;
    use App\Models\QALog;

    class QAObserver
    {
        /**
         * Handle the User "created" event.
         *
         * @param QA $qa
         * @return void
         */
        public function created(QA $qa)
        {
            //
        }

        /**
         * Handle the User "updated" event.
         *
         * @param QA $qa
         * @return void
         */
        public function updated(QA $qa)
        {
            //
        }



        /**
         * Handle the User "deleted" event.
         *
         * @param QA $qa
         * @return void
         */
        public function deleted(QA $qa)
        {
            QAAnswer::where('qa_id', $qa->id)->delete();
            QAInfo::where('qa_id', $qa->id)->delete();
            QALike::where('qa_id', $qa->id)->delete();
            QALog::where('qa_id', $qa->id)->delete();
        }

        /**
         * Handle the User "forceDeleted" event.
         *
         * @param QA $qa
         * @return void
         */
        public function forceDeleted(QA $qa)
        {
            //
        }
    }