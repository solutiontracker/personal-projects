<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBadgeDesign extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_badges_design';
    protected $fillable = ['id', 'event_id', 'name', 'type', 'attendee_type', 'width', 'height', 'mirror', 'is_default', 'body', 'IsName', 'nameLocation', 'IsCompanyName', 'companyNameLocation', 'IsTitle', 'titleLocation', 'IsCompanyAddress', 'companyAddressLocation', 'IsPrivateAddress', 'privateAddressLocation', 'IsTelephone', 'telephoneLocation', 'IsMobile', 'mobileLocation', 'IsMobile_2', 'mobile2Location', 'IsMobile_3', 'mobile3Location', 'IsMobile_4', 'mobile4Location', 'IsMobile_5', 'mobile5Location', 'IsMobile_6', 'mobile6Location', 'IsMobile_7', 'mobile7Location', 'IsMobile_8', 'mobile8Location', 'IsMobile_9', 'mobile9Location', 'IsMobile_10', 'mobile10Location', 'Interests', 'interestsLocation', 'Textfield', 'textfieldLocation', 'IsLogo', 'logoLocation', 'IsEmail', 'emailLocation', 'IsProductArea', 'productAreaLocation', 'IsDepartment', 'departmentLocation', 'IsBarcode', 'barcodeLocation', 'IsCountry', 'countryLocation', 'IsOrganization', 'organizationLocation', 'IsDelegateNumber', 'delegateNumberLocation', 'IsNetworkGroup', 'networkGroupLocation', 'IsFirstName', 'firstnameLocation', 'IsLastName', 'lastnameLocation', 'IsName_1', 'nameLocation_1', 'IsFirstName_1', 'firstnameLocation_1', 'IsLastName_1', 'lastnameLocation_1', 'IsCompanyName_1', 'companyNameLocation_1', 'IsTitle_1', 'titleLocation_1', 'IsBarcode_1', 'barcodeLocation_1', 'created_at', 'updated_at'];

    protected $dates = ['deleted_at'];

    public function printQue()
    {
        return $this->belongsTo('\App\Models\BadgePrinterQueue', 'event_id', 'event_id');
    }

    public function eventInfo()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id', 'id');
    }
}
