<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationQuestion extends Model
{
	use SoftDeletes;
	
	protected $attributes = [
		'required_question' => '0',
		'enable_comments' => '0',
		'link_to' => '0',
		'max_options' => '0',
	];

	protected $table = 'conf_event_sub_registration_questions';

    protected $fillable = ['question_type', 'required_question', 'enable_comments', 'sort_order', 'sub_registration_id', 'status', 'link_to', 'min_options','max_options'];
	
	protected $dates = ['deleted_at'];

	public function info()
	{
		return $this->hasMany('\App\Models\EventSubRegistrationQuestionInfo', 'question_id');
	}

	public function answer()
	{
		return $this->hasMany('\App\Models\EventSubRegistrationAnswer', 'question_id');
	}

	public function result()
	{
		return $this->hasMany('\App\Models\EventSubRegistrationResult', 'question_id');
	}

    public function matrix()
    {
        return $this->hasMany(EventSubregistrationMatrix::class, 'question_id');
    }

}