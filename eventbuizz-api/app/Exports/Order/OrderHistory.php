<?php
namespace App\Exports\Order;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderHistory implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    protected $eventId;
    protected $languageId;
    protected $request;
    protected $results;
    protected $secLabels;
    protected $regLabels;

    /**
     * @param Request $request
     * @param mixed $results
     * @param mixed $secLabels
     * @param mixed $regLabels
     */
    public function __construct(Request $request, $results, $secLabels, $regLabels)
    {
        $this->request = $request->all();
        $this->eventId = $request->event_id;
        $this->languageId = $request->language_id;
        $this->secLabels = $secLabels;
        $this->regLabels = $regLabels;
        $this->results = $results;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:BP1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

    /**
     * @return [type]
     */
    public function collection()
    {
        $array = array();
        $key = 0;
        foreach ($this->results as $row) {
            if (trim($row['order_number'])) {
                $order_number = $row['order_number'];
            } else {
                $order_number = $row['id'];
            }

            $order_logs = \App\Models\BillingOrderLog::where('event_id', $this->eventId)
                ->where('order_id', $row->id)
                ->get();

            foreach ($order_logs as $log) {
                //$array[$key]['order_number'] = $order_number;
                $array[$key]['order_date'] = date('Y-m-d', strtotime($row['order_date']));
                $array[$key]['order_time'] = date('H.i.s', strtotime($row['order_date']));
                $array[$key]['change_log'] = ucfirst(str_replace('_', ' ', $log['field_name'])) . ' ' . $log['data_log'];
                $key++;
            }
        }
        return collect($array);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            //'Order Number',
            'Date',
            'Time',
            'Change Log',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Order history';
    }
}
