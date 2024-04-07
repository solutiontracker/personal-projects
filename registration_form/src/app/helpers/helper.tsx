import moment from 'moment-timezone';
import UAParser from 'ua-parser-js'
import ReactPixel from 'react-facebook-pixel';
import LinkedInTag from 'react-linkedin-insight';
import ReactGA from "react-ga4";

const parser = new UAParser()

const userAgentInfo = parser.getResult()

const formatString = (fmt: string, ...args: any[]): any => {
    if (!fmt.match(/^(?:(?:(?:[^{}]|(?:\{\{)|(?:\}\}))+)|(?:\{[0-9]+\}))+$/)) {
        throw new Error('invalid format string.');
    }
    return fmt.replace(/((?:[^{}]|(?:\{\{)|(?:\}\}))+)|(?:\{([0-9]+)\})/g, (m, str, index) => {
        if (str) {
            return str.replace(/(?:{{)|(?:}})/g, (m: any) => m[0]);
        } else {
            if (index >= args.length) {
                throw new Error('argument index is out of range in format');
            }
            return args[index];
        }
    });
}

const ltrim = (str: string, chr: string): any => {
    const rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
    return str.replace(rgxtrim, '');
}

const isSafari = (): any => {
    return (
        userAgentInfo.browser.name === 'Safari' ||
        userAgentInfo.browser.name === 'Mobile Safari'
    )
}

const isCompatibleChrome = (): any => {
    if (userAgentInfo.browser.name === 'Chrome') {
        const major = +userAgentInfo.browser.major
        if (major >= 72) return true
    }
    return false
}

const isFirefox = (): any => {
    return userAgentInfo.browser.name === 'Firefox'
}

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
const number_format = (number: any, decimals: any, dec_point: any, thousands_point: any): any => {
    if (number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }

    if (!decimals) {
        const len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }

    if (!dec_point) {
        dec_point = '.';
    }

    if (!thousands_point) {
        thousands_point = ',';
    }

    number = parseFloat(number).toFixed(decimals);

    number = number.replace(".", dec_point);

    const splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);

    return number;
}

const getCurrency = (floatcurr: number, _curr = 'USD'): any => {

    const currencies: any = {
        'ARS': [2, ',', '.'],         //  Argentine Peso
        'AMD': [2, '.', ','],         //  Armenian Dram
        'AWG': [2, '.', ','],         //  Aruban Guilder
        'AUD': [2, '.', ' '],         //  Australian Dollar
        'BSD': [2, '.', ','],         //  Bahamian Dollar
        'BHD': [3, '.', ','],         //  Bahraini Dinar
        'BDT': [2, '.', ','],         //  Bangladesh, Taka
        'BZD': [2, '.', ','],         //  Belize Dollar
        'BMD': [2, '.', ','],          //  Bermudian Dollar
        'BOB': [2, '.', ','],          //  Bolivia, Boliviano
        'BAM': [2, '.', ','],          //  Bosnia and Herzegovina, Convertible Marks
        'BWP': [2, '.', ','],          //  Botswana, Pula
        'BRL': [2, ',', '.'],          //  Brazilian Real
        'BND': [2, '.', ','],          //  Brunei Dollar
        'CAD': [2, '.', ','],          //  Canadian Dollar
        'KYD': [2, '.', ','],          //  Cayman Islands Dollar
        'CLP': [0, '', '.'],           //  Chilean Peso
        'CNY': [2, '.', ','],          //  China Yuan Renminbi
        'COP': [2, ',', '.'],          //  Colombian Peso
        'CRC': [2, ',', '.'],          //  Costa Rican Colon
        'HRK': [2, ',', '.'],          //  Croatian Kuna
        'CUC': [2, '.', ','],          //  Cuban Convertible Peso
        'CUP': [2, '.', ','],          //  Cuban Peso
        'CYP': [2, '.', ','],          //  Cyprus Pound
        'CZK': [2, '.', ','],          //  Czech Koruna
        'DKK': [2, ',', '.'],          //  Danish Krone
        'DOP': [2, '.', ','],          //  Dominican Peso
        'XCD': [2, '.', ','],          //  East Caribbean Dollar
        'EGP': [2, '.', ','],          //  Egyptian Pound
        'SVC': [2, '.', ','],          //  El Salvador Colon
        'ATS': [2, ',', '.'],          //  Euro
        'BEF': [2, ',', '.'],          //  Euro
        'DEM': [2, ',', '.'],          //  Euro
        'EEK': [2, ',', '.'],          //  Euro
        'ESP': [2, ',', '.'],          //  Euro
        'EUR': [2, ',', '.'],          //  Euro
        'FIM': [2, ',', '.'],          //  Euro
        'FRF': [2, ',', '.'],          //  Euro
        'GRD': [2, ',', '.'],          //  Euro
        'IEP': [2, ',', '.'],          //  Euro
        'ITL': [2, ',', '.'],          //  Euro
        'LUF': [2, ',', '.'],          //  Euro
        'NLG': [2, ',', '.'],          //  Euro
        'PTE': [2, ',', '.'],          //  Euro
        'GHC': [2, '.', ','],          //  Ghana, Cedi
        'GIP': [2, '.', ','],          //  Gibraltar Pound
        'GTQ': [2, '.', ','],          //  Guatemala, Quetzal
        'HNL': [2, '.', ','],          //  Honduras, Lempira
        'HKD': [2, '.', ','],          //  Hong Kong Dollar
        'HUF': [0, '', '.'],           //  Hungary, Forint
        'ISK': [0, '', '.'],           //  Iceland Krona
        'INR': [2, '.', ','],          //  Indian Rupee
        'IDR': [2, ',', '.'],          //  Indonesia, Rupiah
        'IRR': [2, '.', ','],          //  Iranian Rial
        'JMD': [2, '.', ','],          //  Jamaican Dollar
        'JPY': [0, '', ','],           //  Japan, Yen
        'JOD': [3, '.', ','],          //  Jordanian Dinar
        'KES': [2, '.', ','],          //  Kenyan Shilling
        'KWD': [3, '.', ','],          //  Kuwaiti Dinar
        'LVL': [2, '.', ','],          //  Latvian Lats
        'LBP': [0, '', ' '],           //  Lebanese Pound
        'LTL': [2, ',', ' '],          //  Lithuanian Litas
        'MKD': [2, '.', ','],          //  Macedonia, Denar
        'MYR': [2, '.', ','],          //  Malaysian Ringgit
        'MTL': [2, '.', ','],          //  Maltese Lira
        'MUR': [0, '', ','],           //  Mauritius Rupee
        'MXN': [2, '.', ','],          //  Mexican Peso
        'MZM': [2, ',', '.'],          //  Mozambique Metical
        'NPR': [2, '.', ','],          //  Nepalese Rupee
        'ANG': [2, '.', ','],          //  Netherlands Antillian Guilder
        'ILS': [2, '.', ','],          //  New Israeli Shekel
        'TRY': [2, '.', ','],          //  New Turkish Lira
        'NZD': [2, '.', ','],          //  New Zealand Dollar
        'NOK': [2, ',', '.'],          //  Norwegian Krone
        'PKR': [2, '.', ','],          //  Pakistan Rupee
        'PEN': [2, '.', ','],          //  Peru, Nuevo Sol
        'UYU': [2, ',', '.'],          //  Peso Uruguayo
        'PHP': [2, '.', ','],          //  Philippine Peso
        'PLN': [2, '.', ' '],          //  Poland, Zloty
        'GBP': [2, '.', ','],          //  Pound Sterling
        'OMR': [3, '.', ','],          //  Rial Omani
        'RON': [2, ',', '.'],          //  Romania, New Leu
        'ROL': [2, ',', '.'],          //  Romania, Old Leu
        'RUB': [2, ',', '.'],          //  Russian Ruble
        'SAR': [2, '.', ','],          //  Saudi Riyal
        'SGD': [2, '.', ','],          //  Singapore Dollar
        'SKK': [2, ',', ' '],          //  Slovak Koruna
        'SIT': [2, ',', '.'],          //  Slovenia, Tolar
        'ZAR': [2, '.', ' '],          //  South Africa, Rand
        'KRW': [0, '', ','],           //  South Korea, Won
        'SZL': [2, '.', ', '],         //  Swaziland, Lilangeni
        'SEK': [2, ',', '.'],          //  Swedish Krona
        'CHF': [2, '.', '\''],         //  Swiss Franc
        'TZS': [2, '.', ','],          //  Tanzanian Shilling
        'THB': [2, '.', ','],          //  Thailand, Baht
        'TOP': [2, '.', ','],          //  Tonga, Paanga
        'AED': [2, '.', ','],          //  UAE Dirham
        'UAH': [2, ',', ' '],          //  Ukraine, Hryvnia
        'USD': [2, '.', ','],          //  US Dollar
        'VUV': [0, '', ','],           //  Vanuatu, Vatu
        'VEF': [2, ',', '.'],          //  Venezuela Bolivares Fuertes
        'VEB': [2, ',', '.'],          //  Venezuela, Bolivar
        'VND': [0, '', '.'],           //  Viet Nam, Dong
        'ZWD': [2, '.', ' '],          //  Zimbabwe Dollar
    };

    return number_format(floatcurr, currencies[_curr][0], currencies[_curr][1], currencies[_curr][2])
}

function toggleValueInArray(arr: any, item: any) {
    return arr?.filter((i: any) => Number(i) === Number(item))?.length > 0
        ? arr?.filter((i: any) => Number(i) !== Number(item)) // remove item
        : [...arr, Number(item)];
}

const getLanguageCode = (language_id: any): any => {
    const languages = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];
    return languages.find((id: any, index: any) => (language_id - 1) === index);
}

export const localeMomentEventDates = (date: any, language_id: any, eventTimezone:any) => {
    let locale = 'en';
    let format = 'dddd, D. MMMM  YYYY';
    if (language_id == 2) {
        locale = 'da';
        format = 'dddd, D. MMMM  YYYY';
    } else if (language_id == 3) {
        locale = 'no';
        format = 'dddd, D. MMMM  YYYY';
    } else if (language_id == 4) {
        locale = 'de';
        format = 'dddd, D. MMMM  YYYY';
    } else if (language_id == 5) {
        locale = 'lt';
        format = 'dddd, D. MMMM  YYYY';

    } else if (language_id == 6) {
        locale = 'fi';
        format = 'dddd, D. MMMM  YYYY';

    } else if (language_id == 7) {
        locale = 'se';
        format = 'dddd, D. MMMM  YYYY';

    } else if (language_id == 8) {
        locale = 'nl';
        format = 'dddd, D. MMMM  YYYY';

    } else if (language_id == 9) {
        locale = 'be';
        format = 'dddd, D. MMMM  YYYY';
    }
    return moment(date).tz(eventTimezone).locale(locale).format(format).charAt(0).toUpperCase() + moment(date).tz(eventTimezone).locale(locale).format(format).slice(1);
}

//The window.postMessage() method safely enables cross-origin communication between Window objects
const postMessage = (object: any) => {
    if (window && window.parent) {
        window.parent.postMessage(object, '*');
    }
}

const facebookPixel = (id: string, view: boolean, event: any, data: any = {}) => {

    ReactPixel.init(id);

    if (view) {
        ReactPixel.pageView();
    }

    if (Object.keys(data).length > 0) {
        ReactPixel.fbq('track', event, data);
    } else {
        ReactPixel.fbq('track', event);
    }

}

const linkedinPixel = (partner_id: string, conversion_id: string, view: boolean) => {
    LinkedInTag.init(partner_id, 'dc');
    if (!view) {
        LinkedInTag.track(conversion_id);
    }
}

const googleTagManager = (id: string, view: boolean, event: any, data: any = {}) => {

    ReactGA.initialize(id);

    if (!view) {
        ReactGA.event(event, data);
    }

}

export {
    formatString,
    ltrim,
    isSafari,
    isCompatibleChrome,
    isFirefox,
    getCurrency,
    number_format,
    toggleValueInArray,
    getLanguageCode,
    postMessage,
    facebookPixel,
    linkedinPixel,
    googleTagManager
};