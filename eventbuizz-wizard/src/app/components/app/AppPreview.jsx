import React, { Component } from 'react';
import Img from 'react-image';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';

class AppPreview extends Component {
  constructor(props) {
    super(props);
    this.state = {
      registration: true,
      mobileMode: false,
      event_website: false,
      registration_app_url: this.props.event.registration_app_url,
      mobile_app_url: this.props.event.mobile_app_url,
      registration_form_url: this.props.event.registration_form_url
    }
  }

  handleCopy = id => e => {
    var range;
    if (document.selection) {
      range = document.body.createTextRange();
      range.moveToElementText(document.getElementById(id));
      range.select().createTextRange();
      document.execCommand("copy");
    } else if (window.getSelection) {
      range = document.createRange();
      range.selectNode(document.getElementById(id));
      window.getSelection().addRange(range);
      document.execCommand("copy");
      confirmAlert({
        customUI: ({ onClose }) => {
          return (
            <Translation >
              {
                t =>
                  <div className='app-main-popup'>
                    <div className="app-header">
                      <h4>Information</h4>
                    </div>
                    <div className="app-body">
                      <p>URL has been copied successfully!</p>
                    </div>
                    <div className="app-footer">
                      <button className="btn btn-success" onClick={onClose}>Ok</button>
                    </div>
                  </div>
              }
            </Translation>
          );
        }
      });
    }
  }
  handleClick = type => e => {
    e.preventDefault();
    this.setState({
      registration: false,
      mobileMode: false,
      event_website: false,
      [type]: true
    })
  }

  render() {
    const { mobileMode, registration_app_url, registration_form_url, mobile_app_url, registration, event_website } = this.state;
    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content">
              <div className="header-tab-preview">
                <span className='app-heading'>{t('PRV_PREVIEW')}</span>
                <span onClick={this.handleClick('registration')} className={registration ? 'active' : ''}>{t('PRV_ONLINE_REGISTRATION_VIEW')}</span>
                <span onClick={this.handleClick('event_website')} className={event_website ? 'active' : ''}>{t('PRV_EVENT_WEBSITE')}</span>
                <span onClick={this.handleClick('mobileMode')} className={mobileMode ? 'active' : ''}>{t('PRV_MOBILE_APP')}</span>
              </div>
              {registration && (
                <React.Fragment>
                  <div id="desktoppreview">
                    <div className="screen-preview">
                      <iframe title="iframe" src={registration_form_url} width="100%" height="410px" frameBorder="0"></iframe>
                    </div>
                  </div>
                  <div className="bottom-url">
                    <div className="wrapp-box">
                      <i onClick={this.handleCopy('copyelement')} className="icons"><Img src={require('img/ico-copyurl.svg')} /></i>
                      <p>{t('ONLINE_REGISTRATION_PRV_COPY_LINK_INFO')}</p>
                      <a target="_blank" id="copyelement" href={registration_form_url}>{registration_form_url}</a>
                    </div>
                  </div>
                </React.Fragment>
              )}
              {event_website && (
                <React.Fragment>
                  <div id="desktoppreview">
                    <div className="screen-preview">
                      <iframe title="iframe" src={registration_app_url} width="100%" height="410px" frameBorder="0"></iframe>
                    </div>
                  </div>
                  <div className="bottom-url">
                    <div className="wrapp-box">
                      <i onClick={this.handleCopy('copyelement')} className="icons"><Img src={require('img/ico-copyurl.svg')} /></i>
                      <p>{t('EVENT_WEBSITE_PRV_COPY_LINK_INFO')}</p>
                      <a target="_blank" id="copyelement" href={registration_app_url}>{registration_app_url}</a>
                    </div>
                  </div>
                </React.Fragment>
              )}
              {mobileMode && (
                <div id="mobilepreview">
                  <div className="screen-preview-mobile">
                    <iframe title="iframe" src={mobile_app_url} width="100%" height="628px" frameBorder="0"></iframe>
                  </div>
                  <div className="bottom-url">
                    <div className="wrapp-box">
                      <i onClick={this.handleCopy('copyelement')} className="icons"><Img src={require('img/ico-copyurl.svg')} /></i>
                      <p>{t('EVENT_APP_PRV_COPY_LINK_INFO')}</p>
                      <a target="_blank" id="copyelement" href={mobile_app_url}>{mobile_app_url}</a>
                    </div>
                  </div>
                </div>
              )}
            </div>
        }
      </Translation>
    )
  }
}

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(AppPreview);
