import * as React from 'react';
import { Translation } from "react-i18next";

const Loader = ({ className, fixed }) => {
  return (
    <Translation>
      {
        t => <div id="loader-wrapper" className={`${className && className} ${fixed && 'popup-fixed'}`}>
          {className ? (
            <div className="wrapper_laoder">
              <h2>{t('ED_LOADER_WAIT_MINUTE')}</h2>
              <p>{t('ED_LOADER_OUR_COMPUTER_BUSY_INFO')} </p>
              <div id="loader"></div>
            </div>
          ) : (
              <div id="loader"></div>
            )}
        </div>
      }
    </Translation>
  );
}

export default Loader;

