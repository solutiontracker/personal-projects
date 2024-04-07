<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute skal accepteres.',
    'active_url'           => ':attribute er ikke en gyldig URL.',
    'after'                => ':attribute skal være større end :date.',
    'after_or_equal'       => ':attribute skal være større end eller lig med :date.',
    'alpha'                => ':attribute må kun indeholde bogstaver.',
    'alpha_dash'           => ':attribute må kun indeholde bogstaver, tal og bindestreger.',
    'alpha_num'            => ':attribute må kun iindeholde bogstaver eller tal.',
    'array'                => ':attribute skal være must be an matrix.',
    'before'               => ':attribute skal være mindre end :date.',
    'before_or_equal'      => ':attribute skal være mindre end eller lig med :date.',
    'between'              => [
        'numeric' => ':attribute skal være mellem :min og :max.',
        'file'    => ':attribute skal være mellem :min og :max kilobyte.',
        'string'  => ':attribute skal være mellem :min og :max tegn.',
        'array'   => ':attribute skal være mellem :min og :max elementer.',
    ],
    'boolean'              => ':attribute felt skal være sandt eller falsk.',
    'confirmed'            => ':attribute er ikke den samme, som du har indtastet ovenfor.',
    'date'                 => ':attribute er ikke en gyldig dato.',
    'date_format'          => ':attribute stemmer ikke overens med formatet :format.',
    'different'            => ':attribute og :other skal være forskellige.',
    'digits'               => ':attribute skal være :digits tal.',
    'digits_between'       => ':attribute skal være mellem :min og :max.',
    'dimensions'           => ':attribute har ugyldige billedstørrelse.',
    'distinct'             => ':attribute feltet har en duplikatværdi.',
    'email'                => ':attribute skal være gyldig email.',
    'exists'               => 'Den valgte :attribute er ugyldig.',
    'file'                 => ':attribute skal være en fil.',
    'filled'               => ':attribute feltet skal indeholde en værdi.',
    'image'                => ':attribute skal være et billede.',
    'in'                   => 'Den valgte :attribute er ugyldig.',
    'in_array'             => ':attribute feltet eksister ikke i :other.',
    'integer'              => ':attribute skal være heltal.',
    'ip'                   => ':attribute skal være gyldig IP adresse.',
    'ipv4'                 => ':attribute skal være gyldig IPv4 adresse.',
    'ipv6'                 => ':attribute skal være gyldig IPv6 adresse.',
    'json'                 => ':attribute skal være gyldig JSON række.',
    'max'                  => [
        'numeric' => ':attribute må ikke være større end :max.',
        'file'    => ':attribute må ikke være større end :max kilobyte.',
        'string'  => ':attribute må ikke indeholde flere end :max tegn.',
        'array'   => ':attribute må ikke indeholde flere end :max elementer.',
    ],
    'mimes'                => ':attribute skal være filtype: :values.',
    'mimetypes'            => ':attribute skal være filtype: :values.',
    'min'                  => [
        'numeric' => ':attribute skal mindst være :min.',
        'file'    => ':attribute skal mindst være :min kilobyte.',
        'string'  => ':attribute skal mindst indeholde :min tegn.',
        'array'   => ':attribute skal mindst indeholde :min elementer.',
    ],
    'not_in'               => 'Valgte :attribute er ikke gyldig.',
    'numeric'              => ':attribute skal være et tal.',
    'present'              => ':attribute feltet skal være synlig.',
    'regex'                => ':attribute formatet er ugyldigt.',
    'required'             => 'Feltet er krævet.',
    'required_if'          => ':attribute feltet er krævet når :other er :value.',
    'required_unless'      => ':attribute feltet er krævet medmindre :other er i :values.',
    'required_with'        => ':attribute feltet er krævet når :values er synlig.',
    'required_with_all'    => ':attribute feltet er krævet når :values er synlig.',
    'required_without'     => ':attribute feltet er krævet når :values ikke er til stede.',
    'required_without_all' => ':attribute feltet er krævet når ingen af :values er til stede.',
    'same'                 => ':attribute og :other skal matche.',
    'size'                 => [
        'numeric' => ':attribute skal være :size.',
        'file'    => ':attribute skal være :size kilobyte.',
        'string'  => ':attribute skal indeholde :size tegn.',
        'array'   => ':attribute skal indeholde :size elementer.',
    ],
    'string'               => ':attribute skal være en række.',
    'timezone'             => ':attribute skal være i gyldig zone.',
    'unique'               => ':attribute er allerede taget.',
    'uploaded'             => ':attribute kunne ikke uploades.',
    'url'                  => ':attribute formatet er ugyldigt.',
    'phone'                  => ':attribute formatet er ugyldigt.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'header_logo' => [
            'base64image' => 'Hovedlogoet skal være et billede.',
        ],
        'app_icon' => [
            'base64image' => 'Appikonet skal være et billede.',
        ],
        'social_media_logo' => [
            'base64image' => 'Sociale medier skal være et billede.',
        ],
        'fav_icon' => [
            'base64image' => 'Favikonen skal være et billede.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'first_name' => 'fornavn',
        'email' => 'email',
        'program_id' => 'program',
        'support_email' => 'support email',
        'name' => 'navn',
        'url' => 'url',
        'phone' => 'telefon',
        'image' => 'billede',
        'organizer_name' => 'organizer navn',
        'timezone_id' => 'tidszone',
        'dateformat' => 'dato format',
        'start_date' => 'start dato',
        'end_date' => 'slut dato',
        'country_id' => 'land',
        'assign_package_id' => 'pakke',
        'location_name' => 'lokation',
        'location_address' => 'lokations adresse',
        'sms_organizer_name' => 'sms afsender navn',
        'file' => 'fil',
        'column' => 'kolonne',
        'topic' => 'emne',
        'date' => 'dato',
        'start_time' => 'starttidspunktet',
        'end_time' => 'Sluttidspunkt',
        'question' => 'spørgsmål',
        'question_type' => 'spørgsmålstype',
        'price' => 'pris',
        'from_date' => 'fra dato',
        'to_date' => 'til dato',
        'subject' => 'emne',
        'inline_text' => 'inline tekst',
        'menu_id' => 'menu',
        'pdf' => 'pdf',
        'pdf_title' => 'pdf titel',
        'page_type' => 'pdf type',
    ],

];
