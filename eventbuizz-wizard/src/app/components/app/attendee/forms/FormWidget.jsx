import * as React from 'react';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import Input from '@/app/forms/Input';
import { AttendeeService } from 'services/attendee/attendee-service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import 'react-phone-input-2/lib/style.css'
import { Translation } from "react-i18next";
import { GeneralService } from 'services/general-service';
import DropDown from '@/app/forms/DropDown';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FormWidget extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      initial: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.initial : ''),
      title: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.title : ''),
      first_name: (this.props.editdata !== undefined ? this.props.editdata.first_name : ''),
      last_name: (this.props.editdata !== undefined ? this.props.editdata.last_name : ''),
      email: (this.props.editdata !== undefined ? this.props.editdata.email : ''),
      phone: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.phone : ''),
      calling_code: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.calling_code : ''),
      company_name: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.company_name : ''),
      department: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.department : ''),
      delegate_number: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.delegate_number : ''),
      network_group: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.network_group : ''),
      table_number: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.table_number : ''),
      ss_number: "",
      old_ss_number: (this.props.editdata !== undefined ? this.props.editdata.ss_number : ''),
      is_ss_number: (this.props.editdata !== undefined ? this.props.editdata.ss_number : '') ? true : false,
      allow_vote: (this.props.editdata !== undefined ? this.props.editdata.event.allow_vote : 0),
      ask_to_apeak: (this.props.editdata !== undefined ? this.props.editdata.event.ask_to_apeak : 0),
      gdpr: (this.props.editdata !== undefined ? this.props.editdata.event.gdpr : 1),
      attendee_type_id: (this.props.editdata !== undefined ? this.props.editdata.event.attendee_type : 0),
      display: true,
      calling_codes: [],
      attendee_types: [],
      event_country_code: '',

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,

      // Valdiation
      first_name_validate: (this.props.editdata !== undefined ? 'success' : ''),
      email_validate: (this.props.editdata !== undefined ? 'success' : ''),

      change: false
    }
  }

  componentDidMount() {
    this._isMounted = true;
    console.log(this.props)
    this.setState({
      attendee_types: this.props.attendee_types
    });
    this.metadata();
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  metadata() {
    this.setState({ preLoader: true });
    GeneralService.metaData()
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                calling_codes: response.data.records.country_codes,
                event_country_code: response.data.records.event_country_code,
                calling_code: (!this.state.calling_code ? response.data.records.event_country_code : this.state.calling_code),
              });
            }
          }
        },
        error => { }
      );
  }

  handleChange = (input, item, type) => e => {
    if (e.target.value === undefined) {
      this.setState({
        [input]: [],
        change: true
      })
    } else {
      if (item && type) {
        const { dispatch } = this.props;
        const validate = dispatch(AuthAction.formValdiation(type, e.target.value));
        if (validate.status) {
          this.setState({
            [input]: e.target.value,
            [item]: 'success',
            change: true
          })
        } else {
          this.setState({
            [input]: e.target.value,
            [item]: 'error',
            change: true
          })
        }
      } else {
        this.setState({
          [input]: e.target.value,
          change: true
        })
      }
    }
  }

  saveData = (e) => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    if (this.state.first_name_validate === 'error' || this.state.first_name_validate.length === 0) {
      this.setState({
        first_name_validate: 'error'
      })
    }
    if (this.state.email_validate === 'error' || this.state.email_validate.length === 0) {
      this.setState({
        email_validate: 'error'
      })
    }
    if (this.state.first_name_validate === 'success' &&
      this.state.email_validate === 'success') {
      this.setState({ isLoader: type });
      if (this.props.editdata !== undefined) {
        AttendeeService.update(this.props.editdata.id, this.state)
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
                this.props.listing(1, true, type);
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
      } else {
        AttendeeService.create(this.state)
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
                this.props.listing(1, false, type);
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
    }
  }

  handleChangePhone = (input) => e => {
      this.setState({
        [input]: e.value,
        change: true
      })
  }

  handleCheckbox = (input) => e => {
    this.setState({
      [input]: (Number(this.state[input]) === 1 ? 0 : 1),
      change: true
    })
  }

  render() {
    this.getSelectedLabel = (item, id,type) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        if(type=='attendee_type'){
          return (obj ? obj.attendee_type : 'Select attendee type');
        }else{
          return (obj ? obj.name : '');
        }
        
      }
    }

    return (
      <Translation>
        {
          t =>
            <div className={`hotel-add-item ${this.props.editdata ? 'isGray' : ''}`}>
              <ConfirmationModal update={this.state.change} />
              {this.state.message &&
                <AlertMessage
                  className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                  title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                  content={this.state.message}
                  icon={this.state.success ? "check" : "info"}
                />
              }
              <h4 className="component-heading">{(this.props.editdata !== undefined ? t('ATTENDEE_EDIT') : t('ATTENDEE_ADD'))}</h4>
              <div className="row d-flex">
                <div className="col-1">
                  <Input
                    type='text'
                    label={t('ATTENDEE_INITIAL')}
                    value={this.state.initial}
                    name='initial'
                    onChange={this.handleChange('initial')}
                    required={false}
                  />
                  {this.state.errors.initial && <p className="error-message">{this.state.errors.initial}</p>}
                </div>
                <div className="col-3 PriceNight">
                  <Input
                    className={this.state.first_name_validate}
                    type='text'
                    label={t('ATTENDEE_FIRST_NAME')}
                    name='first_name'
                    value={this.state.first_name}
                    onChange={this.handleChange('first_name', 'first_name_validate', 'text')}
                    required={true}
                  />
                  {this.state.errors.first_name && <p className="error-message">{this.state.errors.first_name}</p>}
                  {this.state.first_name_validate === 'error' &&
                    <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                </div>
                <div className="col-3 PriceNight">
                  <Input
                    type='text'
                    label={t('ATTENDEE_LAST_NAME')}
                    name='last_name'
                    value={this.state.last_name}
                    onChange={this.handleChange('last_name')}
                    required={false}
                  />
                  {this.state.errors.last_name && <p className="error-message">{this.state.errors.last_name}</p>}
                </div>
                <div className="col-3">
                  <Input
                    type='text'
                    label={t('ATTENDEE_TITLE')}
                    name='title'
                    value={this.state.title}
                    onChange={this.handleChange('title')}
                    required={false}
                  />
                  {this.state.errors.title && <p className="error-message">{this.state.errors.title}</p>}
                </div>
                <div className="col-3 PriceNight">
                  <Input
                    className={this.state.email_validate}
                    type='email'
                    label={t('ATTENDEE_EMAIL')}
                    name='email'
                    value={this.state.email}
                    onChange={this.handleChange('email', 'email_validate', 'email')}
                    required={true}
                  />
                  {this.state.errors.email && <p className="error-message">{this.state.errors.email}</p>}
                  {
                    (() => {
                      if (this.state.email_validate === 'error' && this.state.email)
                        return <p className="error-message">{t('EE_VALID_EMAIL')}</p>
                      else if (this.state.email_validate === 'error' && !this.state.email)
                        return <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>
                    })()
                  }
                </div>
              </div>
              <div className="row d-flex">
                <div className="col-3 d-flex custom-phone-field">
                  {this.state.calling_codes.length > 0 && (
                    <DropDown
                      className=''
                      label={false}
                      listitems={this.state.calling_codes}
                      selected={(this.state.calling_code ? this.state.calling_code : this.state.event_country_code)}
                      selectedlabel={this.getSelectedLabel(this.state.calling_codes, (this.state.calling_code ? this.state.calling_code : this.state.event_country_code),'calling_code')}
                      onChange={this.handleChangePhone('calling_code')}
                      required={false}
                    />
                  )}
                  <Input
                    type='text'
                    value={`${this.state.phone}`}
                    label={t('ATTENDEE_PHONE')}
                    pattern='[0-9]*'
                    onChange={this.handleChange('phone')}
                    required={false}
                  />
                  {this.state.errors.phone && <p className="error-message">{this.state.errors.phone}</p>}
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    label={t('ATTENDEE_COMPANY_NAME')}
                    name='company_name'
                    value={this.state.company_name}
                    onChange={this.handleChange('company_name')}
                    required={false}
                  />
                  {this.state.errors.company_name &&
                    <p className="error-message">{this.state.errors.company_name}</p>}
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    label={t('ATTENDEE_DEPARTMENT')}
                    name='department'
                    value={this.state.department}
                    onChange={this.handleChange('department')}
                    required={false}
                  />
                  {this.state.errors.department && <p className="error-message">{this.state.errors.department}</p>}
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    label={t('ATTENDEE_DELEGATE_NUMBER')}
                    name='delegate_number'
                    value={this.state.delegate_number}
                    onChange={this.handleChange('delegate_number')}
                    required={false}
                  />
                  {this.state.errors.delegate_number &&
                    <p className="error-message">{this.state.errors.delegate_number}</p>}
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    label={t('ATTENDEE_NETWORK_GROUP')}
                    name='network_group'
                    value={this.state.network_group}
                    onChange={this.handleChange('network_group')}
                    required={false}
                  />
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    label={t('ATTENDEE_TABLE_NUMBER')}
                    name='table_number'
                    value={this.state.table_number}
                    onChange={this.handleChange('table_number')}
                    required={false}
                  />
                </div>
                <div className="col-3 orginnal">
                  <Input
                    type='text'
                    icon={this.state.is_ss_number ? 'mode_edit' : 'clear'}
                    label={t('ATTENDEE_CPR_NUMBER')}
                    name='ss_number'
                    onChange={this.handleChange('ss_number')}
                    required={false}
                    onClick={() => this.setState({is_ss_number : ''})}
                    disabled={this.state.is_ss_number}
                    value={this.state.is_ss_number ? 'xxxxxxxxxx' : this.state.ss_number}
                  />
                  {this.state.errors.ss_number &&
                    <p className="error-message">{this.state.errors.ss_number}</p>}
                </div>
                <div className="col-3 orginnal">
                {this.state.attendee_types.length > 0 && (
                    <DropDown
                      className=''
                      label={false}
                      listitems={this.state.attendee_types}
                      selected={(this.state.attendee_type_id ? this.state.attendee_type_id : 0)}
                      selectedlabel={this.getSelectedLabel(this.state.attendee_types, (this.state.attendee_type_id ? this.state.attendee_type_id :0),'attendee_type')}
                      onChange={this.handleChangePhone('attendee_type_id')}
                      required={true}
                      type='attendee_type'
                    />
                  )}
                  {this.state.errors.attendee_type_id &&
                    <p className="error-message">{this.state.errors.attendee_type_id}</p>}
                </div>
              </div>
              <div className="row d-flex">
                <div className="col-3 d-flex">
                  <p>{t('ATTENDEE_ALLOW_VOTE')} </p>
                  <label className="custom-checkbox-toggle float-right"><input onClick={this.handleCheckbox('allow_vote')}
                    defaultChecked={Number(this.state.allow_vote) === 1 ? true : false}
                    type="checkbox"
                    name="" /><span></span></label>
                </div>
                <div className="col-3 d-flex">
                  <p>{t('ATTENDEE_ASK_TO_SPEAK')} </p>
                  <label className="custom-checkbox-toggle float-right"><input onClick={this.handleCheckbox('ask_to_apeak')}
                    defaultChecked={Number(this.state.ask_to_apeak) === 1 ? true : false}
                    type="checkbox"
                    name="" /><span></span></label>
                </div>
                <div className="col-3 d-flex">
                  <p>{t('GDPR')} </p>
                  <label className="custom-checkbox-toggle float-right"><input onClick={this.handleCheckbox('gdpr')}
                    defaultChecked={Number(this.state.gdpr) === 1 ? true : false}
                    type="checkbox"
                    name="" /><span></span></label>
                </div>
              </div>
              <div className="bottom-panel-button">
                <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save" className="btn">{this.state.isLoader === "save" ?
                  <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t('G_SAVE'))}</button>
                {!this.props.editdata && (
                  <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save-new" className="btn save-new">{this.state.isLoader === "save-new" ?
                    <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_AND_ADD_ANOTHER')}</button>
                )}
                <button className="btn btn-cancel" onClick={this.props.datacancel}>{t('G_CANCEL')}</button>
              </div>
            </div>
        }
      </Translation>
    )
  }
}

function mapStateToProps(state) {
  const { alert } = state;
  return {
    alert
  };
}

export default connect(mapStateToProps)(withRouter(FormWidget));