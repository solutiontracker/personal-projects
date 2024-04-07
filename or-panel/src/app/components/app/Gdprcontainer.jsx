import * as React from "react";
import { NavLink } from 'react-router-dom';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import CKEditor from 'ckeditor4-react';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class Gdprcontainer extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      subject: '',
      inline_text: '',
      description: '',

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false,

      change: false
    };

    this.config = {
      htmlRemoveTags: ['script'],
    };
  }

  componentDidMount() {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/event-settings/gdpr-disclaimer`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              if (response.data) {
                this.setState({
                  subject: response.data.subject,
                  inline_text: response.data.inline_text,
                  description: response.data.description,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  save = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.setState({ isLoader: type });
    service.put(`${process.env.REACT_APP_URL}/event-settings/gdpr-disclaimer/update`, this.state)
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
            if (type === "save-next") this.props.history.push('/event/registration/tos');
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
      description: e.editor.getData(),
      change: true
    });
  }

  handleChange = input => e => {
    this.setState({
      [input]: e.target.value,
      change: true
    })
  };

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
                    <h1 className="section-title">{t('GDPR_NAME')}</h1>
                    <p>{t('GDPR_SUB_HEADING')}</p>
                    <Input
                      type='text'
                      label={t('GDPR_SUBJECT')}
                      name='subject'
                      value={this.state.subject}
                      onChange={this.handleChange('subject')}
                      required={true}
                    />
                    {this.state.errors.subject && <p className="error-message">{this.state.errors.subject}</p>}
                    <Input
                      type='text'
                      label={t('GDPR_LINK')}
                      name='content'
                      value={this.state.inline_text}
                      onChange={this.handleChange('inline_text')}
                      required={true}
                    />
                    {this.state.errors.content && <p className="error-message">{this.state.errors.content}</p>}
                    <p><em>{t('GDPR_LINK_INFO')}</em></p>
                    <CKEditor
                      data={this.state.description}
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
                    {this.state.errors.description &&
                      <p className="error-message">{this.state.errors.description}</p>}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t('G_PREVIEW')}
                    </NavLink>
                    <NavLink className="btn btn-prev-step" to={`/event/registration/manage/hotels`}><span className="material-icons">
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
export default Gdprcontainer;