<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrder extends Model
{
    use SoftDeletes;

    use Observable;

    protected $table = 'conf_billing_orders';
    
    protected $fillable = ['event_id', 'language_id', 'attendee_id', 'session_id', 'event_price', 'event_qty', 'event_discount', 'security', 'vat', 'vat_amount', 'payment_fee', 'transaction_id', 'grand_total', 'summary_sub_total', 'total_attendee', 'discount_type', 'code', 'coupon_id', 'discount_amount', 'order_date', 'eventsite_currency', 'order_number', 'billing_quantity', 'status', 'comments', 'is_voucher', 'is_payment_received', 'payment_received_date', 'order_type', 'dibs_dump', 'is_free', 'invoice_reference_no', 'is_archive', 'is_waitinglist', 'is_tango', 'sale_agent_id', 'quantity_discount', 'user_agent', 'session_data', 'sale_type', 'reporting_panel_total', 'corrected_total', 'is_added_reporting', 'registration_type_id', 'registration_type', 'edit_mode', 'payment_response', 'registration_form_id', 'is_credit_note', 'clone_of', 'is_new_flow'];

    public function order_attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    /**
     * @return mixed
     */
    public function order_attendees()
    {
        return $this->hasMany('\App\Models\BillingOrderAttendee', 'order_id');
    }

    /**
     * @return mixed
     */
    public function child_orders()
    {
        return $this->hasMany('\App\Models\BillingOrder', 'parent_id', 'id');
    }

    public function scopeCurrentActiveOrders($q)
    {
        $orders = $q->with(['latestChild' => function ($query) {
            $query->where('is_credit_note', '=', 0)->where('status', '<>', 'draft');
            return $query;
        }, 'latestSibling' => function ($query) {
            $query->where('is_credit_note', '=', 0)->where('status', '<>', 'draft');
            return $query;
        }])->get()->toArray();
        if (count($orders) > 0) {
            $idsToShow = [];
            foreach ($orders as $ord) {
                if (count($ord['latest_child'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_child']['id'];
                } else if (count($ord['latest_sibling'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_sibling']['id'];
                } else {
                    $idsToShow[] = $ord['id'];
                }
            }
            return $q->whereIn('conf_billing_orders.id', $idsToShow)->where('conf_billing_orders.status', '<>', 'draft')->where('is_credit_note', '=', 0);;
        } else {
            return $q;
        }
    }

    public function scopeCurrentDraftOrders($q)
    {
        $orders = $q->with(['latestChild' => function ($query) {
            $query->where('is_credit_note', '=', 0)->where('status', 'draft');
            return $query;
        }, 'latestSibling' => function ($query) {
            $query->where('is_credit_note', '=', 0)->where('status', 'draft');
            return $query;
        }])->get()->toArray();
        if (count($orders) > 0) {
            $idsToShow = [];
            foreach ($orders as $ord) {
                if (count($ord['latest_child'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_child']['id'];
                } else if (count($ord['latest_sibling'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_sibling']['id'];
                } else {
                    $idsToShow[] = $ord['id'];
                }
            }
            return $q->whereIn('id', $idsToShow)->where('status', 'draft')->where('is_credit_note', '=', 0);;
        } else {
            return $q;
        }
    }

    public function scopeCurrentOrder($q)
    {
        $orders = $q->with(['latestChild' => function ($query) {
            $query->where('is_credit_note', '=', 0);
            return $query;
        }, 'latestSibling' => function ($query) {
            $query->where('is_credit_note', '=', 0);
            return $query;
        }])->get()->toArray();
        if (count($orders) > 0) {
            $idsToShow = [];
            foreach ($orders as $ord) {
                if (count($ord['latest_child'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_child']['id'];
                } else if (count($ord['latest_sibling'] ?? []) > 0) {
                    $idsToShow[] = $ord['latest_sibling']['id'];
                } else {
                    $idsToShow[] = $ord['id'];
                }
            }
            return $q->whereIn('id', $idsToShow)->where('is_credit_note', '=', 0);
        } else {
            return $q;
        }
    }

    public function latestChild()
    {
        return $this->hasOne('\App\Models\BillingOrder', 'parent_id', 'id')->latest();
    }

    public function latestSibling()
    {
        return $this->hasOne('\App\Models\BillingOrder', 'parent_id', 'parent_id')->where('parent_id', '<>', '0')->latest();
    }

    public function order_addons($attendee_id = null)
    {
        if ($attendee_id) {
            return $this->hasMany('\App\Models\BillingOrderAddon', 'order_id')->where('conf_billing_order_addons.attendee_id', '=', $attendee_id);
        }
        return $this->hasMany('\App\Models\BillingOrderAddon', 'order_id');
    }

    public function credit_notes()
    {
        return $this->hasMany(BillingOrderCreditNote::class, 'order_id');
    }

    public function order_hotels() {
        return $this->hasMany(EventOrderHotel::class, 'order_id','id');
    }

    public function order_address() {
        return $this->hasOne(AttendeeBilling::class, 'order_id');
    }

    public function siblings()
    {
        return $this->hasMany(BillingOrder::class,'parent_id','parent_id')->where('parent_id','<>','0');
    }

    public function mainBillingAttendee(){
        return $this->hasMany(AttendeeBilling::class, 'order_id')->where('attendee_id', $this->attendee_id);
    }

    public function latestOrder()
    {
        if($this->latestSibling()) {
            return $this->latestSibling();
        }
        if($this->latestChild()) {
            return $this->latestChild();
        }
        return $this;
    }

    public function tickets()
    {
        return $this->hasManyThrough(EventTicket::class,BillingOrderAddon::class,'order_id','addon_id')->whereNull('conf_billing_order_addons.deleted_at');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function order_vats() {
        return $this->hasMany(BillingOrderVAT::class, 'order_id','id');
    }
}
