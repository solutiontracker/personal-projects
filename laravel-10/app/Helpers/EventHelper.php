<?php

use App\Models\EventsiteTopMenu;

use Carbon\Carbon;
use Pelago\Emogrifier\CssInliner;



function get_event_languages($event_id)
{
    $language = array();
    $model = \App\Models\Event::find($event_id);
    $language[] = $model->language_id;
    return $language;
}
/*
 * return all active status record
 */
function get_all_languages()
{
    $languages = \App\Models\Language::where('status', 1)->select('id', 'name')->get();
    return $languages;
}

function get_all_countries_languages()
{
    $allCountry = \App\Models\Country::orderBy('language_name','ASC')->groupBy("language_name")->get()->toArray();
    $returnArray=[];
    foreach ($allCountry as $country) {
        $returnArray[] = [
            "id"=> $country['id'],
            "name"=>$country['language_name']
        ];
    }
    return $returnArray;
}

function get_all_countries()
{
    $countries = \App\Models\Country::select('id', 'name')->orderBy('name')->get()->toArray();
    return $countries;
}

function get_all_timezones()
{
    $timezones = \App\Models\Timezone::select('id', 'name')->get();
    return $timezones;
}

function get_date_formats()
{
    return \App\Models\DateFormat::all();
}

function get_all_country_codes()
{
    $country_codes = \App\Models\Country::whereNotNull('calling_code')->where('calling_code', '<>', 0)
        ->select(\DB::raw("CONCAT('+',calling_code) as ID"), \DB::raw("CONCAT('+',calling_code) as name"), "name as country")->orderBy('calling_code')->groupBy('calling_code')->get();

    $array = array();
    foreach ($country_codes as $key => $row) {
        $array[$key]['id'] = $row->ID;
        $array[$key]['name'] = $row->name;
        $array[$key]['country'] = $row->country;
    }
    return $array;
}

function eventsite_labels($alias = '', $params = array(), $label = '')
{
    $event_id = $params['event_id'];

    $language_id = $params['language_id'];

    if(is_array($alias))
        $key = 'event-labels-'. $event_id. implode("-", $alias). $label;
    else
        $key = 'event-labels-' . $event_id . $alias . $label;

    if (Cache::tags('event-labels-'.$event_id)->has($key)) {
        return Cache::tags('event-labels-'.$event_id)->get($key);
    } else {
        $query = \App\Models\EventSiteText::where('event_id', '=', $event_id);

        $query->where('parent_id', '=', '0');

        if (is_array($alias))
            $query->whereIn('module_alias', $alias);
        else if ($alias)
            $query->where('module_alias', $alias);

        $query->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }]);

        $query->with(['children' => function ($r) {
            return $r->orderBy('constant_order');
        }, 'children.childrenInfo' => function ($r) use ($language_id) {
            return $r->where('languages_id', '=', $language_id);
        }]);

        $labels = $query->orderBy('section_order')->get()->toArray();

        $labels_array = [];

        foreach ($labels as $record) {
            foreach ($record['children'] as $row) {
                if (count($row['children_info']) > 0) {
                    foreach ($row['children_info'] as $val) {
                        $labels_array[trim($row['alias'])] = $val['value'];
                    }
                }
            }
        }

        $expiresAt = \Carbon\Carbon::now()->addHours(24);

        if($label) {
            Cache::tags('event-labels-'.$event_id)->put($key, $labels_array[$label], $expiresAt);
            return $labels_array[$label];
        } else {
            Cache::tags('event-labels-'.$event_id)->put($key, $labels_array, $expiresAt);
            return $labels_array;
        }
    }
}

function get_package_modules($organizer_id, $module = null)
{
    $package = \App\Models\AssignPackage::where('organizer_id', '=', $organizer_id)
        ->with('assignPackageAddons')
        ->first();
    if (!$module) {
        return $package;
    } else {
        foreach ($package->assignPackageAddons as $addon) {
            if ($addon['alias'] == $module) {
                return true;
            }
        }
        return false;
    }
}

function get_modules($language_id, $id = '')
{
    $modules = array(
        '2' => array(
            '1' => array('1', 'Program', 'agendas'),
            '2' => array('2', 'Deltagere', 'attendees'),
            '3' => array('3', 'Talere', 'speakers'),
            '4' => array('4', 'Netværk interesse', 'business'),
            '5' => array('5', 'Dokumenter', 'ddirectory'),
            '6' => array('6', 'Sponsors', 'agendas'),
            '7' => array('7', 'Udstillere', 'exhibitors'),
            '8' => array('8', 'Banner ad', 'banner'),
            '9' => array('9', 'Afstemninger', 'polls'),
            '10' => array('10', 'Q&A', 'qa'),
            '11' => array('11', 'Nyheder og opdateringer', 'alerts'),
            '12' => array('12', 'Praktisk information', 'infobooth'),
            '13' => array('13', 'Sociale medier', 'social'),
            '14' => array('14', 'Kort', 'maps'),
            '15' => array('15', 'Konkurrence', 'competition'),
            '16' => array('16', 'Del', 'share'),
            '17' => array('17', 'Billedgalleri', 'gallery'),
            '18' => array('18', 'Badge', 'badge'),
            '19' => array('19', 'Floor plan', 'plans'),
            '20' => array('20', 'Tjek ind', 'checkIn'),
            '21' => array('21', 'Mine dokumenter', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Noter', 'notes'),
            '24' => array('24', 'Event site', 'eventsite'),
            '25' => array('25', 'Eventsite payment', 'eventsite_payment'),
            '26' => array('26', 'Mobile app', 'mobile_app'),
            '27' => array('27', 'Event details', 'event_detail'),
            '28' => array('28', 'Customize', 'customize'),
            '29' => array('29', 'Ekstra deltageroplysninger', 'subregistration'),
            '30' => array('30', 'Templates', 'templates'),
            '31' => array('31', 'Analytics', 'analytics'),
            '32' => array('32', 'SMS history', 'sms'),
            '33' => array('33', 'Program', 'sms_history'),
            '34' => array('34', 'Talerliste', 'myturnlist'),
            '35' => array('35', 'Rediger profil', 'editprofile'),
            '36' => array('36', 'Email mine noter', 'emailmynotes'),
            '37' => array('37', 'Mine sponsors', 'mysponsers'),
            '38' => array('38', 'Mine udstillere', 'myexhibitors'),
            '39' => array('39', 'Mine events', 'myevents'),
            '40' => array('40', 'Mine interesser', 'mykeywords'),
            '41' => array('41', 'Administrer dokumenter', 'managedocuments'),
            '42' => array('42', 'Mine spørgsmål', 'myquestions'),
            '43' => array('43', 'Afstemninger', 'livepolls'),
            '44' => array('44', 'Mine evalueringer', 'livesurveys'),
            '45' => array('45', 'Settings', 'settings'),
            '46' => array('46', 'Sub registration(s)', 'subregistration'),
            '47' => array('47', 'Log ud', 'logout'),
            '48' => array('48', 'Mit program', 'myagendas'),
            '49' => array('49', 'Mine deltagere', 'myattendees'),
            '50' => array('50', 'Home mine events', 'homeMyevents'),
            '51' => array('51', 'Mine reservationer', 'my-reservations'),
            '52' => array('52', 'Mine afstemningsresultater', 'myPollResults'),
            '53' => array('53', 'Mine evalueringsresultater', 'mySurveyResults'),
            '54' => array('54', 'Social væg', 'social_wall'),
            '55' => array('55', 'Se profil', 'view_profile'),
            '56' => array('56', 'Mine noter', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Indtjekning check-in', 'checkin_agendas'),
            '60' => array('60', 'Yderligere Information', 'additional_info'),
            '61' => array('61', 'Generelle oplysninger', 'general_info'),
            '62' => array('62', 'Billetter', 'tickets'),
            '63' => array('63', 'API Nøgle', 'api_key'),
            '64' => array('64', 'Postliste', 'mailing_list'),
            '65' => array('65', 'Email marketing', 'email_marketing'),
            '66' => array('66', 'Leads', 'leadsmanagment'),
            '67' => array('67', 'Kommende events', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Helpdesk', 'help_desk'),
            '70' => array('70', 'Helpdesk spørgsmål', 'hdquestions'),
            '71' => array('71', 'Nyheder', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '3' => array(
            '1' => array('1', 'Program', 'agendas'),
            '2' => array('2', 'Deltakere', 'attendees'),
            '3' => array('3', 'Talere', 'speakers'),
            '4' => array('4', 'Network interesse', 'business'),
            '5' => array('5', 'Dokumenter', 'ddirectory'),
            '6' => array('6', 'Sponsorer', 'sponsors'),
            '7' => array('7', 'Utstillere', 'exhibitors'),
            '8' => array('8', 'Bannerannonser', 'banner'),
            '9' => array('9', 'Avstemminger og undersøkelser', 'polls'),
            '10' => array('10', 'Spørsmål og svar', 'qa'),
            '11' => array('11', 'Nyheter og oppdateringer', 'alerts'),
            '12' => array('12', 'Praktisk informasjon', 'infobooth'),
            '13' => array('13', 'Sosiale medier', 'social'),
            '14' => array('14', 'Kart', 'maps'),
            '15' => array('15', 'Konkurranse', 'competition'),
            '16' => array('16', 'Del', 'share'),
            '17' => array('17', 'Bildegalleri', 'gallery'),
            '18' => array('18', 'Merke', 'badge'),
            '19' => array('19', 'Etasjeplan', 'plans'),
            '20' => array('20', 'Sjekk inn', 'checkIn'),
            '21' => array('21', 'Mine dokumenter', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Merknader', 'notes'),
            '24' => array('24', 'Arrangementets nettsted', 'eventsite'),
            '25' => array('25', 'Betaling på arrangementets nettsted', 'eventsite_payment'),
            '26' => array('26', 'Mobil-app', 'mobile_app'),
            '27' => array('27', 'Detaljer om arrangementet', 'event_detail'),
            '28' => array('28', 'Tilpass', 'customize'),
            '29' => array('29', 'Underregistreringer', 'subregistration'),
            '30' => array('30', 'Maler', 'templates'),
            '31' => array('31', 'Analyse', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'SMS-historikk', 'sms_history'),
            '34' => array('34', 'Talerliste', 'myturnlist'),
            '35' => array('35', 'Rediger profil', 'editprofile'),
            '36' => array('36', 'Send notater via e-post', 'emailmynotes'),
            '37' => array('37', 'Mine sponsorer', 'mysponsers'),
            '38' => array('38', 'Mine utstillere', 'myexhibitors'),
            '39' => array('39', 'Mine arrangementer', 'myevents'),
            '40' => array('40', 'Mine nøkkelord', 'mykeywords'),
            '41' => array('41', 'Behandle dokumenter', 'managedocuments'),
            '42' => array('42', 'Mine spørsmål', 'myquestions'),
            '43' => array('43', 'Live-avstemminger', 'livepolls'),
            '44' => array('44', 'Mine undersøkelser', 'livesurveys'),
            '45' => array('45', 'Innstillinger', 'settings'),
            '46' => array('46', 'Underregistrering(er)', 'subregistration'),
            '47' => array('47', 'Logg ut', 'logout'),
            '48' => array('48', 'Mitt program', 'myagendas'),
            '49' => array('49', 'Mine deltakere', 'myattendees'),
            '50' => array('50', 'Hjem mine arrangementer', 'homeMyevents'),
            '51' => array('51', 'Mine bestillinger', 'my-reservations'),
            '52' => array('52', 'Mine avstemmingsresultater', 'myPollResults'),
            '53' => array('53', 'Mine undersøkelsesresultater', 'mySurveyResults'),
            '54' => array('54', 'Sosial veggen', 'social_wall'),
            '55' => array('55', 'Vis profil', 'view_profile'),
            '56' => array('56', 'Mine notater', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Innlevering av sesjon', 'checkin_agendas'),
            '60' => array('60', 'Tilleggsinformasjon', 'additional_info'),
            '61' => array('61', 'Generell informasjon', 'general_info'),
            '62' => array('62', 'Billetter', 'tickets'),
            '63' => array('63', 'API Nøkkel', 'api_key'),
            '64' => array('64', 'Mailingliste', 'mailing_list'),
            '65' => array('65', 'Epost markedsføring', 'email_marketing'),
            '66' => array('66', 'Leder ledelse', 'leadsmanagment'),
            '67' => array('67', 'Kommende arrangementer', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Helpdesk', 'help_desk'),
            '70' => array('70', 'Helpdesk spørsmål', 'hdquestions'),
            '71' => array('71', 'Nyheter', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '4' => array(
            '1' => array('1', 'Programm', 'agendas'),
            '2' => array('2', 'Teilnehmer', 'attendees'),
            '3' => array('3', 'Redner', 'speakers'),
            '4' => array('4', 'Netzwerk Interesse', 'business'),
            '5' => array('5', 'Dokumente', 'ddirectory'),
            '6' => array('6', 'Sponsoren', 'sponsors'),
            '7' => array('7', 'Aussteller', 'exhibitors'),
            '8' => array('8', 'Banner-Werbung', 'banner'),
            '9' => array('9', 'Abstimmungen & Umfragen', 'polls'),
            '10' => array('10', 'Q&A', 'qa'),
            '11' => array('11', 'Alert & Updates', 'alerts'),
            '12' => array('12', 'Praktische Informationen', 'infobooth'),
            '13' => array('13', 'Sozialen Medien', 'social'),
            '14' => array('14', 'Karte', 'maps'),
            '15' => array('15', 'Wettbewerb', 'competition'),
            '16' => array('16', 'Teilen', 'share'),
            '17' => array('17', 'Bildergalerie', 'gallery'),
            '18' => array('18', 'Abzeichen', 'badge'),
            '19' => array('19', 'Grundriss', 'plans'),
            '20' => array('20', 'Ankunft', 'checkIn'),
            '21' => array('21', 'Meine Dokumente', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Notizen', 'notes'),
            '24' => array('24', 'Veranstaltungsort', 'eventsite'),
            '25' => array('25', 'Veranstaltungsort Zahlung', 'eventsite_payment'),
            '26' => array('26', 'Mobile App', 'mobile_app'),
            '27' => array('27', 'Details zur Veranstaltung', 'event_detail'),
            '28' => array('28', 'Anpassen', 'customize'),
            '29' => array('29', 'Sub-Registrierungen', 'subregistration'),
            '30' => array('30', 'Vorlagen', 'templates'),
            '31' => array('31', 'Analysen', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'SMS-Verlauf', 'sms_history'),
            '34' => array('34', 'Rednerliste', 'myturnlist'),
            '35' => array('35', 'Profil bearbeiten', 'editprofile'),
            '36' => array('36', 'Meine Notizen per E-Mail verschicken', 'emailmynotes'),
            '37' => array('37', 'Meine Sponsoren', 'mysponsers'),
            '38' => array('38', 'Mein Aussteller', 'myexhibitors'),
            '39' => array('39', 'Meine Veranstaltungen', 'myevents'),
            '40' => array('40', 'Meine Schlüsselbegriffe', 'mykeywords'),
            '41' => array('41', 'Verwalten von Dokumenten', 'managedocuments'),
            '42' => array('42', 'Meine Fragen', 'myquestions'),
            '43' => array('43', 'Live Umfragen', 'livepolls'),
            '44' => array('44', 'Meine Umfragen', 'livesurveys'),
            '45' => array('45', 'Einstellungen', 'settings'),
            '46' => array('46', 'Sub-Registrierungen', 'subregistration'),
            '47' => array('47', 'Abmelden', 'logout'),
            '48' => array('48', 'Mein Programm', 'myagendas'),
            '49' => array('49', 'Meine Teilnehmer', 'myattendees'),
            '50' => array('50', 'Meine Veranstaltungen auf Startseite setzen', 'homeMyevents'),
            '51' => array('51', 'Meine Buchungen', 'my-reservations'),
            '52' => array('52', 'Meine Abstimmungsergebnisse', 'myPollResults'),
            '53' => array('53', 'Meine Umfrageergebnisse', 'mySurveyResults'),
            '54' => array('54', 'Sozial Wand', 'social_wall'),
            '55' => array('55', 'Profil anzeigen', 'view_profile'),
            '56' => array('56', 'Meine Notizen', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Sitzungs-Check-in', 'checkin_agendas'),
            '60' => array('60', 'Zusätzliche Information', 'additional_info'),
            '61' => array('61', 'Allgemeine Information', 'general_info'),
            '62' => array('62', 'Tickets', 'tickets'),
            '63' => array('63', 'API Schlüssel', 'api_key'),
            '64' => array('64', 'Mailingliste', 'mailing_list'),
            '65' => array('65', 'E-Mail Marketing', 'email_marketing'),
            '66' => array('66', 'Leitet das Management', 'leadsmanagment'),
            '67' => array('67', 'Kommende Veranstaltungen', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Beratungsstelle', 'help_desk'),
            '70' => array('70', 'Helpdesk-Fragen', 'hdquestions'),
            '71' => array('71', 'Nachrichten', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '5' => array(
            '1' => array('1', 'Programa', 'agendas'),
            '2' => array('2', 'Dalyviai', 'attendees'),
            '3' => array('3', 'Praneðëjai', 'speakers'),
            '4' => array('4', 'Tinklo palūkanų', 'business'),
            '5' => array('5', 'Dokumentai', 'ddirectory'),
            '6' => array('6', 'Rëmëjai', 'sponsors'),
            '7' => array('7', 'Parodos dalyviai', 'exhibitors'),
            '8' => array('8', 'Reklaminiai skydeliai', 'banner'),
            '9' => array('9', 'Apklausos ir balsavimai', 'polls'),
            '10' => array('10', 'Klausimai ir atsakymai', 'qa'),
            '11' => array('11', 'Naujienos', 'alerts'),
            '12' => array('12', 'Praktinë informacija', 'infobooth'),
            '13' => array('13', 'Soc. Medijos', 'social'),
            '14' => array('14', 'Þemëlapis', 'maps'),
            '15' => array('15', 'Konkursas', 'competition'),
            '16' => array('16', 'Dalintis', 'share'),
            '17' => array('17', 'Nuotraukos', 'gallery'),
            '18' => array('18', 'Dalyvio kortelës', 'badge'),
            '19' => array('19', 'Salës planas', 'plans'),
            '20' => array('20', 'Bilietai', 'checkIn'),
            '21' => array('21', 'Mano dokumentai', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Uþraðau', 'notes'),
            '24' => array('24', 'Registracijos puslapis', 'eventsite'),
            '25' => array('25', 'Registracijos puslapio mokëjimai', 'eventsite_payment'),
            '26' => array('26', 'Mob. aplikacija', 'mobile_app'),
            '27' => array('27', 'Renginio informacija', 'event_detail'),
            '28' => array('28', 'Pritaikyti', 'customize'),
            '29' => array('29', 'Sub-registracija', 'subregistration'),
            '30' => array('30', 'Ðablonai', 'templates'),
            '31' => array('31', 'Analitika', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'SMS istorija', 'sms_history'),
            '34' => array('34', 'Praneðëjø sàraðas', 'myturnlist'),
            '35' => array('35', 'Profilio redagavimas', 'editprofile'),
            '36' => array('36', 'Parsisiøsti savo uþraðus', 'emailmynotes'),
            '37' => array('37', 'Paþymëti rëmëjai', 'mysponsers'),
            '38' => array('38', 'Paþymëti parodos dalyviai', 'myexhibitors'),
            '39' => array('39', 'Mano renginiai', 'myevents'),
            '40' => array('40', 'Paþymëti raktaþodþiai', 'mykeywords'),
            '41' => array('41', 'Tvarkyti dokumentus', 'managedocuments'),
            '42' => array('42', 'Mano klausimai', 'myquestions'),
            '43' => array('43', 'Vykstantys balsavimai', 'livepolls'),
            '44' => array('44', 'Mano apklausos', 'livesurveys'),
            '45' => array('45', 'Nustatymai', 'settings'),
            '46' => array('46', 'Sub-registracija', 'subregistration'),
            '47' => array('47', 'Iðeiti', 'logout'),
            '48' => array('48', 'Mano programa', 'myagendas'),
            '49' => array('49', 'Paþymëti dalyviai', 'myattendees'),
            '50' => array('50', 'Perþiûrëti mano renginius', 'homeMyevents'),
            '51' => array('51', 'Mano susitikimai', 'my-reservations'),
            '52' => array('52', 'Mano balsavimø rezultatai', 'myPollResults'),
            '53' => array('53', 'Mano apklausø rezultatai', 'mySurveyResults'),
            '54' => array('54', 'Socialinė sienos', 'social_wall'),
            '55' => array('55', 'Peržiūrėti vartotojo profilį', 'view_profile'),
            '56' => array('56', 'Mano užrašai', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Sesijos registracija', 'checkin_agendas'),
            '60' => array('60', 'Papildoma informacija', 'additional_info'),
            '61' => array('61', 'Bendra informacija', 'general_info'),
            '62' => array('62', 'Bilietai', 'tickets'),
            '63' => array('63', 'API Raktas', 'api_key'),
            '64' => array('64', 'Pašto adresų sąrašas', 'mailing_list'),
            '65' => array('65', 'Rinkodara el. Paštu', 'email_marketing'),
            '66' => array('66', 'Vadovauja vadovybei', 'leadsmanagment'),
            '67' => array('67', 'Artimiausi renginiai', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Pagalbos tarnyba', 'help_desk'),
            '70' => array('70', 'Pagalbos tarnybos klausimai', 'hdquestions'),
            '71' => array('71', 'žinios', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '6' => array(
            '1' => array('1', 'Ohjelma', 'agendas'),
            '2' => array('2', 'Osallistujat', 'attendees'),
            '3' => array('3', 'Puhujat', 'speakers'),
            '4' => array('4', 'verkko kiinnostusta', 'business'),
            '5' => array('5', 'Asiakirjat', 'ddirectory'),
            '6' => array('6', 'Sponsorit', 'sponsors'),
            '7' => array('7', 'Näytteilleasettajat', 'exhibitors'),
            '8' => array('8', 'Bannerimainokset', 'banner'),
            '9' => array('9', 'Gallupit ja kyselyt', 'polls'),
            '10' => array('10', 'Kysymykset ja vastaukset', 'qa'),
            '11' => array('11', 'Uutiset ja päivitykset', 'alerts'),
            '12' => array('12', 'Hyödyllistä tietoa', 'infobooth'),
            '13' => array('13', 'Sosiaalinen media', 'social'),
            '14' => array('14', 'Kartta', 'maps'),
            '15' => array('15', 'Kilpailu', 'competition'),
            '16' => array('16', 'Jaa', 'share'),
            '17' => array('17', 'Kuvagalleria', 'gallery'),
            '18' => array('18', 'Nimikyltti', 'badge'),
            '19' => array('19', 'Pohjapiirros', 'plans'),
            '20' => array('20', 'Kirjaudu sisään', 'checkIn'),
            '21' => array('21', 'Omat asiakirjat', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Huomautukset', 'notes'),
            '24' => array('24', 'Tapahtumasivusto', 'eventsite'),
            '25' => array('25', 'Tapahtumasivuston maksu', 'eventsite_payment'),
            '26' => array('26', 'Mobiilisovellus', 'mobile_app'),
            '27' => array('27', 'Tapahtuman tiedot', 'event_detail'),
            '28' => array('28', 'Mukauta', 'customize'),
            '29' => array('29', 'Alarekisteröitymiset', 'subregistration'),
            '30' => array('30', 'Mallit', 'templates'),
            '31' => array('31', 'Analytiikka', 'analytics'),
            '32' => array('32', 'Tekstiviestit', 'sms'),
            '33' => array('33', 'Tekstiviestihistoria', 'sms_history'),
            '34' => array('34', 'Puhujaluettelo', 'myturnlist'),
            '35' => array('35', 'Muokkaa profiilia', 'editprofile'),
            '36' => array('36', 'Lähetä huomautukset sähköpostiini', 'emailmynotes'),
            '37' => array('37', 'Omat sponsorit', 'mysponsers'),
            '38' => array('38', 'Omat näytteilleasettajat', 'myexhibitors'),
            '39' => array('39', 'Omat tapahtumat', 'myevents'),
            '40' => array('40', 'Omat avainsanat', 'mykeywords'),
            '41' => array('41', 'Hallitse asiakirjoja', 'managedocuments'),
            '42' => array('42', 'Omat kysymykset', 'myquestions'),
            '43' => array('43', 'Live-gallupit', 'livepolls'),
            '44' => array('44', 'Omat kyselyt', 'livesurveys'),
            '45' => array('45', 'Asetukset', 'settings'),
            '46' => array('46', 'Alarekisteröityminen/alarekisteröitymiset', 'subregistration'),
            '47' => array('47', 'Kirjaudu ulos', 'logout'),
            '48' => array('48', 'Oma ohjelma', 'myagendas'),
            '49' => array('49', 'Omat osallistujat', 'myattendees'),
            '50' => array('50', 'Etusivu – omat tapahtumat', 'homeMyevents'),
            '51' => array('51', 'Omat varaukset', 'my-reservations'),
            '52' => array('52', 'Omat gallup-tulokset', 'myPollResults'),
            '53' => array('53', 'Omat kyselytulokset', 'mySurveyResults'),
            '54' => array('54', 'Sosiaalinen seinä', 'social_wall'),
            '55' => array('55', 'Näytä profiili', 'view_profile'),
            '56' => array('56', 'Omat muistiinpanot', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Istunnon sisäänkirjautuminen', 'checkin_agendas'),
            '60' => array('60', 'Lisäinformaation', 'additional_info'),
            '61' => array('61', 'Yleistä tietoa', 'general_info'),
            '62' => array('62', 'Liput', 'tickets'),
            '63' => array('63', 'API Avain', 'api_key'),
            '64' => array('64', 'Postitus lista', 'mailing_list'),
            '65' => array('65', 'Sähköpostimarkkinointi', 'email_marketing'),
            '66' => array('66', 'Johtaa johtoa', 'leadsmanagment'),
            '67' => array('67', 'Tulevat tapahtumat', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Tukipalvelu', 'help_desk'),
            '70' => array('70', 'Helpdesk-kysymykset', 'hdquestions'),
            '71' => array('71', 'Uutiset', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '7' => array(
            '1' => array('1', 'Program', 'agendas'),
            '2' => array('2', 'Deltagare', 'attendees'),
            '3' => array('3', 'Talare', 'speakers'),
            '4' => array('4', 'nätverks intresse', 'business'),
            '5' => array('5', 'Dokument', 'ddirectory'),
            '6' => array('6', 'Sponsorer', 'sponsors'),
            '7' => array('7', 'Utställare', 'exhibitors'),
            '8' => array('8', 'Banner-ads', 'banner'),
            '9' => array('9', 'Omröstningar & enkäter', 'polls'),
            '10' => array('10', 'Q & A', 'qa'),
            '11' => array('11', 'Nyheter och uppdateringar', 'alerts'),
            '12' => array('12', 'Praktisk information', 'infobooth'),
            '13' => array('13', 'Sociala media', 'social'),
            '14' => array('14', 'Karta', 'maps'),
            '15' => array('15', 'Tävling', 'competition'),
            '16' => array('16', 'Dela', 'share'),
            '17' => array('17', 'Bildgalleri', 'gallery'),
            '18' => array('18', 'Märke', 'badge'),
            '19' => array('19', 'Våningskarta', 'plans'),
            '20' => array('20', 'Checka in', 'checkIn'),
            '21' => array('21', 'Mina dokument', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Anteckningar', 'notes'),
            '24' => array('24', 'Händelse-webbsida', 'eventsite'),
            '25' => array('25', 'Betalning på händelse-webbsida', 'eventsite_payment'),
            '26' => array('26', 'Mobil-app', 'mobile_app'),
            '27' => array('27', 'Händelseinformation', 'event_detail'),
            '28' => array('28', 'Anpassa', 'customize'),
            '29' => array('29', 'Underregistreringar', 'subregistration'),
            '30' => array('30', 'Mallar', 'templates'),
            '31' => array('31', 'Statistik', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'SMS-historik', 'sms_history'),
            '34' => array('34', 'Talarlista', 'myturnlist'),
            '35' => array('35', 'Redigera profil', 'editprofile'),
            '36' => array('36', 'Skicka mina anteckningar med e-post', 'emailmynotes'),
            '37' => array('37', 'Mina sponsorer', 'mysponsers'),
            '38' => array('38', 'Mina utställare', 'myexhibitors'),
            '39' => array('39', 'Mina händelser', 'myevents'),
            '40' => array('40', 'Mina nyckelord', 'mykeywords'),
            '41' => array('41', 'Hantera dokument', 'managedocuments'),
            '42' => array('42', 'Mina frågor', 'myquestions'),
            '43' => array('43', 'Live-omröstningar', 'livepolls'),
            '44' => array('44', 'Mina enkäter', 'livesurveys'),
            '45' => array('45', 'Inställningar', 'settings'),
            '46' => array('46', 'Underregistrering/-ar', 'subregistration'),
            '47' => array('47', 'Logga ut', 'logout'),
            '48' => array('48', 'Mitt program', 'myagendas'),
            '49' => array('49', 'Mina deltagare', 'myattendees'),
            '50' => array('50', 'Hem mina händelser', 'homeMyevents'),
            '51' => array('51', 'Mina bokningar', 'my-reservations'),
            '52' => array('52', 'Mina omröstningsresultat', 'myPollResults'),
            '53' => array('53', 'Mina enkätresultat', 'mySurveyResults'),
            '54' => array('54', 'social vägg', 'social_wall'),
            '55' => array('55', 'Visa profil', 'view_profile'),
            '56' => array('56', 'Mina anteckningar', 'my_notes'),
            '58' => array('58', 'Native App', 'nativeapp'),
            '59' => array('59', 'Session incheckningen', 'checkin_agendas'),
            '60' => array('60', 'Ytterligare information', 'additional_info'),
            '61' => array('61', 'Allmän information', 'general_info'),
            '62' => array('62', 'Biljetter', 'tickets'),
            '63' => array('63', 'API Nyckel', 'api_key'),
            '64' => array('64', 'Postlista', 'mailing_list'),
            '65' => array('65', 'E-post marknadsföring', 'email_marketing'),
            '66' => array('66', 'Ledar ledning', 'leadsmanagment'),
            '67' => array('67', 'Uppkommande händelser', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Kundtjänst', 'help_desk'),
            '70' => array('70', 'Helpdesk frågor', 'hdquestions'),
            '71' => array('71', 'Nyheter', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '8' => array(
            '1' => array('1', 'Programma', 'agendas'),
            '2' => array('2', 'Deelnemers', 'attendees'),
            '3' => array('3', 'Sprekers', 'speakers'),
            '4' => array('4', 'Netwerk interesse', 'business'),
            '5' => array('5', 'Documenten', 'ddirectory'),
            '6' => array('6', 'Sponsoren', 'sponsors'),
            '7' => array('7', 'Exposanten', 'exhibitors'),
            '8' => array('8', 'Advertentie-banners', 'banner'),
            '9' => array('9', 'Polls & enquêtes', 'polls'),
            '10' => array('10', 'Vraag & Antwoord', 'qa'),
            '11' => array('11', 'Nieuws & updates', 'alerts'),
            '12' => array('12', 'Praktisch informatie', 'infobooth'),
            '13' => array('13', 'Sociale media', 'social'),
            '14' => array('14', 'Kaart', 'maps'),
            '15' => array('15', 'Wedstrijd', 'competition'),
            '16' => array('16', 'Delen', 'share'),
            '17' => array('17', 'Fotogallerij', 'gallery'),
            '18' => array('18', 'Badge', 'badge'),
            '19' => array('19', 'Plattegrond', 'plans'),
            '20' => array('20', 'Inchecken', 'checkIn'),
            '21' => array('21', 'Mijn documenten', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Notities', 'notes'),
            '24' => array('24', 'Evenementenwebsite', 'eventsite'),
            '25' => array('25', 'Betaling evenementenwebsite', 'eventsite_payment'),
            '26' => array('26', 'Mobiele applicatie', 'mobile_app'),
            '27' => array('27', 'Details evenement', 'event_detail'),
            '28' => array('28', 'Aanpassen', 'customize'),
            '29' => array('29', 'Subregistraties', 'subregistration'),
            '30' => array('30', 'Sjablonen', 'templates'),
            '31' => array('31', 'Analyses', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'sms-geschiedenis', 'sms_history'),
            '34' => array('34', 'Verzoek om te spreken', 'myturnlist'),
            '35' => array('35', 'Profiel bewerken', 'editprofile'),
            '36' => array('36', 'E-mail mijn notities', 'emailmynotes'),
            '37' => array('37', 'Mijn sponsoren', 'mysponsers'),
            '38' => array('38', 'Mijn exposanten', 'myexhibitors'),
            '39' => array('39', 'Mijn evenementen', 'myevents'),
            '40' => array('40', 'Mijn zoekwoorden', 'mykeywords'),
            '41' => array('41', 'Beheer documenten', 'managedocuments'),
            '42' => array('42', 'Mijn vragen', 'myquestions'),
            '43' => array('43', 'Live polls', 'livepolls'),
            '44' => array('44', 'Mijn enquêtes', 'livesurveys'),
            '45' => array('45', 'Instellingen', 'settings'),
            '46' => array('46', 'Subregistratie(s)', 'subregistration'),
            '47' => array('47', 'Uitloggen', 'logout'),
            '48' => array('48', 'Mijn programma', 'myagendas'),
            '49' => array('49', 'Mijn deelnemers', 'myattendees'),
            '50' => array('50', 'Startpagina mijn evenementen', 'homeMyevents'),
            '51' => array('51', 'Mijn reserveringen', 'my-reservations'),
            '52' => array('52', 'Mijn poll resultaten', 'myPollResults'),
            '53' => array('53', 'Mijn enquête resultaten', 'mySurveyResults'),
            '54' => array('54', 'Nieuwsoverzicht', 'social_wall'),
            '55' => array('55', 'Bekijk profiel', 'view_profile'),
            '56' => array('56', 'Mijn notities', 'my_notes'),
            '58' => array('58', 'Native applicatie', 'nativeapp'),
            '59' => array('59', 'Inchecken sessie', 'checkin_agendas'),
            '60' => array('60', 'Aanvullende informatie', 'additional_info'),
            '61' => array('61', 'Algemene informatie', 'general_info'),
            '62' => array('62', 'Kaartjes', 'tickets'),
            '63' => array('63', 'API Sleutel', 'api_key'),
            '64' => array('64', 'Mailinglijst', 'mailing_list'),
            '65' => array('65', 'Email reclame', 'email_marketing'),
            '66' => array('66', 'Leidt management', 'leadsmanagment'),
            '67' => array('67', 'Uppkommande händelser', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Helpdesk', 'help_desk'),
            '70' => array('70', 'Helpdesk vragen', 'hdquestions'),
            '71' => array('71', 'Nieuws', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        ),
        '9' => array(
            '1' => array('1', 'Programma', 'agendas'),
            '2' => array('2', 'Deelnemers', 'attendees'),
            '3' => array('3', 'Sprekers', 'speakers'),
            '4' => array('4', 'Netwerk interesse', 'business'),
            '5' => array('5', 'Documenten', 'ddirectory'),
            '6' => array('6', 'Sponsors', 'sponsors'),
            '7' => array('7', 'Exposanten', 'exhibitors'),
            '8' => array('8', 'Advertentie-banners', 'banner'),
            '9' => array('9', 'Polls & enquêtes', 'polls'),
            '10' => array('10', 'Vraag & Antwoord', 'qa'),
            '11' => array('11', 'Nieuws & updates', 'alerts'),
            '12' => array('12', 'Praktisch informatie', 'infobooth'),
            '13' => array('13', 'Sociale media', 'social'),
            '14' => array('14', 'Kaart', 'maps'),
            '15' => array('15', 'Wedstrijd', 'competition'),
            '16' => array('16', 'Delen', 'share'),
            '17' => array('17', 'Fotogallerij', 'gallery'),
            '18' => array('18', 'Badge', 'badge'),
            '19' => array('19', 'Plattegrond', 'plans'),
            '20' => array('20', 'Inchecken', 'checkIn'),
            '21' => array('21', 'Mijn documenten', 'mydocuments'),
            '22' => array('22', 'Chat', 'chat'),
            '23' => array('23', 'Notities', 'notes'),
            '24' => array('24', 'Evenement website', 'eventsite'),
            '25' => array('25', 'Betaling evenement website', 'eventsite_payment'),
            '26' => array('26', 'Mobiele applicatie', 'mobile_app'),
            '27' => array('27', 'Details evenement', 'event_detail'),
            '28' => array('28', 'Aanpassen', 'customize'),
            '29' => array('29', 'Subregistraties', 'subregistration'),
            '30' => array('30', 'Sjablonen', 'templates'),
            '31' => array('31', 'Analyses', 'analytics'),
            '32' => array('32', 'SMS', 'sms'),
            '33' => array('33', 'sms-geschiedenis', 'sms_history'),
            '34' => array('34', 'Verzoek om te spreken', 'myturnlist'),
            '35' => array('35', 'Profiel bewerken', 'editprofile'),
            '36' => array('36', 'E-mail mijn notities', 'emailmynotes'),
            '37' => array('37', 'Mijn sponsors', 'mysponsers'),
            '38' => array('38', 'Mijn exposanten', 'myexhibitors'),
            '39' => array('39', 'Mijn evenementen', 'myevents'),
            '40' => array('40', 'Mijn zoekwoorden', 'mykeywords'),
            '41' => array('41', 'Beheer documenten', 'managedocuments'),
            '42' => array('42', 'Mijn vragen', 'myquestions'),
            '43' => array('43', 'Live polls', 'livepolls'),
            '44' => array('44', 'Mijn enquêtes', 'livesurveys'),
            '45' => array('45', 'Instellingen', 'settings'),
            '46' => array('46', 'Subregistratie(s)', 'subregistration'),
            '47' => array('47', 'Uitloggen', 'logout'),
            '48' => array('48', 'Mijn programma', 'myagendas'),
            '49' => array('49', 'Mijn deelnemers', 'myattendees'),
            '50' => array('50', 'Startpagina mijn evenementen', 'homeMyevents'),
            '51' => array('51', 'Mijn reserveringen', 'my-reservations'),
            '52' => array('52', 'Mijn poll resultaten', 'myPollResults'),
            '53' => array('53', 'Mijn enquête resultaten', 'mySurveyResults'),
            '54' => array('54', 'Nieuwsoverzicht', 'social_wall'),
            '55' => array('55', 'Bekijk profiel', 'view_profile'),
            '56' => array('56', 'Mijn notities', 'my_notes'),
            '58' => array('58', 'Native applicatie', 'nativeapp'),
            '59' => array('59', 'Inchecken sessie', 'checkin_agendas'),
            '60' => array('60', 'Aanvullende informatie', 'additional_info'),
            '61' => array('61', 'Algemene informatie', 'general_info'),
            '62' => array('62', 'Kaartjes', 'tickets'),
            '63' => array('63', 'API Sleutel', 'api_key'),
            '64' => array('64', 'Mailinglijst', 'mailing_list'),
            '65' => array('65', 'Email reclame', 'email_marketing'),
            '66' => array('66', 'Leidt management', 'leadsmanagment'),
            '67' => array('67', 'Uppkommande händelser', 'upcomingEvents'),
            '68' => array('68', 'Streams', 'programVideos'),
            '69' => array('69', 'Helpdesk', 'help_desk'),
            '70' => array('70', 'Helpdesk vragen', 'hdquestions'),
            '71' => array('71', 'Nieuws', 'news'),
            '72' => array('72', 'Information Pages', 'information_pages'),
        )
    );

    return (isset($modules[$language_id][$id]) ? $modules[$language_id][$id] : null);
}

function get_module_images($module_alias = '')
{
    $modules = array();
    $modules['agendas'] = "agendas.png";
    $modules['myagendas'] = "agendas.png";
    $modules['attendees'] = "attendees.png";
    $modules['myattendees'] = "attendees.png";
    $modules['speakers'] = "speakers.png";
    $modules['sponsors'] = "sponsors.png";
    $modules['exhibitors'] = "exhibitors.png";
    $modules['banner'] = "gallery.png";
    $modules['gallery'] = "gallery.png";
    $modules['checkIn'] = "checkin.png";
    $modules['myturnlist'] = "alerts.png";
    $modules['polls'] = "polls.png";
    $modules['qa'] = "qa.png";
    $modules['subregistration'] = "qa.png";
    $modules['ddirectory'] = "ddirectory.png";
    $modules['alerts'] = "alerts.png";
    $modules['badge'] = "qrcode.png";
    $modules['mydocuments'] = "document.png";
    $modules['chat'] = "message.png";
    $modules['business'] = "business.png";
    $modules['infobooth'] = "infobooth.png";
    $modules['social'] = "social.png";
    $modules['maps'] = "maps.png";
    $modules['competition'] = "competition.png";
    $modules['share'] = "share.png";
    $modules['plans'] = "plans.png";
    $modules['homeMyevents'] = "homeMyevents.png";
    $modules['livepolls'] = "polls.png";
    $modules['managedocuments'] = "document.png";
    $modules['myquestions'] = "qa.png";
    $modules['myevents'] = "myevents.png";
    $modules['editprofile'] = "edit-account-icon.png";
    $modules['emailmynotes'] = "email_icon.png";
    $modules['mysponsers'] = "fav-sponsors.png";
    $modules['myexhibitors'] = "exhibitors.png";
    $modules['mykeywords'] = "match-icon.png";
    $modules['livesurveys'] = "polls.png";
    $modules['settings'] = "setting-icon.png";
    $modules['my-reservations'] = "plans.png";
    $modules['notes'] = "notes.png";
    $modules['social_wall'] = "social_wall.png";
    $modules['view_profile'] = "edit-account-icon.png";
    $modules['my_notes'] = "my_notes.png";
    $modules['nativeapp'] = "nativeapp.png";
    $modules['additional_info'] = "additional_info.png";
    $modules['general_info'] = "general_info.png";
    return (isset($modules[$module_alias]) ? $modules[$module_alias] : '');
}

function set_event_timezone($event_id)
{
    $event = \App\Models\Event::join('conf_timezones', 'conf_timezones.id', '=', 'conf_events.timezone_id')
        ->where('conf_events.id', $event_id)->first();
    date_default_timezone_set($event->timezone);
}

function get_event_branding($event_id)
{
    $event_settings = \App\Models\EventSetting::where('event_id', $event_id)->where(
        function ($query) {
            $query->where('name', 'app_icon')
                ->orWhere('name', 'header_logo')
                ->orWhere('name', 'landing_image')
                ->orWhere('name', 'fav_icon')
                ->orWhere('name', 'primary_color')
                ->orWhere('name', 'secondary_color');
        }
    )->get();

    foreach ($event_settings as $row) {
        if ($row['value'] == 'NULL')
            $container[$row['name']] = '';
        else
            $container[$row['name']] = $row->value;
    }

    $branding = array();

    if (isset($container['app_icon']) && $container['app_icon']) {
        $branding['app_icon'] = $container['app_icon'];
    } else {
        $branding['app_icon'] = '';
    }

    if (isset($container['header_logo']) && $container['header_logo']) {
        $branding['header_logo'] = $container['header_logo'];
    } else {
        $branding['header_logo'] = '';
    }

    if (isset($container['landing_image']) && $container['landing_image']) {
        $branding['landing_image'] = $container['landing_image'];
    } else {
        $branding['landing_image'] = '';
    }

    if (isset($container['fav_icon']) && $container['fav_icon']) {
        $branding['fav_icon'] = $container['fav_icon'];
    } else {
        $branding['fav_icon'] = '';
    }

    if (isset($container['primary_color']) && $container['primary_color']) {
        $branding['primary_color'] = $container['primary_color'];
    } else {
        $branding['primary_color'] = '#f28121';
    }

    if (isset($container['secondary_color']) && $container['secondary_color']) {
        $branding['secondary_color'] = $container['secondary_color'];
    } else {
        $branding['secondary_color'] = '#69c7cf';
    }

    return $branding;
}

function email_background_color($event_id)
{
    $event_setting = get_event_branding($event_id);
    $color['css'] = <<<EOD
<style id="background_color" type="text/css">
.primary_background_color{
	background-color:{$event_setting['primary_color']};
}
.secondary_background_color{
	background-color:{$event_setting['secondary_color']};
}
.primary_font_color{
	color:{$event_setting['primary_color']};
}
.secondary_font_color{
	color:{$event_setting['secondary_color']};
}
a{
color:{$event_setting['primary_color']} ;
text-decoration: none;
}
</style>
EOD;
    return $color;
}

function setEventBrandingCss($event_id)
{
    $event_setting = get_event_branding($event_id);
    $color['css'] = <<<EOD
.primary_background_color{
	background-color:{$event_setting['primary_color']};
}
.secondary_background_color{
	background-color:{$event_setting['secondary_color']};
}
.primary_font_color{
	color:{$event_setting['primary_color']};
}
.secondary_font_color{
	color:{$event_setting['secondary_color']};
}
a{
color:{$event_setting['primary_color']} ;
text-decoration: none;
}
EOD;
    return $color;
}

function eventsite_setting($event_id)
{
    return \App\Models\EventsiteSetting::where('event_id', $event_id)->where('registration_form_id', 0)->first();
}

function getEmailTemplate($template, $event_id)
{
    try {
        if (!$template) {
            $template = '<html><div></div></html>';
        }

        $emailBackgroun = setEventBrandingCss($event_id);
        
        $template = CssInliner::fromHtml($template)->inlineCss($emailBackgroun['css'])->render();

        return $template;
    } catch (\Throwable $e) {
        return $template;
    }
}

function getCountryName($p_country_id)
{
    $country = \App\Models\Country::where('parent_id', '=', $p_country_id)->where('languages_id', '=', 1)->orderBy('name')->first();
    return stripslashes($country->name);
}

function getBillingItemName($values, $language_id)
{
    $link_to_id = \App\Models\BillingItem::where('id', $values['addon_id'])->value('link_to_id');

    if ($values['link_to'] == 'track') {
        $track = \App\Models\EventTrack::where('id', $link_to_id)->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', $language_id)->where('name', 'name');
        }])->first();
        $name = $track->info[0]['value'];
    }
    if ($values['link_to'] == 'workshop') {
        $workshop = \App\Models\EventWorkshop::where('id', $link_to_id)->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', $language_id)->where('name', 'name');
        }])->first();
        $name = $workshop->info[0]['value'];
    }
    if ($values['link_to'] == 'program') {
        $program = \App\Models\EventAgenda::where('id', $link_to_id)->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', $language_id)->where('name', 'topic');
        }])->first();
        $name = $program->info[0]['value'];
    }
    return $name;
}

/**
 * @param mixed $event_id
 * 
 * @return [type]
 */
function getEventAttendeeSetting($event_id)
{
    return  \App\Models\AttendeeSetting::where('event_id', $event_id)->first();
}

function getTemplate($type, $alias, $event_id, $language_id)
{
    $template = \App\Models\EventEmailTemplate::where('event_id', $event_id)
        ->where('type', $type)
        ->where('alias', $alias)
        ->with(['info' => function ($query) use ($language_id) {
            if ($language_id) {
                return $query->where('languages_id', $language_id)
                    ->where('name', '<>', 'title');
            } else {
                return $query->where('name', '<>', 'title');
            }
        }])->first();

    return $template;
}

function getLanguageId(){
    $id = Session::get('admin_language_id');
    if(trim($id) == '') {
        return '';
    }
    return Crypt::decrypt($id);
}

function getEventId(){
    $id = Session::get('admin_event_id');
    if($id) {
        return Crypt::decrypt($id);
    }
}

/**
 * @param mixed $event_id
 * @param mixed $language_id
 * @param mixed $date_location
 * @param mixed $date
 * 
 * @return [type]
 */
function getEventDateFormat($event_id, $language_id, $date_location, $date)
{
    $format = \App\Models\EventDateFormat::where('event_id', $event_id)->where('language_id', $language_id)->first();
    if ($format->date_format_id == 2) {
        setlocale(LC_TIME, "da_DK.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d %B %Y';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d. %b. %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' d. %d. %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d. %B. %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A %d. %B. %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A d. %d. %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %B %d, %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = 'H:i:s';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%d.%m.%Y - %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%d.%m.%Y - %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%d.%m.%Y - %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d. %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%d. %b. %Y';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 3) {
        setlocale(LC_TIME, "nb_NO.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d. %B %Y';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d. %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%d. %m %Y';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A %d. %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A %d. %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %B %d, %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = 'H:i:s';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d. %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%d. %b %Y';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 4) {
        setlocale(LC_TIME, "de_DE.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d. %B %Y';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d. %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%d. %m %Y';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A %d. %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A %d. %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %B %d, %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = 'H:i:s';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%d/%m/%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%d/%m/%Y';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d. %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%d. %b %Y';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 5) {
        setlocale(LC_TIME, "lt_LT.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%Y %B %d d.';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%Y %B %d d.';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %Y %B %d d.';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%Y %b %d d.';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%Y %B %d';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%Y %B %d';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %Y %B %d d.';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %Y %B %d d.';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %Y %B %d d.';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%Y %B %d d.';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%Y %B %d d.';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%Y-%m-%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%Y-%m-%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%Y-%m-%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%Y-%m-%d';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%Y-%m-%d';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 6) {
        setlocale(LC_TIME, "fi_FI.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = ' %d. %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d. %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d. %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = ' %d. %B %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%d.%m.%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%d.%m.%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%d.%m.%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%d.%m.%Y';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%d.%m.%Y';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 7) {
        setlocale(LC_TIME, "sv_SE.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d %B - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%Y/%m/%d';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 8) {
        setlocale(LC_TIME, "nl_NL.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d %B - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%Y/%m/%d';
        }
        $result = strftime($data_formats, strtotime($date));
    } elseif ($format->date_format_id == 9) {
        setlocale(LC_TIME, "nl_BE.utf8");
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d %B - %d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d %B %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%Y/%m/%d %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%Y/%m/%d';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e. %B %G';
        } else {
            $data_formats = '%Y/%m/%d';
        }
        $result = strftime($data_formats, strtotime($date));
    } else {
        if ($date_location == 'registration_site_header_single_date') {
            $data_formats = '%d. %B %Y';
        } elseif ($date_location == 'registration_site_header_double_date') {
            $data_formats = '%d. - %d. %B %Y';
        } elseif ($date_location == 'registration_site_header_multi_mounth_date') {
            $data_formats = '%d. %B - %d %B %Y';
        } elseif ($date_location == 'registration_end_date') {
            $data_formats = '%d %b %Y';
        } elseif ($date_location == 'mobile_site_program_listing_day_date') {
            $data_formats = '%A,';
        } elseif ($date_location == 'mobile_site_program_listing_time_date') {
            $data_formats = ' %d %B, %Y';
        } elseif ($date_location == 'registration_site_register_attendee_date') {
            $data_formats = '%d. %b %Y';
        } elseif ($date_location == 'registration_site_checkin_hotel_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'registration_site_order_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'registration_site_billing_registration_step1_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_program_listing_date') {
            $data_formats = '%A, %B %d %Y';
        } elseif ($date_location == 'mobile_site_speaker_detail_program_date') {
            $data_formats = '%A, %B %d, %Y';
        } elseif ($date_location == 'mobile_site_alert_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_chat_date') {
            $data_formats = '%H:%M:%S';
        } elseif ($date_location == 'mobile_site_polls_listing_date') {
            $data_formats = '%A, %B %d, %Y';
        } elseif ($date_location == 'mobile_site_reservation_listing_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_reservation_detail_date') {
            $data_formats = '%d %B %Y';
        } elseif ($date_location == 'mobile_site_slot_listing_date') {
            $data_formats = 'H:i:s';
        } elseif ($date_location == 'mobile_site_checkin_listing_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_checkin_listing_date_time') {
            $data_formats = '%d-%m-%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_checkin_popup_date_time') {
            $data_formats = '%d-%m-%Y %H:%M:%S';
        } elseif ($date_location == 'mobile_site_directory_listing_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_share_doc_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_popup_date_time') {
            $data_formats = '%d-%m-%Y  %H:%M:%S';
        } elseif ($date_location == 'mobile_site_edit_profile_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_my_question_date') {
            $data_formats = '%d-%m-%Y';
        } elseif ($date_location == 'mobile_site_social_wall_coments_date') {
            $data_formats = '%d %B';
        } elseif ($date_location == 'reg_speaker_detail_date') {
            $data_formats = '%A %d %B %Y';
        } elseif ($date_location == 'projector_speaker_list') {
            $data_formats = '%A, %d. %B %Y';
        } elseif ($date_location == 'program_search') {
            $data_formats = '%e, %B %G';
        } else {
            $data_formats = '%d-%m-%Y';
        }
        $result = strftime($data_formats, strtotime($date));
    }
    return $result;
}

/**
 * @param mixed $id
 * @param mixed $language_id
 * 
 * @return [type]
 */
function getCustomFieldValue($id, $language_id) {
    $field = \App\Models\EventCustomFieldInfo::where('custom_field_id', $id)->where('languages_id', $language_id)->first();
    return $field->value;
}

function getCustomFields($event_id, $language_id)
{
        $customFields = \App\Models\EventCustomField::where('event_id', '=', $event_id)
            ->with(['info' => function ($query) use ($language_id) {

                return $query->where('languages_id', '=', $language_id);
            }, 'childrenRecursive' => function ($r) {

                return $r->orderBy('sort_order');
            }, 'childrenRecursive.info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->where('parent_id', '=', '0')->orderBy('sort_order', 'ASC')->orderBy('sort_order', 'ASC')->get()->toArray();

        return $customFields;
}

function convertEventTimezoneToUtc($timezone_id, $time, $format = "YmdTG:i:sz")
{
    $timezone = \App\Models\Timezone::where('id', $timezone_id)->first();
    $date = new \DateTime($time, new \DateTimeZone($timezone->timezone));
    $date->setTimezone(new \DateTimeZone("UTC"));
    $time = $date->format($format);
    return $time;
}

function getReferenceNumber($event_id, $invoice_Id, $debitorNumber, $type)
{
    $sum = $invoice_Id;
    $sumcount = strlen($invoice_Id);
    $start_numb = strlen($event_id);
    $fourteendigit = "00000000000000";
    $fourteendigit_zero = substr($fourteendigit, 0, -$start_numb);
    $fourteendigit = $event_id . $fourteendigit_zero;
    $fourteendigit = substr($fourteendigit, 0, -$sumcount);
    $fourteendigit = $fourteendigit . $sum;
    $nordeaNumber = $fourteendigit;
    $fourteendigit = str_split($fourteendigit);
    $i = 1;
    $taeller = $number = '';
    foreach ($fourteendigit as $digit) {
        if ($i % 2 == 0) {
            $number = $digit * 2;
        } else {
            $number = $digit * 1;
        }
        if ($number >= 10) {
            $number = array_sum(str_split($number));
        }
        $taeller .= $number;
        $i++;
    }
    $taeller = array_sum(str_split($taeller));
    $modulus = $taeller % 10;
    if ($modulus == 0)
        $controlnumber = 0;
    else
        $controlnumber = 10 - $modulus;
    $nordeaNumber = $nordeaNumber . $controlnumber;
    $referenceNumber = "+71&lt; " . $nordeaNumber . "+" . $debitorNumber . "&lt;";
    if ($type == 1) {
        $nordeaNumber = str_repeat('&nbsp;', 15); // adds 15 spaces
        $referenceNumber = "+73&lt; " . $nordeaNumber . "+" . $debitorNumber . "&lt;";
    }
    if ($type == 2) {
        $referenceNumber = "";
    }
    return $referenceNumber;
}

function moduleNameFront($event_id, $language_id, $alias)
{
    $module = \App\Models\EventModuleOrder::where('event_id',$event_id)->where('alias', '=', $alias)->with(['info' => function($q) use($language_id) {
        return $q->where('languages_id', $language_id);
    }])->get()->toArray();
    return $module[0]['info'][0]['value'];
}

function sortBySortOrder($a, $b)
{
    if ($a['sort_order'] == $b['sort_order']) {
        return 0;
    }
    return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
}

function returnInfoArray($array) {
    $final_array = array();
    if(count($array) > 0) {
        foreach($array as $record) {

            $temp_array = array();
            foreach($record as $key => $rec) {
                if($key == 'info') {
                    foreach($record['info'] as $info) {
                        $temp_array[$info['name']] = $info['value'];
                    }
                }
                else if($key == 'settings' && is_array($rec)) {
                    foreach($record['settings'] as $setting) {
                        $temp_array[$setting['name']] = $setting['value'];
                    }
                }
                else {
                    $temp_array[$key] = $rec;
                }

            }
            $final_array[] = $temp_array;
        }
    }
    else {
        return $array;
    }
    return $final_array;
}

function returnInfoRecursiveChild($array, $child_feild){
    $final_array = array();
    if(count($array) > 0) {
        foreach($array as $record) {

            $temp_array = array();
            foreach($record as $key => $rec) {
                if($key == 'info') {
                    foreach($record['info'] as $info) {
                        $temp_array[$info['name']] = $info['value'];
                    }
                }
                elseif($key == $child_feild){
                    $temp_array[$key] = returnInfoRecursiveChild($rec, $child_feild);
                }
                else {
                    $temp_array[$key] = $rec;
                }
            }
            $final_array[] = $temp_array;
        }
    }
    else {
        return $array;
    }
    return $final_array;
}

function getEventFormatDate($format, $date)
{
    $result = trim(strftime($format, strtotime($date)));

    return $result;
}

function getLabelByAlias($event_id, $language_id, $alias)
{
    $event_label = \App\Models\EventSiteText::where('alias', '=', $alias)->where('event_id', '=', $event_id)->with(['info' => function ($query) use ($language_id) {
        return $query->where('languages_id', '=', $language_id);
    }])->get()->toArray();
    $event_label = $event_label[0];
    return $event_label['info'][0]['value'];

}

function getIdsArrayfromPivot($data, $column){
    if(!is_array($data)){
        $data = $data->toArray();
    }
    $arr =[];
    foreach ($data as $key => $value) {
        $arr[] = $value[$column];
    }
    return $arr;
}

function isBookingActive($settingModel, $event_id)
{
    $settings = $settingModel::where('event_id', '=', $event_id)->first();

    if ($settings) {
        return $settings->reservation == 1 ? true : false;
    }
    return false;
}

function isBookingSettingsON($settingModel, $event_id, $name)
{
    $settings = $settingModel::where('event_id', '=', $event_id)->first();

    if ($settings) {
        return $settings->$name == 1 ? true : false;
    }
    return false;
}

function event_data($event_id, $language_id){
    $event = \App\Models\Event::where('id', $event_id)->with(["info" => function($q) use($language_id) { return $q->where('language_id', $language_id);}, 'settings'])->first();
    $event['settings'] = readArrayKey($event, [], 'settings');
    $event['info'] = readArrayKey($event, [], 'info');
    return $event;
}

function showPollToAttendee($agenda_id, $poll_id, $event_id, $attendee_id, $module_status, $my_program_module_status){

        $already_send = \App\Models\EventAttendeePollAuthorityLog::where('event_id',$event_id)->where('attendee_from',$attendee_id)->where('is_accepted',1)->first();
        if($already_send){
            return false;
        }

        $poll_groups = \App\Models\EventPollGroup::where('poll_id',$poll_id)->pluck('group_id')->toArray();
        $attendee_groups = \App\Models\EventAttendeeGroup::where('attendee_id',$attendee_id)->whereIn('group_id',$poll_groups)->count();
        $poll_groups = count($poll_groups);

        $show_poll = false;
            if ($module_status) {
                $show_poll = ($poll_groups <= 0 || ($poll_groups > 0 && $attendee_groups > 0)) ? true : false; 
            } elseif ($my_program_module_status) {
                $program_attach_attendees = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)->where('agenda_id', $agenda_id)->first();
                $program_settings = \App\Models\AgendaSetting::where('event_id', $event_id)->first();
                $program_groups_setting = $program_settings->enable_program_attendee;

                if ($program_attach_attendees) {
                    $show_poll = ($poll_groups <= 0 || ($poll_groups > 0 && $attendee_groups > 0)) ? true : false; 
                } elseif ($program_groups_setting) {
                    $program_groups = \App\Models\EventAgendaGroup::where('agenda_id', $agenda_id)->lists('group_id');
                    $attendee_program_groups = \App\Models\AgendaSetting::where('attendee_id', $attendee_id)->whereIn('group_id', $program_groups)->count();
                    if ($attendee_program_groups == 0) {
                        $show_poll = false;
                    } else {
                        $show_poll = ($poll_groups <= 0 || ($poll_groups > 0 && $attendee_groups > 0)) ? true : false; 
                    }
                } else {
                    $show_poll = false;
                }
            } else {
                $show_poll = false;
            }
        
    return $show_poll; 
}

function getEventSettings($event_id)
{
    $eventSettings = \App\Models\EventSetting::select(['name', 'value'])->where("event_id", $event_id)->pluck('value', 'name')->toArray();
    if(is_null($eventSettings)){
        return [];
    }
    array_walk_recursive($eventSettings, function (&$item, $key) {
        $item = $item === null ? '' : $item;
    });
    return $eventSettings;
}

function getGdprSettings($event_id)
{
    $gdpr_settings =  \App\Models\EventGdpr::where('event_id', '=', $event_id)->first();
    return $gdpr_settings ? $gdpr_settings->toArray() : [];

}

function getAttendeeSettings($event_id){
    $attendee_setting = \App\Models\AttendeeSetting::where('event_id', '=', $event_id)->first();
    return $attendee_setting ? $attendee_setting->toArray() : [];
}

function getEventStreamingChannel($channelName)
{
    return \App\Models\EventListSignallingChannel::where('ChannelName', '=', $channelName)->first();
}

function getEventStreamingChannelChat($channelName, $count = false)
{
    $chat =  \App\Models\EventStreamingChannelChat::where('ChannelName', '=', $channelName);
    if($count) {
        return $chat->count();
    } else {
        return $chat->get();
    }
}

function getSpeakerlistAttendeeFields($event_id){
    $fields = \App\Models\SpeakerListProjectorAttendeeField::where('event_id',$event_id)->where('is_show_on_projector',0)->orderBy('sort_order')->lists('fields_name');
    return array_flip($fields);
}

function getEventModuleDetail($event_id, $module_alias)
{
    $modules = \App\Models\EventModuleOrder::where('event_id', '=', $event_id)->where('alias', '=', $module_alias)->first();
    $modules = $modules ? $modules->toArray() : [];
}

function getEmailTemplateInfo($event_id, $language_id, $type, $alias)
{
    $template = \App\Models\EventEmailTemplate::where('event_id', '=', $event_id)
        ->where('type', '=', $type)
        ->where('alias', '=', $alias)
        ->with(['info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->first();
    
    $template = $template ? $template->toArray() : [];

    if(!empty($template)){
        $template['info'] = readArrayKey($template, [], 'info');
    }
        
    return $template;
}

function getDateTimeEventTimeZone($value,$id,$model=''){
    $timezone='UTC';
    if($id>0){
        if($model!=''){
            $model_data=$model::whereId($id)->first();
            if($model_data){
                $event=\App\Models\Event::select('timezone_id')->whereId($model_data->event_id)->first();
            }else{
                $timezone= 'UTC';
            }
        }else{
            $event=\App\Models\Event::select('timezone_id')->whereId($id)->first();
        }
        if($event){
            $timezone=\App\Models\Timezone::find($event->timezone_id);
            $timezone=$timezone->timezone;
        }

    }
    return Carbon::parse($value,'UTC')->setTimezone($timezone)->toDateTimeString();

}

function getItemSoldTickets($item_id)
{
    /*
     * checking billing items link with programs
    */
    $item_detail = \App\Models\BillingItem::find($item_id);
    if($item_detail && $item_detail->link_to == 'program') {
        $program = \App\Models\Agenda::find($item_detail->link_to_id);
        $item_ids = \App\Models\BillingItem::where('event_id', $item_detail->event_id)->where('link_to_id', $item_detail->link_to_id)->select('id')->pluck('id');
        $addons_order_id = \App\Models\BillingOrderAddon::whereIn('addon_id', $item_ids)->groupBy('order_id')->select('order_id')->pluck('order_id');
        $ids = \App\Models\BillingOrder::whereIn('id',$addons_order_id)->currentOrder()->select('id')->pluck('id');
        $tickets_used = \DB::table('conf_billing_order_addons')->join('conf_billing_orders','conf_billing_orders.id','=','conf_billing_order_addons.order_id')
            ->whereIn('conf_billing_order_addons.addon_id',$item_ids)->where('conf_billing_orders.is_archive','=','0')->whereNull('conf_billing_order_addons.deleted_at')
            ->whereIn('conf_billing_orders.id',$ids)->where('conf_billing_orders.status','=','completed')->where('conf_billing_orders.is_waitinglist','=','0')->sum('conf_billing_order_addons.qty');
        return $tickets_used;
    }

    $temp = \App\Models\BillingOrderAddon::where('addon_id','=',$item_id)->groupBy('order_id')->select('order_id')->pluck('order_id');
    $ids = \App\Models\BillingOrder::whereIn('id',$temp)->currentOrder()->select('id')->pluck('id');

    $tickets_used = \DB::table('conf_billing_order_addons')->join('conf_billing_orders','conf_billing_orders.id','=','conf_billing_order_addons.order_id')->where('conf_billing_order_addons.addon_id','=',$item_id)->where('conf_billing_orders.is_archive','=','0')->whereNull('conf_billing_order_addons.deleted_at')->whereIn('conf_billing_orders.id',$ids)->where('conf_billing_orders.status','=','completed')->where('conf_billing_orders.is_waitinglist','=','0')->sum('conf_billing_order_addons.qty');
    return $tickets_used;
}