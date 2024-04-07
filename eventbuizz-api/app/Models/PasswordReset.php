<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordReset extends Model
{
    protected $table = 'conf_password_resets';
    protected $fillable = ['email', 'token'];


    /**
     * return sales agent object
     *
     * @return BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(SaleAgent::class, 'email', 'email');
    }

}
