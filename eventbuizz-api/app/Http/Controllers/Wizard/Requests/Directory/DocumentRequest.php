<?php

namespace App\Http\Controllers\Wizard\Requests\Directory;

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
        if (!\Route::is('wizard-directory-upload-document')) {
            if (\Route::is('wizard-directory-rename-document-file')) {
                $rules = [
                    'name' => 'bail|required',
                ];
            } else if (\Route::is('wizard-directory-schedule-document')) {
                $rules = [
                    'start_date' => 'bail|required|date',
                    'start_time' => 'bail|required|date_format:H:i',
                ];
            } else {
                if (request()->module == "agendas") {
                    $rules['name'] = ["bail", "required"];
                    if ($this->request->get('agenda_id')) {
                        $rules['agenda_id'] = [
                            'bail',
                            'required',
                            Rule::unique('conf_directory')->where(function ($query) {
                                $query = $query->where('agenda_id', $this->request->get('agenda_id'))
                                    ->where('event_id', $this->request->get('event_id'))
                                    ->whereNull('deleted_at');
                                if (\Route::is('wizard-directory-update-document')) {
                                    $query->where('id', '!=', request()->id);
                                }
                                return $query;
                            }),
                        ];
                    }
                } else if (request()->module == "speakers") {
                    $rules['name'] = ["bail", "required"];
                    if ($this->request->get('speaker_id')) {
                        $rules['speaker_id'] = [
                            'bail',
                            'required',
                            Rule::unique('conf_directory')->where(function ($query) {
                                $query = $query->where('speaker_id', $this->request->get('speaker_id'))
                                    ->where('event_id', $this->request->get('event_id'))
                                    ->whereNull('deleted_at');
                                if (\Route::is('wizard-directory-update-document')) {
                                    $query->where('id', '!=', request()->id);
                                }
                                return $query;
                            }),
                        ];
                    }
                } else if (request()->module == "sponsors") {
                    $rules['name'] = ["bail", "required"];
                    if ($this->request->get('sponsor_id')) {
                        $rules['sponsor_id'] = [
                            'bail',
                            'required',
                            Rule::unique('conf_directory')->where(function ($query) {
                                $query = $query->where('sponsor_id', $this->request->get('sponsor_id'))
                                    ->where('event_id', $this->request->get('event_id'))
                                    ->whereNull('deleted_at');
                                if (\Route::is('wizard-directory-update-document')) {
                                    $query->where('id', '!=', request()->id);
                                }
                                return $query;
                            }),
                        ];
                    }
                } else if (request()->module == "exhibitors") {
                    $rules['name'] = ["bail", "required"];
                    if ($this->request->get('exhibitor_id')) {
                        $rules['exhibitor_id'] = [
                            'bail',
                            'required',
                            Rule::unique('conf_directory')->where(function ($query) {
                                $query = $query->where('exhibitor_id', $this->request->get('exhibitor_id'))
                                    ->where('event_id', $this->request->get('event_id'))
                                    ->whereNull('deleted_at');
                                if (\Route::is('wizard-directory-update-document')) {
                                    $query->where('id', '!=', request()->id);
                                }
                                return $query;
                            }),
                        ];
                    }
                } else if (request()->module == "other") {
                    $rules['name'] = ["bail", "required"];
                }
            }
        }
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
        $allowedfileExtension = ['png', 'jpg', 'ico', 'jpeg', 'gif', 'svg', 'bmp', 'xlsx', 'xls', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'pdf', 'ics', 'zip', 'rar', 'csv'];
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
