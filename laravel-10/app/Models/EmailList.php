<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailList extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $connection= 'mysql_email_logs';

    protected $table= "conf_email_lists";

    protected $fillable=  [ 'email',  'bounced',  'active' ];

}
