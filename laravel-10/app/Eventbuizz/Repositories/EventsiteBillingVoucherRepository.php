<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

use \App\Models\BillingVoucher;

use App\Eventbuizz\Repositories\OrganizerRepository;

class EventsiteBillingVoucherRepository extends AbstractRepository
{
    private $request;

    protected $model;

    protected $organizerRepository;

    public function __construct(Request $request, BillingVoucher $model, OrganizerRepository $organizerRepository)
    {
        $this->request = $request;
        $this->model = $model;
        $this->organizerRepository = $organizerRepository;
    }

    /**
     *Billing vouchers clone/default
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            //Billing vouchers
            $from_event_billing_vouchers = \App\Models\BillingVoucher::where("event_id", $request['from_event_id'])->get();

            if ($from_event_billing_vouchers) {

                foreach ($from_event_billing_vouchers as $from_event_billing_voucher) {

                    $to_event_billing_voucher = $from_event_billing_voucher->replicate();

                    $to_event_billing_voucher->event_id = $request['to_event_id'];

                    if (session()->has('clone.event.event_registration_form.' . $from_event_billing_voucher->registration_form_id) && $from_event_billing_voucher->registration_form_id > 0) {
                        $to_event_billing_voucher->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_event_billing_voucher->registration_form_id);
                    }

                    $to_event_billing_voucher->save();

                    //voucher info 
                    $from_event_billing_voucher_info = \App\Models\BillingVoucherInfo::where("voucher_id", $from_event_billing_voucher->id)->get();
                    
                    foreach ($from_event_billing_voucher_info as $from_info) {

                        $info = $from_info->replicate();

                        $info->voucher_id = $to_event_billing_voucher->id;

                        $info->languages_id = $request["languages"][0];

                        $info->save();
                    }

                    //voucher items
                    $from_voucher_items = \App\Models\BillingVoucherItem::where("voucher_id", $from_event_billing_voucher->id)->get();
                    
                    if ($from_voucher_items) {

                        foreach ($from_voucher_items as $from_voucher_item) {

                            if (session()->has('clone.event.billing_items.' . $from_voucher_item->item_id)) {

                                $to_voucher_item = $from_voucher_item->replicate();

                                $to_voucher_item->voucher_id = $to_event_billing_voucher->id;

                                $to_voucher_item->item_id = session()->get('clone.event.billing_items.' . $from_voucher_item->item_id);
                               
                                $to_voucher_item->save();

                            }

                        }

                    }
                }
            }
        }
    }

    /**
     * voucher listing
     * @param array
     *
     */

    public function listing($formInput)
    {
        
        $query = \App\Models\BillingVoucher::where(['event_id'=> $formInput["event_id"], 'registration_form_id'=>$formInput['registration_form_id']])
            ->join('conf_billing_vouchers_info', 'conf_billing_vouchers.id', '=', 'conf_billing_vouchers_info.voucher_id')
            ->where('conf_billing_vouchers_info.languages_id', $formInput["language_id"]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->where('name', '=', "voucher_name");
                    });
                });
            });
        }
        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "voucher_name")) {
            $query->orderBy('conf_billing_vouchers_info.value', $formInput['order_by']);
        } else {
            $query->orderBy('conf_billing_vouchers.' . $formInput['sort_by'], $formInput['order_by']);
        }

        $result = $query->select("conf_billing_vouchers.*", "conf_billing_vouchers_info.value")->paginate($formInput['limit'])->toArray();

        foreach ($result["data"] as $i => $row) {
            $items = [];
            if ($row['type'] == 'billing_items') {
                $items = \App\Models\BillingVoucherItem::where('voucher_id', $row['voucher_id'])->get();
                $orders = \App\Models\BillingOrder::where('code', $row['code'])->where('event_id', $formInput["event_id"])->where('is_archive', '=', '0')->currentOrder()->get();
                $usedInOrders = 0;
                foreach ($orders as $order) {
                    $count = 0;
                    $order_items = $order->load('order_addons')->order_addons;
                    foreach ($order_items as $item) {
                        foreach ($row['items'] as $k => $it) {
                            if ($it['item_id'] == $item['addon_id']) {
                                $matchedItem = $row['items'][$k];
                                break;
                            }
                        }
                        $count += self::getItemVoucherUsedQuantity($item, $matchedItem);
                    }
                    $usedInOrders += $count;
                }
                $used_count = $usedInOrders;
            } else {
                $usage_detail = \App\Models\BillingOrder::where('code', $row['code'])->where('event_id', $formInput["event_id"])->where('is_archive', '=', '0')->currentOrder()->get();
                $used_count = count($usage_detail);
            }
            $info = readArrayKey($row, [], 'info');
            $result["data"][$i] = $row;
            $result["data"][$i]['display_expiry_date'] = \Carbon\Carbon::parse($row['expiry_date'])->format('d/m/y');
            $result["data"][$i]['delete'] = $this->isVoucherDelete($row);
            $result["data"][$i]['edit'] = $this->isVoucherEdit($row);
            $result["data"][$i]['items'] = $items;
            $result["data"][$i]['detail'] = $info;
            $result["data"][$i]['number_of_usage_in_order'] = $used_count;
            $result["data"][$i]['available'] = abs((int)$row['usage'] - (int)$used_count);
            if ($used_count > 0) {
                $result["data"][$i]['order_exists'] = '1';
            } else {
                $result["data"][$i]['order_exists'] = '0';
            }
        }
        return $result;
    }

    /**
     * check voucher should be delete or not
     * @param object
     */
    public function isVoucherDelete($item)
    {
        $permission = $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'delete');
        if ($permission == "1") {
            return "delete";
        } else {
            return "hide";
        }
    }

    /**
     * check voucher should be edit or not
     * @param object
     */
    public function isVoucherEdit($item)
    {
        $permission = $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'edit');
        if ($permission == "1") {
            return "show";
        } else {
            return "hide";
        }
    }

    /**
     * Set form values for creation/updation
     *
     * @param array
     */

    public function setForm($formInput)
    {
        $formInput['event_id'] = $formInput['event_id'];
        $formInput['usage'] = (isset($formInput['usage']) && $formInput['usage'] ? $formInput['usage'] : 0);
        $formInput['status'] = 1;
        if ($formInput['expiry_date']) $formInput['expiry_date'] = date('Y-m-d', strtotime($formInput['expiry_date']));
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * create voucher
     * @param array
     *
     */
    public function createVoucher($formInput)
    {
        $instance = $this->setForm($formInput);
        $instance->create();
        $instance->insertInfo();
        $instance->insertItems();
    }

    /**
     * update voucher
     * @param array
     * @param int
     *
     */
    public function updateVoucher($formInput, $id)
    {
        $item = \App\Models\BillingVoucher::find($id);
        $instance = $this->setForm($formInput);
        $instance->update($item);
        $instance->updateInfo($id);
        $instance->deleteItems($id);
        $instance->insertItems();
    }

    /**
     * insert voucher info
     */
    public function insertInfo()
    {
        $fields = array('voucher_name');
        $formInput = $this->getFormInput();
        $voucher = $this->getObject();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $lang_id) {
            foreach ($fields as $field) {
                $info[] = new \App\Models\BillingVoucherInfo(array('name' => $field, 'value' => $formInput[$field], 'languages_id' => $lang_id));
            }
        }
        $voucher->info()->saveMany($info);
        return $this;
    }

    /**
     * update voucher info
     * @param int
     *
     */
    public function updateInfo($id)
    {
        $fields = array('voucher_name');
        $formInput = $this->getFormInput();
        foreach ($fields as $field) {
            \App\Models\BillingVoucherInfo::where('voucher_id', $id)->where('languages_id', $formInput["language_id"])->where('name', $field)->update(array('value' => $formInput[$field]));
        }
        return $this;
    }

    /**
     * delete voucher items
     * @param int
     *
     */
    public function deleteItems($id)
    {
        \App\Models\BillingVoucherItem::where("voucher_id", $id)->delete();
        return $this;
    }

    /**
     * insert voucher items
     * @param int
     *
     */
    public function insertItems()
    {
        $voucher = $this->getObject();
        $formInput = $this->getFormInput();
        if (isset($formInput["items"]) && count($formInput["items"]  ?? []) > 0) {
            foreach ($formInput["items"] as $key => $item) {
                if ($item["checked"]) {
                    \App\Models\BillingVoucherItem::create(array('voucher_id' => $voucher->id, 'discount_type' => $item["discount_type"], 'price' => $item["discount_price"], 'useage' => $item["useage"], 'item_id' => $item["id"], 'item_type' => 'addon'));
                }
            }
        }
        return $this;
    }

    /**
     * Fetch voucher
     * @param array
     * @param int
     *
     */
    public function getVoucher($formInput, $id)
    {
        $array = array();
        $voucher = \App\Models\BillingVoucher::find($id);
        if ($voucher) {
            $info = $voucher->info()->where('languages_id', $formInput["language_id"])->get();
            $items = $voucher->items()->get();
            $fields = array();
            foreach ($info as $info) {
                $fields[$info->name] = $info->value;
                if ($info->name == 'date') {
                    $fields[$info->name] = date('d-m-Y', strtotime($info->value));
                }
            }
            $array = $voucher->toArray();
            $array['detail'] = $fields;
            $array['items'] = $items;
        }
        return $array;
    }

    /**
     * items
     * @param array
     *
     */

    public function items($formInput)
    {
        $query = \App\Models\BillingItem::join('conf_billing_items_info', function ($join) use ($formInput) {
            $join->on('conf_billing_items.id', '=', 'conf_billing_items_info.item_id')
                ->whereIn("conf_billing_items_info.name", ["item_name"])
                ->where('conf_billing_items.is_free', (int)$formInput["is_free"])
                ->where('conf_billing_items_info.languages_id', $formInput["language_id"]);
        });

        $query->leftJoin("conf_billing_voucher_items", "conf_billing_voucher_items.item_id", "=", "conf_billing_items.id");

        $query->where('conf_billing_items.event_id', $formInput["event_id"])
            ->where('conf_billing_items.status', '=', '1')
            ->where('conf_billing_items.is_archive', '=', '0')
            ->where('conf_billing_items.type', '<>', 'group')
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
        }]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->where('name', '=', "item_name");
                    });
                });
            });
        }

        if (isset($formInput['registration_form_id']) && $formInput['registration_form_id']) {
            $query->where('conf_billing_items.registration_form_id', $formInput['registration_form_id']);
        }

        $query->groupBy('conf_billing_items.id');

        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] &&  in_array($formInput['sort_by'], ["id", "price"]))) {
            $query->orderBy('conf_billing_items.' . $formInput['sort_by'], $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] &&  in_array($formInput['sort_by'], ["useage", "discount_type"]))) {
            $query->orderBy('conf_billing_voucher_items.' . $formInput['sort_by'], $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] &&  in_array($formInput['sort_by'], ["amount"]))) {
            $query->orderBy('conf_billing_voucher_items.price', $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] &&  in_array($formInput['sort_by'], ["item_name"]))) {
            $query->orderBy('conf_billing_items_info.value', $formInput['order_by']);
        } else {
            $query->orderBy('conf_billing_items.sort_order', 'ASC');
        }

        $result = $query->select("conf_billing_items.*")->get()->toArray();

        foreach ($result as $i => $item) {
            $link_to_name = $this->itemLinkTo($formInput, $item);
            $info = readArrayKey($item, [], 'info');
            $info["link_to_name"] = $link_to_name;
            $item['detail'] = $info;
            $result[$i] = $item;
        }

        return $result;
    }

    /**
     * item link info => program/workshop/tracks/attendee group
     * @param array
     * @param object
     *
     */

    public static function itemLinkTo($formInput, $item)
    {
        if ($item['type'] != 'group' && $item['link_to'] != 'none') {
            if ($item['link_to'] == 'workshop') {
                $workshop = \App\Models\EventWorkshop::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"]);
                }])->first();
                return $link_to_name = $workshop->info[0]->value;
            } elseif ($item['link_to'] == 'program') {
                $program = \App\Models\EventAgenda::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"])->where('name', 'topic');
                }])->first();
                return $link_to_name = $program->info[0]->value;
            } else if ($item['link_to'] == 'attendee_group') {
                $group_ids = explode(",", $item['link_to_id']);
                $group = \App\Models\EventGroupInfo::whereIn('group_id', $group_ids)->where('name', 'name')->where('languages_id', $formInput["language_id"])->select(\DB::raw('group_concat(value) as names'))->first();
                return $link_to_name = ($group ? $group->names : '');
            } else {
                $track = \App\Models\EventTrack::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"]);
                }])->first();
                return $link_to_name = $track->info[0]->value;
            }
        }
    }

    /**
     * @param $item
     * @param $voucherItem
     * @return bool|float|int
     */
    public static function getItemVoucherUsedQuantity($item, $voucherItem)
    {
        if (count($voucherItem  ?? []) < 1 || count($item  ?? []) < 1 || $item['discount'] < 1) return false;
        $discount_type = $voucherItem['discount_type'];
        $unit_price = $item['price'];
        if ($discount_type == '2') {
            //percentage discount
            $price_percentage = $voucherItem['price'];
            $discount_amount_per_item = ($unit_price * $price_percentage) / 100;
            $usedCount = ($item['discount'] / $discount_amount_per_item);
        } else {
            //price discount
            $discountAmount = $voucherItem['price'];
            $usedCount = $item['discount'] / $discountAmount;
        }

        return $usedCount;
    }

    /**
     * delete voucher
     * @param int
     *
     */
    public function deleteVoucher($id)
    {
        \App\Models\BillingVoucher::where("id", $id)->delete();
        \App\Models\BillingVoucherInfo::where("voucher_id", $id)->delete();
        \App\Models\BillingVoucherItem::where("voucher_id", $id)->delete();
    }

    /**
     * update voucher status
     * @param array
     * @param int
     *
     */
    public static function updateVoucherStatus($formInput, $id)
    {
        \App\Models\BillingVoucher::where('id', $id)->update(array('status' => $formInput["status"]));
    }

    /**
     * fetch vouchers for export
     * @param array
     *
     */
    public function getVoucherExportData($formInput)
    {
        $result = \App\Models\BillingVoucher::where('event_id', $formInput["event_id"])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }, 'items'])->get()->toArray();

        $vouchers = array();
        foreach ($result as $i => $row) {
            if ($row['type'] == 'billing_items') {
                foreach ($row['items'] as $item) {
                    $billing_item = \App\Models\BillingItem::where('id', $item['item_id'])->where('event_id', $formInput["event_id"])->with(['info' => function ($query) use ($formInput) {
                        return $query->where('languages_id', $formInput["language_id"]);
                    }])->first();
                    if (count($row['info']  ?? []) > 0) {
                        foreach ($row['info'] as $val) {
                            if ($val['name'] == "voucher_name") {
                                $row['voucher_name'] = $val['value'];
                            }
                        }
                    }
                    if ($item['discount_type'] == 1) {
                        $discount_type = 'A';
                    } else {
                        $discount_type = 'P';
                    }
                    $vouchers[$i] = array('voucher_type' => ucfirst($row['type']), 'voucher_name' => $row['voucher_name'], 'discount_type' => $discount_type, 'price' => $item['price'], 'voucher_code' => $row['code'], 'expiry_date' => $row['expiry_date'], 'number_of_usage' => $item['useage'], 'item_id' => $item['item_id'], 'item_name' => $billing_item->info[0]['value']);
                }
            } else {
                if (count($row['info']  ?? []) > 0) {
                    foreach ($row['info'] as $val) {
                        if ($val['name'] == "voucher_name") {
                            $row['voucher_name'] = $val['value'];
                        }
                    }
                }
                if ($row['discount_type'] == 1) {
                    $discount_type = 'A';
                } else {
                    $discount_type = 'P';
                }
                $vouchers[$i] = array('voucher_type' => ucfirst($row['type']), 'voucher_name' => $row['voucher_name'], 'discount_type' => $discount_type, 'price' => $row['price'], 'voucher_code' => $row['code'], 'expiry_date' => $row['expiry_date'], 'number_of_usage' => $row['usage'], 'item_id' => '', 'item_name' => '');
            }
        }

        return $vouchers;
    }

    /**
     * voucher export setting
     */
    static public function getVoucherExportSettings()
    {

        $settings = array(
            'fields' => array(
                'voucher_type' => array(
                    'field' => 'voucher_type',
                    'label' => 'Voucher type',
                    'required' => false
                ),
                'voucher_name' => array(
                    'field' => 'voucher_name',
                    'label' => 'Voucher name',
                    'required' => true
                ),
                'discount_type' => array(
                    'field' => 'discount_type',
                    'label' => 'Discount type',
                    'required' => true
                ),
                'price' => array(
                    'field' => 'price',
                    'label' => 'Price',
                    'required' => true
                ),
                'code' => array(
                    'field' => 'code',
                    'label' => 'Voucher code',
                    'required' => true
                ),
                'expiry_date' => array(
                    'field' => 'expiry_date',
                    'label' => 'Expiry date (YYYY-MM-DD)',
                    'required' => false
                ),
                'usage' => array(
                    'field' => 'usage',
                    'label' => 'Number of usage',
                    'required' => false
                ),
                'item_id' => array(
                    'field' => 'item_id',
                    'label' => 'Item ID',
                    'required' => false
                ),
                'item_name' => array(
                    'field' => 'item_name',
                    'label' => 'Item name',
                    'required' => false
                ),

            )
        );

        return $settings;
    }

    /**
     * generate random code
     * @param array
     *
     */
    public function generateRandomCode($length = 6, $level = 1)
    {
        list($usec, $sec) = explode(' ', microtime());
        srand((float) $sec + ((float) $usec * 100000));
        $validchars[1] = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";
        $value  = "";
        $counter   = 0;
        while ($counter < $length) {
            $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level]) - 1), 1);
            if (!strstr($value, $actChar)) {
                $value .= $actChar;
                $counter++;
            }
        }
        return $value;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function getVoucherByCode($formInput)
    {
        return \App\Models\BillingVoucher::where('code', $formInput['voucher_code'])->where('event_id', $formInput['event_id'])->where('status', 1)->first();
    }
}
