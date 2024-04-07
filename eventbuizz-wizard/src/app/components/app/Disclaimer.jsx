import * as React from "react";
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import CKEditor from 'ckeditor4-react';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class Disclaimer extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      disclaimer: '',

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false,

      next: (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event/module/eventsite-module-order"),
      change: false
    };

    this.config = {
      htmlRemoveTags: ['script'],
    };
  }

  componentDidMount() {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/event-settings/disclaimer`)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  disclaimer: response.data.disclaimer,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );

    //set next previous
    if (this.props.event.modules !== undefined && this.props.event.modules.length > 0 && Number(this.props.event.is_registration) === 0 && Number(this.props.event.is_app) === 0) {
      let modules = this.props.event.modules.filter(function (module, i) {
        return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
      });

      this.setState({
        next: (modules[0] !== undefined && module_routes[modules[0]['alias']] !== undefined ? module_routes[modules[0]['alias']] : "/event/manage/surveys"),
      });
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  save = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.setState({ isLoader: type });
    service.put(`${process.env.REACT_APP_URL}/event-settings/disclaimer/update`, this.state)
      .then(
        response => {
          if (response.success) {
            this.setState({
              'message': response.message,
              'success': true,
              isLoader: false,
              errors: {},
              change: false
            });
            if (type === "save-next") this.props.history.push(this.state.next);
          } else {
            this.setState({
              'message': response.message,
              'success': false,
              'isLoader': false,
              'errors': response.errors
            });
          }
        },
        error => { }
      );
  }

  handleEditorChange = (e) => {
    this.setState({
      disclaimer: e.editor.getData(),
      change: true
    });
  }

  render() {
    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content third-step">
              <ConfirmationModal update={this.state.change} />
              {this.state.message &&
                <AlertMessage
                  className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                  title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                  content={this.state.message}
                  icon={this.state.success ? "check" : "info"}
                />
              }
              {this.state.preLoader && <Loader />}
              {!this.state.preLoader && (
                <React.Fragment>
                  <div style={{ height: '100%', width: '50%' }}>
                    <h1 className="section-title">{t('DIS_TERMS_CONDITIONS')}</h1>
                    <p>{t('DIS_SUB_HEADING')}</p>
                    <CKEditor
                      data={this.state.disclaimer}
                      config={{
                        enterMode: CKEditor.ENTER_BR,
                        fullPage: true,
                        allowedContent: true,
                        extraAllowedContent: 'style[id]',
                        htmlEncodeOutput: false,
                        entities: false,
                        height: 250,
                      }}
                      onChange={this.handleEditorChange}
                    />
                    {this.state.errors.disclaimer && <p className="error-message">{this.state.errors.disclaimer}</p>}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t('G_PREVIEW')}
                    </NavLink>
                    <NavLink className="btn btn-prev-step" to={`/event/registration/gdpr`}><span className="material-icons">
                      keyboard_backspace</span></NavLink>
                    <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.save}>{this.state.isLoader === "save" ?
                      <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                    </button>
                    <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.save}>{this.state.isLoader === "save-next" ?
                      <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                    </button>
                  </div>
                </React.Fragment>
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

export default connect(mapStateToProps)(Disclaimer);