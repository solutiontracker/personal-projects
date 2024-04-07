<?php

    namespace App\Eventbuizz\Database;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Schema;

    class EBSchema
    {
        const ARCHIVE_DB_CON = 'mysql_archive';

        /**
         * @param string $table
         * @return bool
         */
        static public function createBeforeDeleteTrigger(string $table) : bool
        {

            $columns = Schema::getColumnListing($table);

            if(count($columns) < 1) {
                dump("Trigger is not updated for table $table");
                Log::error("Trigger is not updated for table $table");
                return false;
            }

            $columns = array_map(function($value) { return '`'.$value.'`'; }, $columns);

            $cols = implode(',', $columns);

            $columns = array_map(function($value) { return 'OLD.'.$value; }, $columns);

            $values = implode(',', $columns);

            // Create Trigger BEFORE DELETE
            DB::unprepared(
                "CREATE OR REPLACE TRIGGER before_{$table}_delete
                    BEFORE DELETE
                    ON $table FOR EACH ROW
                    BEGIN
                        INSERT INTO ".config('database.connections.mysql_archive.database').".$table
                        ( $cols ) VALUES
                        ( $values );
                    END"
            );

            return true;
        }

        /**
         *  Drop Delete Trigger if it exists.
         * @param string $table
         */
        static public function dropDeleteTrigger(string $table){
            DB::unprepared("DROP TRIGGER IF EXISTS `before_{$table}_delete`");
        }
    }
