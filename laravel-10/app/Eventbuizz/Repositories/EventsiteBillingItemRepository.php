<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\OrganizerRepository;

use \App\Models\BillingItem;

use Illuminate\Support\Arr;

class EventsiteBillingItemRepository extends AbstractRepository
{
    private $request;

    protected $model;

    protected $organizerRepository;

    public function __construct(Request $request, BillingItem $model, OrganizerRepository $organizerRepository)
    {
        $this->request = $request;
        $this->model = $model;
        $this->organizerRepository = $organizerRepository;
    }

    /**
     *Billing items clone/default
     *
     * @param array
     */
    public function install($request)
    {
        //Billing groups
        $from_event_billing_groups = \App\Models\BillingItem::where("event_id", $request['from_event_id'])->where("type", "group")->get();
        
        if ($from_event_billing_groups) {

            foreach ($from_event_billing_groups as $from_event_billing_group) {

                $to_event_billing_group = $from_event_billing_group->replicate();

                $to_event_billing_group->event_id = $request['to_event_id'];

                if (session()->has('clone.event.event_registration_form.' . $from_event_billing_group->registration_form_id) && $from_event_billing_group->registration_form_id > 0) {
                    $to_event_billing_group->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_event_billing_group->registration_form_id);
                }

                $to_event_billing_group->save();

                //group info 
                $from_event_billing_group_info = \App\Models\BillingItemInfo::where("item_id", $from_event_billing_group->id)->get();
                
                if ($from_event_billing_group_info) {

                    foreach ($from_event_billing_group_info as $from_info) {

                        $info = $from_info->replicate();

                        $info->item_id = $to_event_billing_group->id;

                        $info->languages_id = $request["languages"][0];

                        $info->save();

                    }

                }

                session()->put('clone.event.billing_items.' . $from_event_billing_group->id, $to_event_billing_group->id);
            }
        }

        //Billing items
        $from_event_billing_items = \App\Models\BillingItem::where("event_id", $request['from_event_id'])->where("type", "!=", "group")->get();
        
        if ($from_event_billing_items) {

            foreach ($from_event_billing_items as $from_event_billing_item) {

                $to_event_billing_item = $from_event_billing_item->replicate();

                $to_event_billing_item->event_id = $request['to_event_id'];

                if ($from_event_billing_item->group_id && session()->has('clone.event.billing_items.' . $from_event_billing_item->group_id)) {
                    $to_event_billing_item->group_id = session()->get('clone.event.billing_items.' . $from_event_billing_item->group_id);
                } else {
                    $to_event_billing_item->group_id = 0;
                }

                if ($from_event_billing_item->link_to == "program" && session()->has('clone.event.programs.' . $from_event_billing_item->link_to_id)) {
                    $to_event_billing_item->link_to_id = session()->get('clone.event.programs.' . $from_event_billing_item->link_to_id);
                } else if ($from_event_billing_item->link_to == "track" && session()->has('clone.event.tracks.' . $from_event_billing_item->link_to_id)) {
                    $to_event_billing_item->link_to_id = session()->get('clone.event.tracks.' . $from_event_billing_item->link_to_id);
                } else if ($from_event_billing_item->link_to == "workshop" && session()->has('clone.event.workshops.' . $from_event_billing_item->link_to_id)) {
                    $to_event_billing_item->link_to_id = session()->get('clone.event.workshops.' . $from_event_billing_item->link_to_id);
                } else if ($from_event_billing_item->link_to == "attendee_group") {
                    $to_groups = array();
                    $from_groups = explode(",", $from_event_billing_item->link_to_id);
                    foreach ($from_groups as $from_group) {
                        if (session()->has('clone.event.event_groups.' . $from_group)) {
                            array_push($to_groups, session()->get('clone.event.event_groups.' . $from_group));
                        }
                    }
                    $to_event_billing_item->link_to_id = implode(",", $to_groups);
                } else if ($from_event_billing_item->link_to == "none") {
                    $to_event_billing_item->link_to_id = 0;
                }

                if (session()->has('clone.event.event_registration_form.' . $from_event_billing_item->registration_form_id) && $from_event_billing_item->registration_form_id > 0) {
                    $to_event_billing_item->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_event_billing_item->registration_form_id);
                }

                $to_event_billing_item->save();

                //item info 
                $from_event_billing_item_info = \App\Models\BillingItemInfo::where("item_id", $from_event_billing_item->id)->get();
                
                foreach ($from_event_billing_item_info as $from_info) {

                    $info = $from_info->replicate();

                    $info->item_id = $to_event_billing_item->id;

                    $info->languages_id = $request["languages"][0];

                    $info->save();

                }

                //item rules
                $from_item_rules = \App\Models\BillingItemRule::where("item_id", $from_event_billing_item->id)->get();
                
                if ($from_item_rules) {

                    foreach ($from_item_rules as $from_item_rule) {

                        $to_item_rule = $from_item_rule->replicate();

                        $to_item_rule->item_id = $to_event_billing_item->id;

                        $to_item_rule->event_id = $request['to_event_id'];

                        $to_item_rule->save();

                        //item rule info
                        $from_item_rule_info = \App\Models\BillingItemRuleInfo::where("rule_id", $from_item_rule->id)->get();
                        
                        if ($from_item_rule_info) {

                            foreach ($from_item_rule_info as $from_info) {

                                $to_info = $from_info->replicate();

                                $to_info->rule_id = $to_item_rule->id;

                                $to_info->languages_id = $request["languages"][0];

                                $to_info->save();

                            }
                        }
                    }
                }

                session()->put('clone.event.billing_items.' . $from_event_billing_item->id, $to_event_billing_item->id);
            }
        }

        //Assign items to event
        $from_event_items = \App\Models\BillingItemEvent::where("event_id", $request['from_event_id'])->get();

        if ($from_event_items) {

            foreach ($from_event_items as $from_event_item) {

                if (session()->has('clone.event.billing_items.' . $from_event_item->item_id)) {

                    $to_event_item = $from_event_item->replicate();

                    $to_event_item->event_id = $request['to_event_id'];

                    $to_event_item->item_id = session()->get('clone.event.billing_items.' . $from_event_item->item_id);

                    $to_event_item->save();

                }

            }
            
        }
    }

    /**
     * insert billing items for default event
     * @param int
     *
     */
    public function insertDefaultEventAdminFee($event_id = 0)
    {
        $user = organizer_info();
        $event = \App\Models\Event::find($event_id);
        $event_languages[] = $event->language_id;
        foreach ($event->languages()->get() as $lang) {
            $event_languages[] = $lang['id'];
        }

        //Event fee
        $section_data = ['sort_order' => '0', 'item_number' => '0001', 'status' => '1', 'event_id' => $event_id, 'organizer_id' => $user->id, 'price' => '0', 'qty' => 0, 'status' => '1', 'type' => 'event_fee'];

        $billing_item = \App\Models\BillingItem::create($section_data);

        foreach ($event_languages as $lang_id) {
            \App\Models\BillingItemInfo::insert(
                ['name' => 'item_name', 'value' => 'Event fee', 'item_id' => $billing_item->id, 'languages_id' => $lang_id],
                ['name' => 'description', 'value' => '', 'item_id' => $billing_item->id, 'languages_id' => $lang_id]
            );
        }

        $section_data = ['item_id' => $billing_item->id, 'event_id' => $event_id, 'status' => 1];
        \App\Models\BillingItemEvent::create($section_data);

        //Administration fee
        $section_data = ['sort_order' => '0', 'item_number' => '0002', 'status' => '1', 'event_id' => $event_id, 'organizer_id' => $user->id, 'price' => '0', 'qty' => 0, 'status' => '1', 'type' => 'admin_fee'];
        $billing_item = \App\Models\BillingItem::create($section_data);

        foreach ($event_languages as $lang_id) {
            \App\Models\BillingItemInfo::insert(
                ['name' => 'item_name', 'value' => 'Administration fee', 'item_id' => $billing_item->id, 'languages_id' => $lang_id],
                ['name' => 'description', 'value' => '', 'item_id' => $billing_item->id, 'languages_id' => $lang_id]
            );
        }

        $section_data = ['item_id' => $billing_item->id, 'event_id' => $event_id, 'status' => 1];
        \App\Models\BillingItemEvent::create($section_data);
    }

    /**
     * check typewise item is inserted
     * @param array
     * @param string
     *
     */

    public function isItemInserted($formInput, $type)
    {
        $items = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('type', $type)->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->get();

        if ($items->count() < 1) {
            $this->insertDefaultEventAdminFee($formInput["event_id"]);
        }

        return $items;
    }

    /**
     * item listing
     * @param array
     *
     */

    public function listing($formInput)
    {
        //active order ids
        $order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', 0)->currentOrder()->pluck('id');

        $currencies = getCurrencyArray();

        $payment_setting = EventSiteSettingRepository::getPaymentSetting(['event_id' => $formInput['event_id'], 'registration_form_id' => 0]);

        $query = \App\Models\BillingItem::leftJoin('conf_billing_items_info', function ($join) use ($formInput) {
            $join->on('conf_billing_items.id', '=', 'conf_billing_items_info.item_id')
                ->whereIn("conf_billing_items_info.name", ["item_name", "group_name"])
                ->where('conf_billing_items_info.languages_id', $formInput["language_id"]);
        })
            ->where('conf_billing_items_info.languages_id', $formInput["language_id"])
            ->where('conf_billing_items.event_id', $formInput["event_id"])
            ->where('conf_billing_items.is_free', $formInput["is_free"])
            ->where('conf_billing_items.is_archive', 0)
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->whereIn('name', ["item_name", "group_name"]);
                    });
                });
                $query->orWhereHas('subitem.info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->where('name', '=', "item_name");
                    });
                });
            });
        }

        if(isset($formInput['exclude_event_fee']) && $formInput['exclude_event_fee'] == '1') {
            $result = $query->where('type', '<>', 'event_fee');
        }

        $query->where('conf_billing_items.registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0);
        
        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "item_name")) {
            $query->orderBy('conf_billing_items_info.value', $formInput['order_by']);
        } else if (isset($formInput['sort_by']) && isset($formInput['order_by'])) {
            $query->orderBy('conf_billing_items.' . $formInput['sort_by'], $formInput['order_by']);
        } else {
            $query->orderBy('conf_billing_items.sort_order', 'ASC');
        }

        $query->groupBy('conf_billing_items.id');

        $result = $query->select("conf_billing_items.*")->where('conf_billing_items.group_id', '0')->get()->toArray();

        foreach ($result as $i => $item) {
            //get group items
            $sub_items = \App\Models\BillingItem::where('group_id', $item['id'])->where('is_archive', 0)->where('is_free', $formInput["is_free"])->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }])->whereNull('deleted_at')->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get()->toArray();
            foreach ($sub_items as $key => $sub_item) {
                $link_to_name = $this->itemLinkTo($formInput, $sub_item);
                $info = readArrayKey($sub_item, [], 'info');
                $info["link_to_name"] = $link_to_name;
                $sub_item['detail'] = $info;
                $sub_item['sold_tickets'] = self::getItemSoldTickets($sub_item['id']);
                if ($sub_item['total_tickets'] > 0 || $sub_item['link_to_id'] > 0) {
                    $response = self::getItemRemainingTickets($sub_item['id'], $sub_item['total_tickets']);
                    $sub_item['remaining_tickets'] = (int) $response['remaining_tickets'];
                    $sub_item['total_tickets'] = $response['total_tickets'];
                } else {
                    $sub_item['remaining_tickets'] = "Unlimited";
                }
                $sub_item['currency'] = $currencies[$payment_setting->eventsite_currency];
                $sub_item['delete'] = $this->isItemDelete($order_ids, $sub_item);
                $sub_item['edit'] = $this->isItemEdit($sub_item);
                
                //Apply rules if enabled
                if(isset($formInput['rule']) && $formInput['rule']) {
                    $rule = $this->getItemRule($sub_item);
                    if($rule) {
                        $sub_item['detail']['item_name'] = $rule->detail['item_name'];
                        $sub_item['price'] = $rule->price;
                    }
                }
                
                $sub_item['priceDisplay'] = getCurrency($sub_item['price'], $currencies[$payment_setting->eventsite_currency]) . ' ' . $currencies[$payment_setting->eventsite_currency];

                $item['group_data'][$key] = $sub_item;
            }
            $link_to_name = $this->itemLinkTo($formInput, $item);
            $info = readArrayKey($item, [], 'info');
            $info["link_to_name"] = $link_to_name;
            $item['detail'] = $info;
            $item['sold_tickets'] = self::getItemSoldTickets($item['id']);
            if ($item['total_tickets'] > 0 || $item['link_to_id'] > 0) {
                $response = self::getItemRemainingTickets($item['id'], $item['total_tickets']);
                $item['remaining_tickets'] = (int) $response['remaining_tickets'];
                $item['total_tickets'] = $response['total_tickets'];
            } else {
                $item['remaining_tickets'] = "Unlimited";
            }
            $item['currency'] = $currencies[$payment_setting->eventsite_currency];
            $item['delete'] = $this->isItemDelete($order_ids, $item);
            $item['edit'] = $this->isItemEdit($item);

            //Apply rules if enabled
            if(isset($formInput['rule']) && $formInput['rule']) {
                $rule = $this->getItemRule($item);
                if($rule) {
                    $item['detail']['item_name'] = $rule->detail['item_name'];
                    $item['price'] = $rule->price;
                }
            }

            $item['priceDisplay'] = getCurrency($item['price'], $currencies[$payment_setting->eventsite_currency]) . ' ' . $currencies[$payment_setting->eventsite_currency];
            
            $result[$i] = $item;
        }
        
        return $result;
    }

    /**
     * check item should be delete or not
     * @param object
     */
    public function isItemDelete($activeOrderIds, $item)
    {
        $permission = $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'delete');
        if ($permission == "1") {
            if (in_array($item["type"], ["event_fee", "admin_fee"])) {
                return "hide";
            } else if ($item["type"] == "group") {
                $items_ids = \App\Models\BillingItem::where("group_id", $item["id"])->pluck('id');
                $count_orders = \App\Models\BillingOrderAddon::join("conf_billing_orders", "conf_billing_orders.id", "=", "conf_billing_order_addons.order_id")
                    ->where("conf_billing_orders.is_archive", 0)
                    ->whereIn("conf_billing_orders.id", $activeOrderIds)
                    ->whereIn("conf_billing_order_addons.addon_id", $items_ids)
                    ->count();
                if ($count_orders > 0) {
                    return "hide";
                } else {
                    return "delete";
                }
            } else {
                $count_orders = \App\Models\BillingOrderAddon::join("conf_billing_orders", "conf_billing_orders.id", "=", "conf_billing_order_addons.order_id")
                    ->where("conf_billing_orders.is_archive", 0)
                    ->whereIn("conf_billing_orders.id", $activeOrderIds)
                    ->where("conf_billing_order_addons.addon_id", $item["id"])
                    ->count();

                if ($count_orders > 0) {
                    return "archive";
                } else {
                    return "delete";
                }
            }
        } else {
            return "hide";
        }
    }

    /**
     * check item should be edit or not
     * @param object
     */
    public function isItemEdit($item)
    {
        $permission = $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'edit');
        if ($permission == "1") {
            if (in_array($item["type"], ["event_fee", "admin_fee"])) {
                $count_orders = \App\Models\BillingOrderAddon::where("addon_id", $item["id"])->count();
                if ($count_orders > 0) {
                    return "hide";
                } else {
                    return "show";
                }
            } else {
                return "show";
            }
        } else {
            return "hide";
        }
    }

    /**
     * Fetch item
     * @param array
     * @param int
     *
     */
    public function getItem($formInput, $id)
    {
        $program = \App\Models\BillingItem::where('id', $id)->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }, 'rules.info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->first();

        if ($program) {
            $date_prices = $qty_base_discount = [];
            foreach ($program->rules as $rule) {
                if ($rule->rule_type == "date") {
                    $date_prices[] = ['item_name' => (isset($rule["info"][0]["value"]) ? $rule["info"][0]["value"] : ""), 'price' => $rule->price, 'value' => getDatesFromRange($rule->start_date, $rule->end_date)];
                } else if ($rule->rule_type == "qty") {
                    $qty_base_discount[] = ['qty' => $rule->qty, 'discount_type' => $rule->discount_type, 'discount' => $rule->discount];
                }
            }
            $program->date_prices = $date_prices;
            $program->qty_base_discount = $qty_base_discount;
            $program = $program->toArray();
            $info = readArrayKey($program, [], 'info');
            $program["info"] = $info;
            return $program;
        }
        return [];
    }

    /**
     * Set form values for creation/updation
     *
     * @param array
     */

    public function setForm($formInput)
    {
        $formInput['event_id'] = $formInput['event_id'];
        $formInput['qty'] = (isset($formInput['qty']) && $formInput['qty'] ? $formInput['qty'] : 0);
        $formInput['total_tickets'] = (isset($formInput['total_tickets']) && $formInput['total_tickets'] ? $formInput['total_tickets'] : 0);
        $formInput['description'] = (isset($formInput['description']) && $formInput['description'] ? $formInput['description'] : "");
        $formInput['price'] = (isset($formInput['price']) && $formInput['price'] ? $formInput['price'] : 0);
        $formInput['organizer_id'] = organizer_id();
        $formInput['status'] = 1;
        if (!$formInput['item_number']) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $formInput['item_number'] = substr(str_shuffle($chars), 0, 6);
        }
        if (!isset($formInput['is_default'])) $formInput['is_default'] = 0;
        if (!isset($formInput['is_required']))  $formInput['is_required'] = 0;
        if (!isset($formInput['link_to_id'])) $formInput['link_to_id'] = 0;
        if ($formInput['link_to_id'] && (is_array($formInput['link_to_id']))) $formInput['link_to_id'] =  implode(',', $formInput['link_to_id']);
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * create item
     * @param array
     */
    public function createItem($formInput)
    {
        $instance = $this->setForm($formInput);
        $instance->create();
        $instance->insertInfo();
        $instance->insertRules();
        $instance->insertEventItem();
        $instance->updateItemsTicket();
        $instance->updateEventGroups();
        $instance->updateItemAttachedTrack();
        $instance->updateItemAttachedWorkshop();
        $instance->updateItemAttachedProgram();
    }

    /**
     * create item group
     * @param array
     */
    public function createItemGroup($formInput)
    {
        $formInput['status'] = 1;
        $instance = $this->setFormInput($formInput);
        $instance->create();
        $instance->insertItemGroupInfo();
    }

    /**
     * update item
     * @param array
     * @param int
     *
     */
    public function updateItem($formInput, $id)
    {
        $item = \App\Models\BillingItem::find($id);
        $cloneItem = clone $item;
        $instance = $this->setForm($formInput);
        $instance->update($item);
        $instance->updateInfo($id);
        $instance->insertRules();
        $instance->updateItemsTicket();
        $instance->updateEventGroups();
        $instance->updateItemAttachedTrack($cloneItem);
        $instance->updateItemAttachedWorkshop($cloneItem);

        $instance->updateItemAttachedProgram($cloneItem);
    }

    /**
     * update item group
     * @param array
     * @param int
     *
     */
    public function updateItemGroup($formInput, $id)
    {
        $item = \App\Models\BillingItem::find($id);
        $instance = $this->setFormInput($formInput);
        $instance->update($item);
        $instance->updateItemGroupInfo($id);
    }

    /**
     * insert event item
     */

    public function insertEventItem()
    {
        $formInput = $this->getFormInput();
        $item = $this->getObject();
        \App\Models\BillingItemEvent::create(array('item_id' => $item->id, 'event_id' => $formInput["event_id"], 'status' => '1'));
        return $this;
    }

    /**
     * insert item info
     */
    public function insertInfo()
    {
        $fields = array('item_name', 'description');
        $formInput = $this->getFormInput();
        $item = $this->getObject();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $lang_id) {
            foreach ($fields as $field) {
                $info[] = new \App\Models\BillingItemInfo(array('name' => $field, 'value' => $formInput[$field], 'languages_id' => $lang_id));
            }
        }
        $item->info()->saveMany($info);
        return $this;
    }

    /**
     * insert item group info
     */
    public function insertItemGroupInfo()
    {
        $fields = array('group_name');
        $formInput = $this->getFormInput();
        $item = $this->getObject();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $lang_id) {
            foreach ($fields as $field) {
                $info[] = new \App\Models\BillingItemInfo(array('name' => $field, 'value' => $formInput[$field], 'languages_id' => $lang_id));
            }
        }
        $item->info()->saveMany($info);
        return $this;
    }

    /**
     * update item info
     * @param int
     *
     */
    public function updateInfo($id)
    {
        $fields = array('item_name', 'description');
        $formInput = $this->getFormInput();
        foreach ($fields as $field) {
            \App\Models\BillingItemInfo::where('item_id', $id)->where('languages_id', $formInput["language_id"])->where('name', $field)->update(array('value' => $formInput[$field]));
        }
        return $this;
    }

    /**
     * update item group info
     * @param int
     *
     */
    public function updateItemGroupInfo($id)
    {
        $fields = array('group_name');
        $formInput = $this->getFormInput();
        foreach ($fields as $field) {
            \App\Models\BillingItemInfo::where('item_id', $id)->where('languages_id', $formInput["language_id"])->where('name', $field)->update(array('value' => $formInput[$field]));
        }
        return $this;
    }

    /**
     * insert item rules
     */

    public function insertRules()
    {
        $item = $this->getObject();
        //Delete old
        \App\Models\BillingItemRule::where('item_id', '=', $item->id)->delete();

        $formInput = $this->getFormInput();

        foreach ($formInput['qty_base_discount'] as $rule) {
            if ($rule['qty'] && $rule['discount']) {
                $ruleObj = new \App\Models\BillingItemRule();
                $ruleObj->qty = $rule['qty'];
                $ruleObj->discount_type = $rule['discount_type'];
                $ruleObj->discount = $rule['discount'];
                $ruleObj->price = '0';
                $ruleObj->start_date = '';
                $ruleObj->end_date = '';
                $ruleObj->rule_type = 'qty';
                $ruleObj->item_id = $item->id;
                $ruleObj->event_id = $formInput["event_id"];
                $ruleObj->save();
            }
        }

        foreach ($formInput['date_prices'] as $rule) {
            if ($rule['price'] && $rule['value']) {
                $ruleObj = new \App\Models\BillingItemRule();
                $ruleObj->qty = '0';
                $ruleObj->discount_type = 'price';
                $ruleObj->discount = '0';
                $ruleObj->price = $rule['price'];
                $ruleObj->start_date = date('Y-m-d', strtotime(Arr::first($rule['value'])));
                $ruleObj->end_date = date('Y-m-d', strtotime(Arr::last($rule['value'])));
                $ruleObj->rule_type = 'date';
                $ruleObj->item_id = $item->id;
                $ruleObj->event_id = $formInput["event_id"];
                $ruleObj->save();

                //Inserting INFO
                $ruleInfoObj = new \App\Models\BillingItemRuleInfo();
                $ruleInfoObj->name = 'item_name';
                $ruleInfoObj->value = $rule['item_name'];
                $ruleInfoObj->status = '1';
                $ruleInfoObj->rule_id = $ruleObj->id;
                $ruleInfoObj->languages_id = $formInput["language_id"];
                $ruleInfoObj->save();
            }
        }

        return $this;
    }

    /**
     * update items total tickets if item attached with program
     */
    public function updateItemsTicket()
    {
        $formInput = $this->getFormInput();
        if ($formInput['link_to_id'] && $formInput['link_to'] == 'program') {
            $program = \App\Models\EventAgenda::find($formInput['link_to_id']);
            \App\Models\BillingItem::where('link_to_id', $formInput['link_to_id'])->where('link_to', 'program')->update(['total_tickets' => $program->ticket]);
        }
        return $this;
    }

    /**
     * update event groups if item attached with attendee group 
     */
    public function updateEventGroups()
    {
        $formInput = $this->getFormInput();

        if ($formInput['link_to'] == 'attendee_group') {
            $groups = \App\Models\EventGroup::whereIn('id', explode(",", $formInput['link_to_id']))->get();
            foreach ($groups as $group) {
                $event_group = \App\Models\EventGroup::find($group->id);
                $event_group->link_type = 'billing_item';
                $event_group->save();
            }
        }

        $items = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to', 'attendee_group')->select(\DB::raw('group_concat(link_to_id) as link_to_ids'))->first();
        if ($items) {
            $groups = explode(",", $items->link_to_ids);
            $groups = \App\Models\EventGroup::where('event_id', $formInput["event_id"])->whereNotIn('id', $groups)->update([
                'link_type' => ''
            ]);
        }
        return $this;
    }

    /**
     * update track programs if item attached with track
     */
    public function updateItemAttachedTrack($item = null)
    {
        if (!$item) $item = $this->getObject();
        $formInput = $this->getFormInput();
        if ($formInput['link_to'] == 'none' && $item->link_to == "track") {
            $tracks = \App\Models\EventAgendaTrack::where('track_id', $item->link_to_id)->get();
            foreach ($tracks as $program) {
                $program = \App\Models\EventAgenda::find($program->agenda_id);
                $program->link_type = '';
                $program->save();
            }
        } else if ($formInput['link_to'] == 'track') {
            //old
            if ($item->link_to == "track") {
                $tracks = \App\Models\EventAgendaTrack::where('track_id', $item->link_to_id)->get();
                foreach ($tracks as $program) {
                    $program = \App\Models\EventAgenda::find($program->agenda_id);
                    $program->link_type = '';
                    $program->save();
                }
            }
            //new
            $tracks = \App\Models\EventAgendaTrack::where('track_id', $formInput['link_to_id'])->get();
            foreach ($tracks as $program) {
                $program = \App\Models\EventAgenda::find($program->agenda_id);
                $program->link_type = 'billing_item';
                $program->save();
            }
        }
    }

    /**
     * update workshop programs if item attached with workshop
     */
    public function updateItemAttachedWorkshop($item = null)
    {
        if (!$item) $item = $this->getObject();
        $formInput = $this->getFormInput();
        if ($formInput['link_to'] == 'none' && $item->link_to == "workshop") {
            $workshops = \App\Models\EventAgenda::where('workshop_id', $item->link_to_id)->get();
            foreach ($workshops as $program) {
                $program = \App\Models\EventAgenda::find($program->id);
                $program->link_type = '';
                $program->save();
            }
        } else if ($formInput['link_to'] == 'workshop') {
            //old
            if ($item->link_to == "workshop") {
                $workshops = \App\Models\EventAgenda::where('workshop_id', $item->link_to_id)->get();
                foreach ($workshops as $program) {
                    $program = \App\Models\EventAgenda::find($program->id);
                    $program->link_type = '';
                    $program->save();
                }
            }

            //new
            $workshops = \App\Models\EventAgenda::where('workshop_id', $formInput['link_to_id'])->get();
            foreach ($workshops as $program) {
                $program = \App\Models\EventAgenda::find($program->id);
                $program->link_type = 'billing_item';
                $program->save();
            }
        }
    }

    /**
     * update programs if item attached with program
     */
    public function updateItemAttachedProgram($item = null)
    {
        if (!$item) $item = $this->getObject();
        $formInput = $this->getFormInput();
        if ($formInput['link_to'] == 'none' && $item->link_to == "program") {
            $program = \App\Models\EventAgenda::find($item->link_to_id);
            $program->link_type = '';
            $program->save();
        } else if ($formInput['link_to'] == 'program') {
            //old
            if ($item->link_to == "program") {
                $program = \App\Models\EventAgenda::find($item->link_to_id);
                $program->link_type = '';
                $program->save();
            }
            //new
            $program = \App\Models\EventAgenda::find($formInput['link_to_id']);
            $program->link_type = 'billing_item';
            $program->save();
        }
    }

    /**
     * item link info => program/workshop/tracks/attendee group
     * @param array
     * @param object
     *
     */

    public function itemLinkTo($formInput, $item)
    {
        if ($item['type'] != 'group' && $item['link_to'] != 'none') {
            if ($item['link_to'] == 'workshop') {
                $workshop = \App\Models\EventWorkshop::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"]);
                }])->first();
                return $workshop->info[0]->value;
            } elseif ($item['link_to'] == 'program') {
                $program = \App\Models\EventAgenda::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"])->where('name', 'topic');
                }])->first();
                return $program->info[0]->value;
            } else if ($item['link_to'] == 'attendee_group') {
                $group_ids = explode(",", $item['link_to_id']);
                $group = \App\Models\EventGroupInfo::whereIn('group_id', $group_ids)->where('name', 'name')->where('languages_id', $formInput["language_id"])->select(\DB::raw('group_concat(value) as names'))->first();
                return ($group ? $group->names : '');
            } else {
                $track = \App\Models\EventTrack::where('id', $item['link_to_id'])->with(['info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput["language_id"]);
                }])->first();
                return $track->info[0]->value;
            }
        }
    }

    /**
     * item link info with detail => program/workshop/tracks/attendee group
     * @param array
     * @param object
     *
     */

    public static function itemLinkToWithDetail($formInput, $item)
    {
        $results = array();
        if ($item['type'] != 'group' && $item['link_to'] != 'none') {
            if ($item['link_to'] == 'workshop') {
                $workshop_id = $item['link_to_id'];
                $workshops = \App\Models\EventAgenda::where('event_id', $formInput["event_id"])
                    ->where('workshop_id', $workshop_id)
                    ->with(['info' => function ($query) use($formInput) {
                        return $query->where('languages_id', $formInput["language_id"]);
                        },
                    ])
                    ->orderBy('start_date', 'desc')
                    ->get();

                $info = array();
                foreach ($workshops as $tracks) {
                    foreach ($tracks['info'] as $track_info) {
                        if($track_info['name'] == 'date') {
                            $info[$track_info['name']] = \Carbon\Carbon::parse($track_info['value'])->format('l, F jS, Y');
                        } else if(in_array($track_info['name'], ['start_time', 'end_time'])) {
                            $info[$track_info['name']] = \Carbon\Carbon::parse($track_info['value'])->format('H:i');
                        } else {
                            $info[$track_info['name']] = $track_info['value'];
                        }
                    }
                    unset($tracks['info']);
                    $results[] = $info;
                }
            } else if ($item['link_to'] == 'track') {
                $track_id = $item['link_to_id'];
                $result_tracks = \App\Models\EventAgenda::where('event_id', $formInput["event_id"])->with(['info' => function ($query) use($formInput) {
                    return $query->where('languages_id', $formInput["language_id"]);
                },])
                    ->whereHas('tracks.info', function ($q) use ($track_id) {
                    $q->where('track_id', $track_id);
                })->orderBy('start_date', 'desc')->get();
                $info = array();
                foreach ($result_tracks as $tracks) {
                    foreach ($tracks['info'] as $track_info) {
                        if($track_info['name'] == 'date') {
                            $info[$track_info['name']] = \Carbon\Carbon::parse($track_info['value'])->format('l, F jS, Y');
                        } else if(in_array($track_info['name'], ['start_time', 'end_time'])) {
                            $info[$track_info['name']] = \Carbon\Carbon::parse($track_info['value'])->format('H:i');
                        } else {
                            $info[$track_info['name']] = $track_info['value'];
                        }
                    }
                    unset($tracks['info']);
                    $results[] = $info;
                }
            }
        }

        $results = collect($results)->groupBy('date')->all();

        return $results;
    }

    /**
     * item sold tickets
     * @param int
     *
     */
    public static function getItemSoldTickets($item_id)
    {
        $item = \App\Models\BillingItem::find($item_id);
        if ($item && $item->link_to == 'program') {
            $item_ids = \App\Models\BillingItem::where('event_id', $item->event_id)->where('link_to_id', $item->link_to_id)->select('id')->get()->toArray();
            $item_ids = Arr::flatten($item_ids);
            $addons_order_id = \App\Models\BillingOrderAddon::whereIn('addon_id', $item_ids)->groupBy('order_id')->select('order_id')->get()->toArray();
            $current_order_ids = \App\Models\BillingOrder::whereIn('id', Arr::flatten($addons_order_id))->currentOrder()->select('id')->get()->toArray();
            $ids = [];
            foreach ($current_order_ids as $o) {
                $ids[] = $o['id'];
            }
            $tickets_used = \App\Models\BillingOrderAddon::join('conf_billing_orders', 'conf_billing_orders.id', '=', 'conf_billing_order_addons.order_id')
                ->whereIn('conf_billing_order_addons.addon_id', $item_ids)->where('conf_billing_orders.is_archive', '=', '0')->whereNull('conf_billing_order_addons.deleted_at')
                ->whereIn('conf_billing_orders.id', $ids)->whereIn('conf_billing_orders.status', ['completed', 'draft'])->where('conf_billing_orders.is_waitinglist', '=', '0')->sum('conf_billing_order_addons.qty');
            return $tickets_used;
        }
        $temp = \App\Models\BillingOrderAddon::where('addon_id', '=', $item_id)->groupBy('order_id')->select('order_id')->get()->toArray();
        $tmp2 = \App\Models\BillingOrder::whereIn('id', Arr::flatten($temp))->currentOrder()->select('id')->get()->toArray();
        $ids = [];
        foreach ($tmp2 as $o) {
            $ids[] = $o['id'];
        }
        $tickets_used = \App\Models\BillingOrderAddon::join('conf_billing_orders', 'conf_billing_orders.id', '=', 'conf_billing_order_addons.order_id')->where('conf_billing_order_addons.addon_id', '=', $item_id)->where('conf_billing_orders.is_archive', '=', '0')->whereNull('conf_billing_order_addons.deleted_at')->whereIn('conf_billing_orders.id', $ids)->whereIn('conf_billing_orders.status', ['completed', 'draft'])->where('conf_billing_orders.is_waitinglist', '=', '0')->sum('conf_billing_order_addons.qty');
        $counter_ticket = $tickets_used;
        return $counter_ticket;
    }

    /**
     * item remaining tickets
     * @param int
     * @param int
     *
     */

    public static function getItemRemainingTickets($item_id, $total_tickets)
    {
        $item_detail = \App\Models\BillingItem::find($item_id);

        if ($item_detail && $item_detail->link_to == 'program') {

            $program = \App\Models\EventAgenda::find($item_detail->link_to_id);

            $total_tickets = $program ? $program->ticket : 0;

            $item_ids = \App\Models\BillingItem::where('event_id', $item_detail->event_id)->where('link_to_id', $item_detail->link_to_id)->select('id')->get()->toArray();

            $item_ids = Arr::flatten($item_ids);

            $addons_order_id = \App\Models\BillingOrderAddon::whereIn('addon_id', $item_ids)->groupBy('order_id')->select('order_id')->get()->toArray();

            $current_order_ids = \App\Models\BillingOrder::whereIn('id', Arr::flatten($addons_order_id))->currentOrder()->select('id')->get()->toArray();

            $ids = [];

            foreach ($current_order_ids as $o) {
                $ids[] = $o['id'];
            }

            $tickets_used = \App\Models\BillingOrderAddon::join('conf_billing_orders', 'conf_billing_orders.id', '=', 'conf_billing_order_addons.order_id')
                ->whereIn('conf_billing_order_addons.addon_id', $item_ids)->where('conf_billing_orders.is_archive', '=', '0')->whereNull('conf_billing_order_addons.deleted_at')
                ->whereIn('conf_billing_orders.id', $ids)->whereIn('conf_billing_orders.status', ['completed', 'draft'])->where('conf_billing_orders.is_waitinglist', '=', '0')->sum('conf_billing_order_addons.qty');

            return [
                'remaining_tickets' => $total_tickets - $tickets_used,
                'total_tickets' => $total_tickets
            ];

        }

        $temp = \App\Models\BillingOrderAddon::where('addon_id', '=', $item_id)->groupBy('order_id')->select('order_id')->get()->toArray();

        $tmp2 = \App\Models\BillingOrder::whereIn('id', Arr::flatten($temp))->currentOrder()->select('id')->get()->toArray();

        $ids = [];

        foreach ($tmp2 as $o) {
            $ids[] = $o['id'];
        }

        $tickets_used = \App\Models\BillingOrderAddon::join('conf_billing_orders', 'conf_billing_orders.id', '=', 'conf_billing_order_addons.order_id')->where('conf_billing_order_addons.addon_id', '=', $item_id)->where('conf_billing_orders.is_archive', '=', '0')->whereNull('conf_billing_order_addons.deleted_at')->whereIn('conf_billing_orders.id', $ids)->whereIn('conf_billing_orders.status', ['completed', 'draft'])->where('conf_billing_orders.is_waitinglist', '=', '0')->sum('conf_billing_order_addons.qty');
        
        $counter_ticket = $tickets_used;

        return [
            'remaining_tickets' => $total_tickets - $counter_ticket,
            'total_tickets' => $total_tickets
        ];
    }

    /**
     * item groups
     * @param array
     *
     */
    public static function getAllGroups($formInput)
    {
        $groups = array();

        $records = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0)->where('type', 'group')->where('is_free', $formInput["is_free"])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->get();

        foreach ($records as $key => $group) {

            foreach ($group['info'] as $info) {
                if ($info['name'] == 'group_name') {
                    $name = $info['value'];
                    break;
                }
            }

            $groups[$key]["id"] = $group['id'];

            $groups[$key]["name"] = $name;
            
        }

        return $groups;
    }

    /**
     * item programs
     * @param array
     *
     */
    public static function programs($formInput)
    {
        $heading_date = null;
        $result = \App\Models\EventAgenda::leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                ->where('a_end_time.name', '=', 'end_time')
                ->where('a_end_time.languages_id', '=', $formInput['language_id']);
        })
            ->where('conf_event_agendas.event_id', '=', $formInput['event_id'])
            ->with(['info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }])
            ->whereHas('info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            })
            ->whereNull('conf_event_agendas.deleted_at')
            ->whereIn('conf_event_agendas.link_type', ["", "billing_item"])
            ->orderBy('conf_event_agendas.start_date', 'ASC')
            ->orderBy('conf_event_agendas.start_time', 'ASC')
            ->orderBy('end_time', 'ASC')
            ->orderBy('conf_event_agendas.created_at', 'ASC')
            ->groupBy('conf_event_agendas.id')
            ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'));
        $programs = $result->get();

        foreach ($programs as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $rowData['id'] = $row['id'];
            $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';
            $rowData['heading_date'] = ($heading_date != $rowData['date'] ? \Carbon\Carbon::parse($rowData['date'])->format('m/d/Y') : '');
            $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
            $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
            $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';
            $rowData['disabled'] = ($row['link_type'] ? 1 : 0);
            $programs[$key] = $rowData;
            $heading_date = $rowData['date'];
        }
        return $programs;
    }

    /**
     * item tracks
     * @param array
     *
     */
    public static function tracks($formInput)
    {
        $tracks = array();
        $query = \App\Models\EventTrack::where('event_id', $formInput["event_id"])->where('parent_id', '0')
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->whereIn('name', ["name"]);
                    });
                });
                $query->orWhereHas('sub_tracks.info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->where('name', '=', "name");
                    });
                });
            });
        }

        $parents = $query->orderBy('sort_order', 'asc')->get();

        foreach ($parents as $parent) {
            $disabled = 0;
            $id = $parent['id'];
            $programs = \App\Models\EventAgendaTrack::where('track_id', $id)->get();
            foreach ($programs as $program) {
                $program_obj = \App\Models\EventAgenda::find($program['agenda_id']);
                if ($program_obj->link_type) {
                    $disabled = 1;
                    break;
                }
            }
            $count = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to_id', $id)->where("link_to", "track")->count();
            if ($count > 0) {
                $disabled = 1;
            }
            foreach ($parent->info as $info) {
                $name = $info['value'];
                break;
            }
            $tracks[] = array("id" => $id, "name" => $name, 'disabled' => $disabled, "parent" => true);
            $sub_query = \App\Models\EventTrack::where('event_id', $formInput["event_id"])->where('parent_id', $id)->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

            //search
            if (isset($formInput['query']) && $formInput['query']) {
                $sub_query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->whereIn('name', ["name"])
                            ->where('languages_id', $formInput["language_id"]);
                    });
                });
            }

            $childs = $sub_query->orderBy('sort_order', 'asc')->get();

            foreach ($childs as $child) {
                $disabled = 0;
                $child_id = $child['id'];
                $programs = \App\Models\EventAgendaTrack::where('track_id', '=', $child_id)->get()->toArray();
                foreach ($programs as $pro) {
                    $program_obj = \App\Models\EventAgenda::find($pro['agenda_id']);
                    if ($program_obj->link_type) {
                        $disabled = 1;
                        break;
                    }
                }
                $count = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to_id', $child_id)->where("link_to", "track")->count();
                if ($count > 0) {
                    $disabled = 1;
                }
                foreach ($child['info'] as $child_info) {
                    $name = $child_info['value'];
                    break;
                }
                $tracks[] = array("id" => $child_id, "name" => $name, 'disabled' => $disabled, "parent" => false);
            }
        }

        return $tracks;
    }

    /**
     * item workshops
     * @param array
     *
     */
    public static function workshops($formInput)
    {
        $workshops = array();
        $query = \App\Models\EventWorkshop::where('event_id', $formInput["event_id"])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->whereIn('name', ["name"]);
                    });
                });
            });
        }

        $records = $query->get();

        foreach ($records as $workshop) {
            $disabled = 0;
            $id = $workshop['id'];
            $program_obj = \App\Models\EventAgenda::where('workshop_id', $id)->get();
            foreach ($program_obj as $program) {
                if ($program['link_type']) {
                    $disabled = 1;
                    break;
                }
            }

            $count = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to_id', $id)->where("link_to", "workshop")->count();
            if ($count > 0) {
                $disabled = 1;
            }

            foreach ($workshop->info as $info) {
                if ($info['name'] == 'name') {
                    $name = $info['value'];
                    break;
                }
            }
            $name = $name . ' (' . $workshop['date'] . ' ' . date('H:i', strtotime($workshop['start_time'])) . ' - ' . date('H:i', strtotime($workshop['end_time'])) . ')';
            $workshops[] = array("id" => $id, "name" => $name, 'disabled' => $disabled);
        }
        return $workshops;
    }

    /**
     * item attendee groups
     * @param array
     *
     */
    public static function attendee_groups($formInput)
    {
        $returned_attendee_groups = [];
        $query = \App\Models\EventGroup::where('event_id', $formInput["event_id"])
            ->where('parent_id', '=', '0')
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }])
            ->with(['children' => function ($query) use ($formInput) {
                if (isset($formInput['query']) && $formInput['query']) {
                    $query->whereHas('info', function ($query) use ($formInput) {
                        $query->where(function ($query) use ($formInput) {
                            $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                                ->whereIn('name', ["name"]);
                        });
                    });
                }
                return $query->whereNull('deleted_at')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
            }, 'children.childrenInfo' => function ($query) use ($formInput) {
                return $query->whereNull('deleted_at')->where('languages_id', $formInput["language_id"]);
            }]);

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $query->where(function ($query) use ($formInput) {
                $query->whereHas('info', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->whereIn('name', ["name"]);
                    });
                });
                $query->orWhereHas('children.childrenInfo', function ($query) use ($formInput) {
                    $query->where(function ($query) use ($formInput) {
                        $query->where('value', 'LIKE', '%' . $formInput['query'] . '%')
                            ->where('name', '=', "name");
                    });
                });
            });
        }

        $attendee_groups = $query->whereNull('deleted_at')->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        foreach ($attendee_groups as $attendee_group) {
            $id = $attendee_group['id'];
            $returned_attendee_groups[] = array(
                "id" => $id,
                "parent" => true,
                "disabled" => 1,
                "name" => $attendee_group->info['value'],
            );
            if (count($attendee_group->children  ?? []) > 0) {
                foreach ($attendee_group->children as $child) {
                    $group = array(
                        "id" => $child['id'],
                        "parent" => false,
                        "disabled" => 0,
                        "name" => $child['info']['value']
                    );
                    $returned_attendee_groups[] = $group;
                }
            }
        }
        return $returned_attendee_groups;
    }

    /**
     * item delete
     * @param array
     * @param int
     *
     */
    public static function deleteItem($formInput, $id)
    {
        $item = \App\Models\BillingItem::where('id', $id)->first();
        if ($item->type == 'group') {
            $group_items = \App\Models\BillingItem::where('group_id', $item->id)->get();
            foreach ($group_items as $item) {
                $itemObj = \App\Models\BillingItem::find($item->id);
                if ($itemObj->link_to == 'program') {
                    $program = \App\Models\EventAgenda::find($itemObj->link_to_id);
                    $program->link_type = '';
                    $program->save();
                } else if ($itemObj->link_to == 'track') {
                    $tracks = \App\Models\EventAgendaTrack::where('track_id', $itemObj->link_to_id)->get();
                    foreach ($tracks as $program) {
                        $program = \App\Models\EventAgenda::find($program->agenda_id);
                        $program->link_type = '';
                        $program->save();
                    }
                } else if ($itemObj->link_to == 'workshop') {
                    $workshops = \App\Models\EventAgenda::where('workshop_id', $itemObj->link_to_id)->get();
                    foreach ($workshops as $program) {
                        $program = \App\Models\EventAgenda::find($program->id);
                        $program->link_type = '';
                        $program->save();
                    }
                }
                \App\Models\BillingItem::where('id', $item['id'])->delete();
                \App\Models\BillingItemInfo::where('item_id', $item['id'])->where('languages_id', $formInput["language_id"])->delete();
            }
            \App\Models\BillingItem::where('id', $id)->delete();
            \App\Models\BillingItemInfo::where('item_id', $id)->where('languages_id', $formInput["language_id"])->delete();
        } else {
            $itemObj = \App\Models\BillingItem::find($id);
            if ($itemObj->link_to == 'program') {
                $program = \App\Models\EventAgenda::find($itemObj->link_to_id);
                $program->link_type = '';
                $program->save();
            } else if ($itemObj->link_to == 'track') {
                $tracks = \App\Models\EventAgendaTrack::where('track_id', $itemObj->link_to_id)->get();
                foreach ($tracks as $program) {
                    $program = \App\Models\EventAgenda::find($program->agenda_id);
                    $program->link_type = '';
                    $program->save();
                }
            } else if ($itemObj->link_to == 'workshop') {
                $workshops = \App\Models\EventAgenda::where('workshop_id', $itemObj->link_to_id)->get();
                foreach ($workshops as $program) {
                    $program = \App\Models\EventAgenda::find($program->id);
                    $program->link_type = '';
                    $program->save();
                }
            }
            \App\Models\BillingItem::where('id', $id)->delete();
            \App\Models\BillingItemInfo::where('item_id', $id)->where('languages_id', $formInput["language_id"])->delete();
        }
        if ($item->is_ticket == '1') {
            \App\Models\EventTicketItemValidity::where('ticket_item_id', $id)->delete();
        }

        //update event groups
        $items = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to', 'attendee_group')->select(\DB::raw('group_concat(link_to_id) as link_to_ids'))->first();
        if ($items) {
            $groups = explode(",", $items->link_to_ids);
            $groups = \App\Models\EventGroup::where('event_id', $formInput["event_id"])->whereNotIn('id', $groups)->update([
                'link_type' => ''
            ]);
        }
    }

    /**
     * item archive
     * @param array
     * @param int
     *
     */
    public static function ArchiveItem($formInput, $id)
    {
        $item = \App\Models\BillingItem::where('id', $id)->first();
        if ($item->type == 'group') {
            $group_items = \App\Models\BillingItem::where('group_id', $item->id)->get();
            foreach ($group_items as $item) {
                $itemObj = \App\Models\BillingItem::find($item->id);
                if ($itemObj->link_to == 'program') {
                    $program = \App\Models\EventAgenda::find($itemObj->link_to_id);
                    $program->link_type = '';
                    $program->save();
                } else if ($itemObj->link_to == 'track') {
                    $tracks = \App\Models\EventAgendaTrack::where('track_id', $itemObj->link_to_id)->get();
                    foreach ($tracks as $program) {
                        $program = \App\Models\EventAgenda::find($program->agenda_id);
                        $program->link_type = '';
                        $program->save();
                    }
                } else if ($itemObj->link_to == 'workshop') {
                    $workshops = \App\Models\EventAgenda::where('workshop_id', $itemObj->link_to_id)->get();
                    foreach ($workshops as $program) {
                        $program = \App\Models\EventAgenda::find($program->id);
                        $program->link_type = '';
                        $program->save();
                    }
                }
                \App\Models\BillingItem::where('id', $item['id'])->update([
                    "is_archive" => 1
                ]);
            }
            \App\Models\BillingItem::where('id', $id)->update([
                "is_archive" => 1
            ]);
        } else {
            $itemObj = \App\Models\BillingItem::find($id);
            if ($itemObj->link_to == 'program') {
                $program = \App\Models\EventAgenda::find($itemObj->link_to_id);
                $program->link_type = '';
                $program->save();
            } else if ($itemObj->link_to == 'track') {
                $tracks = \App\Models\EventAgendaTrack::where('track_id', $itemObj->link_to_id)->get();
                foreach ($tracks as $program) {
                    $program = \App\Models\EventAgenda::find($program->agenda_id);
                    $program->link_type = '';
                    $program->save();
                }
            } else if ($itemObj->link_to == 'workshop') {
                $workshops = \App\Models\EventAgenda::where('workshop_id', $itemObj->link_to_id)->get();
                foreach ($workshops as $program) {
                    $program = \App\Models\EventAgenda::find($program->id);
                    $program->link_type = '';
                    $program->save();
                }
            }
            \App\Models\BillingItem::where('id', $id)->update([
                "is_archive" => 1
            ]);
        }

        //update event groups
        $items = \App\Models\BillingItem::where('event_id', $formInput["event_id"])->where('is_archive', 0)->where('link_to', 'attendee_group')->select(\DB::raw('group_concat(link_to_id) as link_to_ids'))->first();
        if ($items) {
            $groups = explode(",", $items->link_to_ids);
            $groups = \App\Models\EventGroup::where('event_id', $formInput["event_id"])->whereNotIn('id', $groups)->update([
                'link_type' => ''
            ]);
        }
    }

    /**
     * update item status
     * @param array
     * @param int
     *
     */
    public static function updateItemStatus($formInput, $id)
    {
        $item = \App\Models\BillingItem::where('id', $id)->first();
        if ($item->type == 'group') {
            $group_items = \App\Models\BillingItem::where('group_id', $item->id)->get();
            foreach ($group_items as $group_item) {
                \App\Models\BillingItem::where('id', $group_item->id)->update(array('status' => $formInput["status"]));
            }
        }
        \App\Models\BillingItem::where('id', $id)->update(array('status' => $formInput["status"]));
    }

    /**
     * update item order
     * @param array
     */
    public static function updateItemOrder($formInput)
    {
        $items = $formInput["items"];
        foreach ($items as $key => $item) {
            $item = \App\Models\BillingItem::find($item["id"]);
            $item->sort_order = $key;
            $item->save();
        }
    }

    /**
     * @param mixed $formInput
    * 
    * @return [type]
    */
    public function getBillingItems($formInput)
    {
        $billingItems = array();
        $items = $this->listing(["event_id" => $formInput["event_id"], "exclude_event_fee" => 1, "is_free" => 1, "language_id" => $formInput["language_id"]]);
        //Excluding Sold out Items
        foreach ($items as $item) {
            if ($item['type'] == 'group') {
                $group_items = array();
                foreach ($item['group_data'] as $group_item) {
                    //Show/Hide
                    if($group_item['remaining_tickets'] == 'Unlimited' || $group_item['remaining_tickets'] > 0) {
                        $group_items[] = $group_item;
                    }
                }
                if (count($group_items) > 0) {
                    $item['group_data'] = $group_items;
                    $billingItems[] = $item;
                }
            } else {
                //Show/Hide
                if($item['remaining_tickets'] == 'Unlimited' || $item['remaining_tickets'] > 0) {
                    $billingItems[] = $item;
                }
            }

        }
        
        return $billingItems;
    }

    public static function getItemsByType($type, $language_id, $event_id)
    {
        $billingItems = \App\Models\BillingItem::where('event_id', $event_id)
            ->where('is_archive','=',0)
            ->where('type','=',$type)
            ->with(['info' => function ($query) use($language_id) {
                return $query->where('languages_id', $language_id);
            }, 'event_items' => function ($query) use($event_id) {
                return $query->where('event_id', $event_id);
            }
            ])->whereHas('event_items', function ($q) use($event_id) {
                $q->where('event_id', $event_id);
            })
            ->whereNull('deleted_at')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->toArray();

        foreach ($billingItems as $i => $row) {
            $temp = array();
            if (count($row['info']) > 0) {
                foreach ($row['info'] as $val) {
                    $temp[$val['name']] = $val['value'];
                }
            }
            $row['detail'] = $temp;
            $billingItems[$i] = $row;
            unset($billingItems[$i]['event_items']);
            unset($billingItems[$i]['info']);
        }

        return $billingItems;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function getRegistrationItems($formInput)
    {
        $currencies = getCurrencyArray();
        
        $allItems = $this->listing($formInput);

        $registrationItems = array();

        $payment_setting = $formInput["event"]['payment_setting'];

        $labels = $formInput["event"]['labels'];

        foreach ($allItems as $key => $item) {
            if ($item['type'] == 'group') {
                $group_items = array();
                foreach ($item['group_data'] as $group_item) {
                    if($group_item['status'] == 1 || (in_array($formInput['provider'], ['sale', 'admin']) && $group_item['is_internal'] == 1)) {
                        //Fetch order attendee item
                        if(isset($formInput["order_id"]) && isset($formInput["attendee_id"])) {
                            $orderItem = \App\Models\BillingOrderAddon::where('order_id', $formInput["order_id"])->where('attendee_id', $formInput["attendee_id"])->where('addon_id', $group_item['id'])->first();
                            if($orderItem) {
                                $group_item['is_default'] = 1;
                                $group_item['quantity'] = $orderItem['qty'];
                                $group_item['price'] = $orderItem['price'];
                                $group_item['priceDisplay'] = getCurrency($orderItem['price'], $currencies[$payment_setting->eventsite_currency]) . ' ' . $currencies[$payment_setting->eventsite_currency];
                                $group_item['discount'] = $orderItem['discount_type'] == 3 ? $orderItem['discount'] : 0;
                                $group_item['discount_type'] = $orderItem['discount_type'];
                                if($group_item['remaining_tickets'] != 'Unlimited') { // If order is editing then add order item existing qty into stock
                                    $group_item['remaining_tickets'] = ($group_item['remaining_tickets'] + $orderItem['qty']);
                                }
                            } else {
                                $group_item['quantity'] = 1;
                                $group_item['discount'] = 0;
                                $group_item['discount_type'] = 0;
                                if($group_item['remaining_tickets'] != 'Unlimited') {
                                    $group_item['is_default'] = $group_item['remaining_tickets'] == 0 ? 0 : $group_item['is_default'];
                                }
                            }
                        } else {
                            $group_item['quantity'] = 1;
                            $group_item['discount'] = 0;
                            $group_item['discount_type'] = 0;
                            if($group_item['remaining_tickets'] != 'Unlimited') {
                                $group_item['is_default'] = $group_item['remaining_tickets'] == 0 ? 0 : $group_item['is_default'];
                            }
                        }
                        
                        $group_item['link_data'] = $this->itemLinkToWithDetail($formInput, $group_item);

                        $group_item['qty_discount_info'] = $this->getQuantityItemRule($group_item, true, $labels);

                        //Show/Hide
                        $group_items[] = $group_item;
                    }
                }
                if (count($group_items) > 0) {
                    $registrationItems[$key] = $item;
                    $registrationItems[$key]['group_data'] = $group_items;
                }
            } else {
                if($item['status'] == 1 || (in_array($formInput['provider'], ['sale', 'admin']) && $item['is_internal'] == 1)) {
                    //Fetch order attendee item
                    if(isset($formInput["order_id"]) && isset($formInput["attendee_id"])) {
                        $orderItem = \App\Models\BillingOrderAddon::where('order_id', $formInput["order_id"])->where('attendee_id', $formInput["attendee_id"])->where('addon_id', $item['id'])->first();
                        if($orderItem) {
                            $item['is_default'] = 1;
                            $item['quantity'] = $orderItem['qty'];
                            $item['price'] = $orderItem['price'];
                            $item['priceDisplay'] = getCurrency($orderItem['price'], $currencies[$payment_setting->eventsite_currency]) . ' ' . $currencies[$payment_setting->eventsite_currency];
                            $item['discount'] = $orderItem['discount_type'] == 3 ? $orderItem['discount'] : 0;
                            $item['discount_type'] = $orderItem['discount_type'];
                            if($item['remaining_tickets'] != 'Unlimited') { // If order is editing then add order item existing qty into stock
                                $item['remaining_tickets'] = ($item['remaining_tickets'] + $orderItem['qty']);
                            }
                        } else {
                            $item['quantity'] = 1;
                            $item['discount'] = 0;
                            $item['discount_type'] = 0;
                            if($item['remaining_tickets'] != 'Unlimited') {
                                $item['is_default'] = $item['remaining_tickets'] == 0 ? 0 : $item['is_default'];
                            }
                        }
                    } else {
                        $item['quantity'] = 1;
                        $item['discount'] = 0;
                        $item['discount_type'] = 0;
                        if($item['remaining_tickets'] != 'Unlimited') {
                            $item['is_default'] = $item['remaining_tickets'] == 0 ? 0 : $item['is_default'];
                        }
                    }

                    $item['link_data'] = $this->itemLinkToWithDetail($formInput, $item);

                    $item['qty_discount_info'] = $this->getQuantityItemRule($item, true, $labels);

                    $registrationItems[$key] = $item;
                }
            }
        }

        return [
            "allItems" => $allItems,
            "registrationItems" => array_values($registrationItems)
        ];
    }

    /**
     * @param mixed $item
     * 
     * @return [type]
     */
    public function getItemRule($item) {
        $rules = \App\Models\BillingItemRule::where('item_id', $item['id'])->with('info')->get();
    
        if(count($rules) > 0) {
            foreach($rules as $rule) {
                $info = readArrayKey($rule, [], 'info');
                $rule['detail'] = $info;
                if($rule['rule_type'] == 'date') {
                    $start_date_timestamp = strtotime($rule['start_date']);
                    $end_date = $rule['end_date'];
                    $end_date_timestamp = strtotime($rule['end_date']);
                    $current_date = date('Y-m-d');
                    $current_date_timestamp = strtotime($current_date);
                    if($end_date == '0000-00-00' || $end_date == '1970-01-01') {
                        if($start_date_timestamp <= $current_date_timestamp) {
                            return $rule;
                        }
                    } else {
                        if($start_date_timestamp <= $current_date_timestamp && $end_date_timestamp >= $current_date_timestamp) {
                            return $rule;
                        }
                    }
                }
            }

            return false;
        }
    }
    
    /**
     * validateItem
     *
     * @param  mixed $item_id
     * @param  mixed $event_id
     * @param  mixed $organizer_id
     * @return void
     */
    public function validateItem($item_id, $event_id, $organizer_id) {
        return \App\Models\BillingItem::where('id',$item_id)->where('event_id',$event_id)->where('organizer_id', $organizer_id)->count();
    }

    /**
     * @param mixed $item
     * 
     * @return [type]
     */
    public function getQuantityItemRule($item, $label = false, $labels = array()) {
        $rules = \App\Models\BillingItemRule::where('item_id', $item['id'])->where('rule_type', 'qty')->get();
        if(!$label) {
            return $rules;
        } else {
            $label = "";
            if(count($rules) > 0) {
                $label = $labels['REGISTRATION_FORM_ITEM_DISCOUNTS'];
                foreach($rules as $rule) {
                    if($rule->discount_type == 'percentage') {
                        $label.= '<br />'.str_replace(array('%s', '%d', '%e'),array($rule->discount, '%', $rule->qty),$labels['REGISTRATION_FORM_QUANTITY_DISCOUNT_LABEL']);
                    } else {
                        $label.= '<br />'.str_replace(array('%s', '%d', '%e'),array($rule->discount, $item['currency'], $rule->qty),$labels['REGISTRATION_FORM_QUANTITY_DISCOUNT_LABEL']);
                    }
                }
            }

            return $label;
        }
    }
}
