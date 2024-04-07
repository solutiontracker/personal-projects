import React, { useState, useEffect } from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import DateTime from '@/app/forms/DateTime';
import Input from '@/app/forms/Input';
import Loader from '@/app/forms/Loader';
import Timepicker from '@/app/forms/Timepicker';
import DropDown from '@/app/forms/DropDown';
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';
import { GeneralService } from 'services/general-service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import moment from 'moment';
import Geocode from "react-geocode";
import {
  withScriptjs,
  withGoogleMap,
  GoogleMap,
  Marker
} from "react-google-maps";

Geocode.setApiKey(process.env.REACT_APP_GEO_LOCATION_API);

const MyMapComponent = withScriptjs(
  withGoogleMap(props => {
    return (
      <GoogleMap
        defaultZoom={17}
        center={{ lat: props.lat, lng: props.lng }}
      >
        {props.isMarkerShown && <Marker position={{ lat: props.lat, lng: props.lng }} />}
      </GoogleMap>
    );
  })
);

function Map(props) {
  const [didMount, setDidMount] = useState(false);

  useEffect(() => {
    setDidMount(true);
    return () => setDidMount(false);
  }, [])

  if (!didMount) {
    return null;
  }

  if (props.lat && props.lng) {
    return (
      <div>
        <MyMapComponent
          isMarkerShown
          lat={props.lat}
          lng={props.lng}
          location={props.location}
          googleMapURL={`https://maps.googleapis.com/maps/api/js?key=${process.env.REACT_APP_MAP_API_KEY}&v=3.exp&libraries=geometry,drawing,places`}
          loadingElement={<div style={{ height: `350px` }} />}
          containerElement={<div style={{ height: `350px` }} />}
          mapElement={<div style={{ height: `100%` }}
          />}
        />
      </div>
    );
  } else {
    return ""
  }

}

class EventDateTime extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      location_name: (this.props.eventState.duration ? this.props.eventState.duration.location_name : (this.props.template ? this.props.template.info.location_name : '')),
      location_address: (this.props.eventState.duration ? this.props.eventState.duration.location_address : (this.props.template ? this.props.template.info.location_address : '')),
      country_id: (this.props.eventState.duration ? this.props.eventState.duration.country_id : (this.props.template ? this.props.template.event.country_id : '57')),
      timezone_id: (this.props.eventState.duration ? this.props.eventState.duration.timezone_id : (this.props.template ? this.props.template.event.timezone_id : '51')),
      readonly: (this.props.eventState.duration ? this.props.eventState.duration.readonly : false),
      start_date: (this.props.eventState.duration ? this.props.eventState.duration.start_date : ''),
      start_time: (this.props.eventState.duration ? this.props.eventState.duration.start_time : '08:00'),
      end_date: (this.props.eventState.duration ? this.props.eventState.duration.end_date : ''),
      end_time: (this.props.eventState.duration ? this.props.eventState.duration.end_time : '13:00'),
      cancellation_date: (this.props.eventState.duration ? this.props.eventState.duration.cancellation_date : ''),
      cancellation_end_time: (this.props.eventState.duration ? this.props.eventState.duration.cancellation_end_time : ''),
      registration_end_date: (this.props.eventState.duration ? this.props.eventState.duration.registration_end_date : ''),
      registration_end_time: (this.props.eventState.duration ? this.props.eventState.duration.registration_end_time : ''),
      ticket_left: (this.props.eventState.duration ? this.props.eventState.duration.ticket_left : ''),
      countries: (this.props.eventState.duration ? this.props.eventState.duration.countries : []),
      timezones: (this.props.eventState.duration ? this.props.eventState.duration.timezones : []),
      is_map: (this.props.eventState.duration ? this.props.eventState.duration.is_map : 0),

      //event template
      from_event_id: localStorage.getItem('from_event_id'),
      is_app: (localStorage.getItem('is_app') ? localStorage.getItem('is_app') : 0),
      is_registration: (localStorage.getItem('is_registration') ? localStorage.getItem('is_registration') : 0),

      // Validation
      location_name_validate: ((this.props.eventState.duration && this.props.eventState.duration.location_name) || (this.props.template && this.props.template.info.location_name ) ? 'success' : ''),
      location_address_validate: ((this.props.eventState.duration && this.props.eventState.duration.location_address) || (this.props.template && this.props.template.info.location_address ) ? 'success' : ''),
      country_id_validate: (this.props.eventState.duration && this.props.eventState.duration.country_id ? 'success' : 'success'),
      timezone_id_validate: (this.props.eventState.duration && this.props.eventState.duration.timezone_id ? 'success' : 'success'),
      start_date_validate: (this.props.eventState.duration && this.props.eventState.duration.start_date ? 'success' : ''),
      end_date_validate: (this.props.eventState.duration && this.props.eventState.duration.end_date ? 'success' : ''),
      start_time_validate: (this.props.eventState.duration && this.props.eventState.duration.start_time ? 'success' : 'success'),
      end_time_validate: (this.props.eventState.duration && this.props.eventState.duration.end_time ? 'success' : 'success'),

      //loading
      preLoader: false,

      //map
      lat: "",
      lng: "",

      change: this.props.change
    };
  }

  componentDidMount() {
    this._isMounted = true;
    if (this.state.countries.length === 0 || this.state.timezones.length === 0) {
      this.metadata();
    }
    this.findLocation();
  }

  componentWillUnmount() {
    this.props.eventState.duration = this.state;
    this.props.dispatch(EventAction.eventState(this.props.eventState));
    this._isMounted = false;
  }

  getSnapshotBeforeUpdate(prevProps, prevState) {
    this.props.eventState.duration = this.state;
    this.props.dispatch(EventAction.eventState(this.props.eventState));
    return null;
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.location_address !== this.state.location_address) {
      this.findLocation();
      this.props.dispatch(GeneralAction.update(this.state.change));
    } else if (prevState.change !== this.state.change) {
      this.props.dispatch(GeneralAction.update(this.state.change));
    }
  }

  metadata() {
    this.setState({ preLoader: true });
    GeneralService.metaData()
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                countries: response.data.records.countries,
                timezones: response.data.records.timezones,
                preLoader: false,
              });
            }
          }
        },
        error => { }
      );
  }

  continue = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    if (this.state.location_name_validate === 'error' || this.state.location_name_validate.length === 0 || this.state.location_name.length === 0) {
      this.setState({
        location_name_validate: 'error'
      })
    }
    if (this.state.location_address_validate === 'error' || this.state.location_address_validate.length === 0 || this.state.location_address.length === 0) {
      this.setState({
        location_address_validate: 'error'
      })
    }
    if (this.state.country_id_validate === 'error' || this.state.country_id_validate.length === 0 || this.state.country_id.length === 0) {
      this.setState({
        country_id_validate: 'error'
      })
    }
    if (this.state.timezone_id_validate === 'error' || this.state.timezone_id_validate.length === 0 || this.state.timezone_id.length === 0) {
      this.setState({
        timezone_id_validate: 'error'
      })
    }
    if (this.state.start_date_validate === 'error' || this.state.start_date_validate.length === 0 || this.state.start_date.length === 0) {
      this.setState({
        start_date_validate: 'error'
      })
    }
    if (this.state.end_date_validate === 'error' || this.state.end_date_validate.length === 0 || this.state.end_date.length === 0) {
      this.setState({
        end_date_validate: 'error'
      })
    }
    if (this.state.start_time_validate === 'error' || this.state.start_time_validate.length === 0 || this.state.start_time.length === 0) {
      this.setState({
        start_time_validate: 'error'
      })
    }
    if (this.state.end_time_validate === 'error' || this.state.end_time_validate.length === 0 || this.state.end_time.length === 0) {
      this.setState({
        end_time_validate: 'error'
      })
    }
    if (this.state.location_name_validate === 'success' &&
      this.state.location_address_validate === 'success' &&
      this.state.country_id_validate === 'success' &&
      this.state.timezone_id_validate === 'success' &&
      this.state.start_date_validate === 'success' &&
      this.state.end_date_validate === 'success' &&
      this.state.start_time_validate === 'success' &&
      this.state.end_time_validate === 'success') {
      var button = document.getElementById("save");
      button.setAttribute("data-type", type);
      button.click();
    }
  }

  back = e => {
    e.preventDefault();
    this.props.dispatch(GeneralAction.step((this.props.eventStep - 1)))
  }

  handleDateChange = (input, item) => e => {
    if (e !== undefined && e !== 'Invalid date' && e !== 'cleardate') {
      var date = moment(new Date(e)).format('YYYY-MM-DD');
      if (input === 'start_date') {
        const timedifference = moment(new Date(date)).diff(moment(new Date(this.state.end_date)), 'days');
        this.setState({
          [input]: date,
          end_date: timedifference > 0 ? date : this.state.end_date,
          [item]: 'success',
          change: true
        });
      } else {
        this.setState({
          [input]: date,
          [item]: 'success',
          change: true
        });
      }

    } else {
      if (input === 'start_date') {
        this.setState({
          [input]: '',
          end_date: '',
          [item]: 'error',
          change: true
        });
      } else {
        this.setState({
          [input]: '',
          [item]: 'error',
          change: true
        });
      }
    }
  }

  handleTimeChange = (input, value, validate) => {
    if (value !== '') {
      this.setState({
        [input]: value,
        [validate]: 'success',
        change: true
      })
    } else {
      this.setState({
        [input]: '',
        [validate]: 'error',
        change: true
      })
    }
  }

  handleChange = (input, item, type) => e => {
    if (item === 'validate_number') {
      const validate = (e.target.validity.valid) ? e.target.value : this.state[input];
      this.setState({
        [input]: validate,
        change: true
      });
    } else
      if (type === 'select') {
        this.setState({
          [input]: e.value,
          [item]: 'success',
          change: true
        })
      } else {
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
  }

  findLocation = () => {
    if (this.state.location_address) {
      Geocode.fromAddress(this.state.location_address).then(
        response => {
          const { lat, lng } = response.results[0].geometry.location;
          if (this._isMounted) {
            this.setState({
              lat: lat,
              lng: lng
            });
          }
        },
        error => {
          console.error(error);
        }
      );
    }
  }

  updateFlag = input => e => {
    this.setState({
      [input]: this.state[input] === 1 ? 0 : 1,
      change: true
    }, () => {
      this.props.eventState.duration = this.state;
      this.props.dispatch(EventAction.eventState(this.props.eventState));
    });
  };

  render() {
    const { location_name, location_address, country_id, timezone_id,
      start_date, start_time, end_date, end_time, cancellation_date, cancellation_end_time, registration_end_date,registration_end_time,
      ticket_left, countries, timezones } = this.state;

    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content second-step">
              {this.props.isLoader && !this.props.eventState.editData && <Loader className='fixed' />}
              {this.state.preLoader && <Loader fixed="true" />}
              {!this.state.preLoader && (
                <React.Fragment>
                  {this.props.message &&
                    <AlertMessage
                      className={`alert  ${this.props.success ? 'alert-success' : 'alert-danger'}`}
                      title={`${this.props.success ? '' : t('EE_OCCURRED')}`}
                      content={this.props.message}
                      icon={this.props.success ? "check" : "info"}
                    />
                  }
                  <h1 className="section-title">{t('ED_DATE_AND_LOCATION')}</h1>
                  <div className="row d-flex">
                    <div className="col-6">
                      <h4 className="component-heading">{t('ED_EVENT_LOCATION')}</h4>
                      <Input
                        className={this.state.location_name_validate}
                        type='text'
                        label={t('ED_LOCATION_NAME')}
                        value={location_name}
                        onChange={this.handleChange('location_name', 'location_name_validate', 'text')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.location_name &&
                        <p className="error-message">{this.props.eventState.errors.location_name}</p>}
                      {this.state.location_name_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                      <Input
                        className={this.state.location_address_validate}
                        type='text'
                        label={t('ED_LOCATION_ADDRESS')}
                        value={location_address}
                        onChange={this.handleChange('location_address', 'location_address_validate', 'text')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.location_address &&
                        <p className="error-message">{this.props.eventState.errors.location_address}</p>}
                      {this.state.location_address_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                      <DropDown
                        className={this.state.country_id_validate}
                        label={t('ED_COUNTRY')}
                        listitems={countries}
                        selected={country_id}
                        selectedlabel={this.getSelectedLabel(countries, country_id)}
                        onChange={this.handleChange('country_id', 'country_id_validate', 'select')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.country_id &&
                        <p className="error-message">{this.props.eventState.errors.country_id}</p>}
                      {this.state.country_id_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                      <DropDown
                        className={this.state.timezone_id_validate}
                        label={t('ED_TIME_ZONE')}
                        listitems={timezones}
                        selected={timezone_id}
                        selectedlabel={this.getSelectedLabel(timezones, timezone_id)}
                        onChange={this.handleChange('timezone_id', 'timezone_id_validate', 'select')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.timezone_id &&
                        <p className="error-message">{this.props.eventState.errors.timezone_id}</p>}
                      {this.state.timezone_id_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                      <div style={{ marginBottom: '0' }} className="checkbox-row">
                        <h4 className="tooltipHeading">{t('ED_SHOW_LOCATION_ON_MAP')}
                          <em className="app-tooltip"><i className="material-icons">info</i><div className="app-tooltipwrapper">{t('ED_SHOW_LOCATION_ON_MAP_TOOLTIP')}</div></em>
                        </h4>
                        <label className="custom-checkbox-toggle"><input
                          onChange={this.updateFlag('is_map')}
                          type="checkbox" defaultChecked={this.state.is_map} /><span></span></label>
                      </div>
                      {Number(this.state.is_map) === 1 ? (
                        <Map
                          location={location_address}
                          lat={this.state.lat}
                          lng={this.state.lng}
                        />
                      ) : ''}
                    </div>
                  </div>
                  <div style={{ paddingTop: '30px' }} className="row d-flex">
                    <div className="col-6">
                      <div className="row shrink-row">
                        <div className="col-7">
                          <h4 className="component-heading">{t('ED_DATE_INFO')}</h4>
                          <DateTime readOnly={this.state.readonly} fromDate={new Date()} value={start_date} onChange={this.handleDateChange('start_date', 'start_date_validate')} label={t('ED_START_DATE')} required={true} />
                          {this.props.eventState.errors && this.props.eventState.errors.start_date && <p className="error-message">{this.props.eventState.errors.start_date}</p>}
                          {this.state.start_date_validate === 'error' &&
                            <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                        </div>
                        <div className="col-5">
                          <h4 className="component-heading">&nbsp;</h4>
                          <Timepicker
                            label={t('ED_START_TIME')}
                            value={start_time}
                            onChange={this.handleTimeChange.bind(this)}
                            stateName='start_time'
                            validateName='start_time_validate'
                            required={true}
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.start_time && <p className="error-message">{this.props.eventState.errors.start_time}</p>}
                          {this.state.start_time_validate === 'error' &&
                            <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                        </div>
                        <div className="col-7">
                          <DateTime readOnly={this.state.readonly} fromDate={(this.state.start_date ? new Date(this.state.start_date) : new Date())} value={end_date} onChange={this.handleDateChange('end_date', 'end_date_validate')} label={t('ED_END_DATE')} required={true} />
                          {this.props.eventState.errors && this.props.eventState.errors.end_date && <p className="error-message">{this.props.eventState.errors.end_date}</p>}
                          {this.state.end_date_validate === 'error' &&
                            <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                        </div>
                        <div className="col-5">
                          <Timepicker
                            label={t('ED_END_TIME')}
                            value={end_time}
                            onChange={this.handleTimeChange.bind(this)}
                            stateName='end_time'
                            validateName='end_time_validate'
                            required={true}
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.end_time && <p className="error-message">{this.props.eventState.errors.end_time}</p>}
                          {this.state.end_time_validate === 'error' &&
                            <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                        </div>
                        <div className="col-7">
                          <DateTime
                            highlighted={[this.state.start_date, this.state.end_date]}
                            value={cancellation_date}
                            onChange={this.handleDateChange('cancellation_date')}
                            toDate={(this.state.end_date ? new Date(this.state.end_date) : '')}
                            fromDate={new Date()}
                            label={t('ED_CANCELLATION_DATE')} required={false} />
                          {this.props.eventState.errors && this.props.eventState.errors.cancellation_date && <p className="error-message">{this.props.eventState.errors.cancellation_date}</p>}
                        </div>
                        <div className="col-5">
                          <Timepicker
                            label={t('ED_CANCELLATION_TIME')}
                            value={cancellation_end_time}
                            onChange={this.handleTimeChange.bind(this)}
                            stateName='cancellation_end_time'
                            required={false}
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.cancellation_end_time && <p className="error-message">{this.props.eventState.errors.cancellation_end_time}</p>}
                        </div>

                        <div className="col-7">
                          <DateTime 
                            highlighted={[this.state.start_date, this.state.end_date]} 
                            value={registration_end_date} 
                            onChange={this.handleDateChange('registration_end_date')} 
                            label={t('ED_REGISTRATION_END_DATE')} 
                            required={false} 
                            toDate={(this.state.end_date ? new Date(this.state.end_date) : '')} 
                            fromDate={new Date()} 
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.registration_end_date && <p className="error-message">{this.props.eventState.errors.registration_end_date}</p>}
                        </div>
                        <div className="col-5">
                          <Timepicker
                            label={t('ED_REGISTRATION_END_TIME')}
                            value={registration_end_time}
                            onChange={this.handleTimeChange.bind(this)}
                            stateName='registration_end_time'
                            required={false}
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.registration_end_time && <p className="error-message">{this.props.eventState.errors.registration_end_time}</p>}
                        </div>
                        <div className="col-12">
                          <Input
                            type='text'
                            label={t('ED_MAX_SEATS_AVAILABLE')}
                            value={ticket_left}
                            onChange={this.handleChange('ticket_left', 'validate_number')}
                            required={false}
                            pattern='[0-9]*'
                            min='0'
                          />
                          {this.props.eventState.errors && this.props.eventState.errors.ticket_left &&
                            <p className="error-message">{this.props.eventState.errors.ticket_left}</p>}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <button id="btn-prev-step" className="btn btn-prev-step" onClick={this.back}><span className="material-icons">
                      keyboard_backspace</span></button>
                    {this.props.eventState.editData && (
                      <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                        <i className='material-icons'>remove_red_eye</i>
                        {t('G_PREVIEW')}
                      </NavLink>
                    )}
                    <button data-type="save" disabled={this.props.isLoader ? true : false} className="btn btn-save" onClick={this.continue}>{this.props.isLoader === "save" ?
                      <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}</button>
                    <button data-type="save-next" disabled={this.props.isLoader ? true : false} className="btn btn-save-next" onClick={this.continue}>{this.props.isLoader === "save-next" ?
                      <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}</button>
                    <span className="hide" onClick={this.props.save} id="save"></span>
                  </div>
                </React.Fragment>
              )}
            </div>
        }
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { eventState, eventStep, template } = state;
  return {
    eventState, eventStep, template
  };
}

export default connect(mapStateToProps)(EventDateTime);