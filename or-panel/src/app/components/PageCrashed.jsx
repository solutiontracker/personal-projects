import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import Img from 'react-image';
import { Translation } from "react-i18next";

export default class PageCrashed extends Component {
  render() {

    return (
      <Translation>
        {
          t =>
            <div className="page-crashed">
              <div className="inner-page-content">
                <Img src={require("img/crashed.svg")} />
                <h1>{t('CRASH_HEADING')}</h1>
                <p>{t('CRASH_SUB_HEADING')}</p>
                <Link to='/' className="btn">{t('CRASH_BACK_HOME')}</Link>
              </div>
            </div>
        }
      </Translation>
    )
  }
}
