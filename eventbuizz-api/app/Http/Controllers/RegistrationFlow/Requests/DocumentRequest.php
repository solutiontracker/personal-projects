<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array();
        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $allowedfileExtension = ['png', 'jpg','jpeg','svg',  'xlsx', 'xls', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'pdf','csv', 'zip'];
        if (\Route::is('wizard-directory-upload-document')) {
            $files = request()->file('files');
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if (!$check) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add('files', __('messages.import_file_format'));
                    });
                }
            }
        }
    }
}
