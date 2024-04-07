<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderSection extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_sections';
    protected $fillable = ['id', 'form_builder_form_id', 'next_section', 'sort_order'];

    // Relations
    public function info()
    {
        return $this->hasMany('\App\Models\FormBuilderSectionInfo', 'section_id');
        
    }

    public function questions()
    {
        return $this->hasMany('\App\Models\FormBuilderQuestion', 'form_builder_section_id')->orderby('sort_order');
    }
    
    // Handlers
    public function delete()
    {
        
        $this->info()->delete();
        foreach($this->questions as $question){
            $question->delete();
        }
        // delete the user
        return parent::delete();
    }
}
