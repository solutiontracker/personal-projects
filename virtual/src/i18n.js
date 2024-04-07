import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import en from './lang/en.json';
import da from './lang/da.json';
import de from './lang/de.json';
import be from './lang/be.json';
import fi from './lang/fi.json';
import nl from './lang/nl.json';
import no from './lang/no.json';
import se from './lang/se.json';
import lt from './lang/se.json';

// the translations
const resources = {
    en: {
        translation: en
    },
    da: {
        translation: da
    },
    de: {
        translation: de
    },
    be: {
        translation: be
    },
    fi: {
        translation: fi
    },
    nl: {
        translation: nl
    },
    no: {
        translation: no
    },
    se: {
        translation: se
    },
    lt: {
        translation: lt
    },
};

i18n.use(initReactI18next) // passes i18n down to react-i18next
    .init({
        resources,
        lng: 'da',
        fallbackLng: "da", // use en if detected lng is not available
        keySeparator: false, // we do not use keys in form messages.welcome
        interpolation: {
            escapeValue: false // react already safes from xss
        }
    });

export default i18n;