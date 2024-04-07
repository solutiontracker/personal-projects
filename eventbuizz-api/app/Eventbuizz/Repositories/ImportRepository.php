<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Illuminate\Support\Facades\Storage;

class ImportRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *import
     *
     * @param string
     * @param string
     */

    public function import($file, $delimeter = ';')
    {
        $data = array();
        $config = new LexerConfig();
        $config->setDelimiter($delimeter);
        $lexer = new Lexer($config);
        $interpreter = new Interpreter();
        $interpreter->addObserver(function (array $row) use (&$data) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[] = $value;
            }
            $data[] = $temp;
        });
        $lexer->parse($file, $interpreter);
        return $data;
    }

    /**
     *export
     *
     * @param array
     * @param array
     * @param string
     * @param string
     * @param string
     * @param boolean
     */

    public function export($formInput, $data, $filename = 'export.csv', $delemeter = '', $save = false, $utf8 = false)
    {
        $filename = str_replace(" ", "_", $filename);

        if ($delemeter == '') {
            $event = \App\Models\Event::where('id', $formInput['event_id'])->first();
            $delimeter = $event->export_setting;
        } else {
            $delimeter = $delemeter;
        }

        $config = new ExporterConfig();
        $config->setDelimiter($delimeter);

        if ($utf8) {
            $config->setFromCharset('UTF-8');
            $config->setToCharset('UTF-8');
        }

        $exporter = new Exporter($config);

        if (!$save) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream; charset=utf-8');
            header("Content-Disposition: attachment; filename=export.csv");
            header('Content-Transfer-Encoding: binary');
            header("Access-Control-Allow-Origin:".$_SERVER['HTTP_ORIGIN']);
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            echo "\xEF\xBB\xBF";
            $exporter->export('php://output', $data);
            exit;
        } else {
            $exporter->export(storage_path('/app/assets/csv/' . $filename), $data);
        }
    }
}
