import React, { useEffect } from 'react';
import 'sass/app.scss';
import RouterOutlet from 'router/RouterOutlet'
import { withRouter } from "react-router-dom";
import HttpsRedirect from 'react-https-redirect';
import { confirmAlert } from 'react-confirm-alert';
import { store } from 'helpers';
import { useTranslation } from "react-i18next";
import {
  isMobileOnly
} from "react-device-detect";

function App() {
  const { t } = useTranslation();

  // Similar to componentDidMount and componentDidUpdate:
  useEffect(() => {
    const reduxStore = store.getState();

    //set default language 
    if (isMobileOnly) {

      confirmAlert({
        customUI: ({ onClose }) => {
          return (
            <div className='app-popup-wrapper'>
              <div className="app-popup-container" style={{ width: '90%' }}>
                <div style={{ backgroundColor: reduxStore.event.settings.primary_color }} className="app-popup-header">
                  {t('G_NOT_SUPPORTED')}
                </div>
                <div className="app-popup-pane">
                  <div className="gdpr-popup-sec">
                    <p>{t('G_PLATEFORM_NOT_SUPPORTED_DESCRIPTION')}</p>
                  </div>
                </div>
                <div className="app-popup-footer">
                  <button
                    style={{ backgroundColor: reduxStore.event.settings.primary_color }}
                    className="btn btn-success"
                    onClick={() => {
                      window.open(`${process.env.REACT_APP_EVENTCENTER_URL}/event/${reduxStore.event.url}`, "_blank")
                    }}
                  >
                    {t('G_GO_TO_WEB_APP')}
                  </button>
                </div>
              </div>
            </div>
          );
        },
        closeOnClickOutside: false
      });
    }
  });

  return (
    <HttpsRedirect disabled={process.env.REACT_APP_SSL === 'false' ? true : false}>
      <div id="App">
        <RouterOutlet />
      </div>
    </HttpsRedirect>
  );
}


export default withRouter(App);
