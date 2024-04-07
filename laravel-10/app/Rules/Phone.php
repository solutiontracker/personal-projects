<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->isValidTelephoneNumber($value);
    }
        
    /**
     * isDigits
     *
     * @param  mixed $s
     * @param  mixed $minDigits
     * @param  mixed $maxDigits
     * @return bool
     */
    function isDigits(string $s, int $minDigits = 9, int $maxDigits = 14): bool {
        return preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $s);
    }
    
    /**
     * isValidTelephoneNumber
     *
     * @param  mixed $telephone
     * @param  mixed $minDigits
     * @param  mixed $maxDigits
     * @return bool
     */
    function isValidTelephoneNumber(string $telephone, int $minDigits = 9, int $maxDigits = 14): bool {
        if (preg_match('/^[+][0-9]/', $telephone)) { //is the first character + followed by a digit
            $count = 1;
            $telephone = str_replace(['+'], '', $telephone, $count); //remove +
        }
        
        //remove white space, dots, hyphens and brackets
        $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone); 
    
        //are we left with digits only?
        return $this->isDigits($telephone, $minDigits, $maxDigits); 
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $labels = request()->event['labels'];
        
        return $labels['REGISTRATION_FORM_ENTER_VALID_PHONE_NUMBER'];
    }
}
