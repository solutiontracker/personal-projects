<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListInfo extends Model
{
    use SoftDeletes;
    protected $table = "conf_mailing_list_info";
    protected $fillable = ['mailing_list_id','name', 'value'];

    // Input Fields for customization
    const FIELDS = [
        'content' => ''
        , 'background_color' => '#fff'
        , 'button_label' => 'Subscribe'
        , 'css' => '.eb-root {
                    /*  style root element */
                    
                    }
                    
                    #eb-content{
                    /* Style Content section */
                    
                    }
                    
                    .eb-root form label {
                     /* Style Input Labels */
                    }
                    
                    .eb-root form input[type="text"] {
                    /* Style Input fields */
                    }
                    
                    .eb-root form input[type="submit"] {
                    /* Style Subscribe Button */
                    
                    }
                    
                    .eb-form-group{
                    /* Style input field\'s parent element*/
                    
                    } 
                    
                    .eb-root form .checkbox{
                        /* Style checkbox*/
                    
                    }
                    
                    #eb-checkbox-content{
                        /* Style checkbox Content*/
                    }
                    
                    #eb_form_message{
                        /* Style Form Submission Message */
                    }
                    '
        , 'show_checkbox' => 0
        ,'checkbox_content' => 'Terms and Conditions'
        , 'first_name_label' => 'First name'
        , 'last_name_label' => 'Last name'
        , 'email_label' => 'Email'
        ];


    public function mailingList(){
        return $this->belongsTo(MailingList::class, 'mailing_list_id');
    }
}