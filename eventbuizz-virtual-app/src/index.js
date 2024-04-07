import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import './sass/app.scss';
import App from './app/App.jsx';
import registerServiceWorker from './registerServiceWorker';
import { Provider } from 'react-redux';
import { ltrim, store } from 'helpers';
import { GeneralAction } from 'actions/general-action';
import { initReactI18next } from "react-i18next";
import i18n from "i18next";
import './i18n';

let path = ltrim(window.location.pathname, "/");

let params = path.split("/");

const _lang = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];

async function loadEvent(url) {
    const response = await fetch(`${process.env.REACT_APP_URL}/${url}`);
    const json = await response.json();
    if (json.event !== undefined) {
        store.dispatch({ type: "event-info", event: json.event });
        store.dispatch(GeneralAction.stream({}));
        document.title = json.event.name;
        localStorage.removeItem('streamInfo');
        localStorage.removeItem('agoraInfo');
        localStorage.removeItem('videoInfo');
        if (json.event.settings.fav_icon) {
            const favicon = document.getElementById("favicon");
            favicon.href = `${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${json.event.settings.fav_icon}`;
        }

        //set interface language
        i18n.use(initReactI18next)
        .init({ lng: _lang[json.event.language_id - 1] });
    }
    return json;
}

loadEvent(params.length >= 2 ? params[1] : '').then(response => {
    if (response.success && response.success !== undefined) {
        ReactDOM.render(<BrowserRouter><Provider store={store}><App /></Provider></BrowserRouter>, document.getElementById('root'));
    } else {
        ReactDOM.render("", document.getElementById('root'));
    }
    registerServiceWorker();
    return response;
});


