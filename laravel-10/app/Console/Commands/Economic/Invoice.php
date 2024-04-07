<?php

namespace App\Console\Commands\Economic;

use Illuminate\Console\Command;

use App\Models\EconomicInvoice;

use App\Models\EconomicInvoiceProduct;

use App\Models\EconomicProduct;

class Invoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch booked invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bookedInvoiceNumber = (int) EconomicInvoice::orderBy('bookedInvoiceNumber', 'DESC')->value('bookedInvoiceNumber');
        $content = $this->_api('https://restapi.e-conomic.com/invoices/booked?filter=bookedInvoiceNumber$gt:' . $bookedInvoiceNumber . '&pagesize=100');
        if (count($content['collection']  ?? []) > 0) {
            foreach ($content['collection'] as $row) {
                $check_invoice_type = true;
                $type = 4;
                $count = EconomicInvoice::where("bookedInvoiceNumber", $row["bookedInvoiceNumber"])->count();
                if ($count == 0) {
                    $detail = $this->_api("https://restapi.e-conomic.com/invoices/booked/" . $row["bookedInvoiceNumber"]);
                    if (\Carbon\Carbon::parse($detail["date"])->greaterThanOrEqualTo('2015-01-01')) {
                        list($deliveryTermDay, $deliveryTermMonth, $deliveryTermYear) = explode(".", $detail["delivery"]["deliveryTerms"]);
                        $invoice = EconomicInvoice::create([
                            "bookedInvoiceNumber" => $detail["bookedInvoiceNumber"],
                            "date" => ($detail["date"] ? \Carbon\Carbon::parse($detail["date"])->toDateString() : NULL),
                            "currency" => $detail["currency"],
                            "exchangeRate" => $detail["exchangeRate"],
                            "netAmount" => $detail["netAmount"],
                            "netAmountInBaseCurrency" => $detail["netAmountInBaseCurrency"],
                            "grossAmount" => $detail["grossAmount"],
                            "is_credit" => ($detail["grossAmount"] < 0 ? 1 : 0),
                            "grossAmountInBaseCurrency" => $detail["grossAmountInBaseCurrency"],
                            "vatAmount" => $detail["vatAmount"],
                            "roundingAmount" => $detail["roundingAmount"],
                            "remainder" => $detail["remainder"],
                            "remainderInBaseCurrency" => $detail["remainderInBaseCurrency"],
                            "dueDate" => ($detail["dueDate"] ? \Carbon\Carbon::parse($detail["dueDate"])->toDateString() : NULL),
                            "paymentTermsNumber" => $detail["paymentTerms"]["paymentTermsNumber"],
                            "daysOfCredit" => $detail["paymentTerms"]["daysOfCredit"],
                            "paymentTermsName" => $detail["paymentTerms"]["name"],
                            "paymentTermsType" => $detail["paymentTerms"]["paymentTermsType"],
                            "customerNumber" => $detail["customer"]["customerNumber"],
                            "recipient_name" => $detail["recipient"]["name"],
                            "recipient_address" => $detail["recipient"]["address"],
                            "recipient_zip" => $detail["recipient"]["zip"],
                            "recipient_city" => $detail["recipient"]["city"],
                            "recipient_country" => $detail["recipient"]["country"],
                            "recipient_ean" => $detail["recipient"]["ean"],
                            "customerContactNumber" => $detail["recipient"]["attention"]["customerContactNumber"],
                            "vatZoneNumber" => $detail["recipient"]["vatZone"]["vatZoneNumber"],
                            "layoutNumber" => $detail["layout"]["layoutNumber"],
                            "delivery_address" => $detail["delivery"]["address"],
                            "deliveryTerms" => ($detail["delivery"]["deliveryTerms"] ? \Carbon\Carbon::parse($deliveryTermYear . '-' . $deliveryTermMonth . '-' . $deliveryTermDay)->toDateString() : NULL),
                            "deliveryDate" => ($detail["delivery"]["deliveryDate"] ? \Carbon\Carbon::parse($detail["delivery"]["deliveryDate"])->toDateString() : NULL),
                        ]);

                        //products
                        if (count($detail["lines"]  ?? []) > 0) {
                            foreach ($detail["lines"] as $line) {
                                if (isset($line["product"]["productNumber"]) && $line["product"]["productNumber"]) {
                                    $productGroupNumber = EconomicProduct::where("productNumber", $line["product"]["productNumber"])->value("productGroupNumber");
                                    EconomicInvoiceProduct::create([
                                        "bookedInvoiceNumber" => $detail["bookedInvoiceNumber"],
                                        "productGroupNumber" => $productGroupNumber,
                                        "lineNumber" => $line["lineNumber"],
                                        "sortKey" => $line["sortKey"],
                                        "description" => $line["description"],
                                        "quantity" => $line["quantity"],
                                        "unitNetPrice" => $line["unitNetPrice"],
                                        "discountPercentage" => $line["discountPercentage"],
                                        "unitCostPrice" => $line["unitCostPrice"],
                                        "vatRate" => $line["vatRate"],
                                        "totalNetAmount" => $line["totalNetAmount"],
                                        "productNumber" => $line["product"]["productNumber"],
                                        "unitNumber" => $line["unit"]["unitNumber"],
                                        "departmentalDistributionNumber" => $line["departmentalDistribution"]["departmentalDistributionNumber"],
                                        "is_credit" => ($detail["grossAmount"] < 0 ? 1 : 0),
                                        "customerNumber" => $detail["customer"]["customerNumber"],
                                        "deliveryTerms" => ($detail["delivery"]["deliveryTerms"] ? \Carbon\Carbon::parse($deliveryTermYear . '-' . $deliveryTermMonth . '-' . $deliveryTermDay)->toDateString() : NULL),
                                        "deliveryDate" => ($detail["delivery"]["deliveryDate"] ? \Carbon\Carbon::parse($detail["delivery"]["deliveryDate"])->toDateString() : NULL),
                                    ]);

                                    if ($check_invoice_type) {
                                        if (in_array($productGroupNumber, ["4", "3"])) {
                                            $type = 1;
                                        } else if (in_array($productGroupNumber, ["5"])) {
                                            $type = 2;
                                        } else {
                                            $type = 3;
                                        }

                                        $check_invoice_type = false;
                                    }
                                }
                            }
                        }

                        $invoice = EconomicInvoice::where("bookedInvoiceNumber", $row["bookedInvoiceNumber"])->update([
                            "type" => $type
                        ]);
                    }
                }
            }
        }
        $this->info('booked invoices executed successfully!');
    }

    public function _api($url)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-AppSecretToken' => config("services.economic.X-AppSecretToken"),
                'X-AgreementGrantToken'     => config("services.economic.X-AgreementGrantToken"),
                'Content-Type'      => 'application/json'
            ]
        ]);

        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }
}
