import React, { Component } from 'react';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import moment from 'moment';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import { store } from 'helpers';
import { GeneralAction } from 'actions/general-action';

const in_array = require("in_array");

class Controls extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            event: this.props.event,
            gdpr_value: false,
            preLoader: true,
            enableEvent: true,
            enableCheckinWithoutLocatiom: true,
            eventStatusMsg: "",
            status: "",
            history: [],
            attendee: [],
            checkin: {},
            checkInOutSetting: {},
            update: this.props.update
        };

        this.updateCheckin = this.updateCheckin.bind(this);
    }

    componentDidMount() {
        this.loadData();
    }

    loadData() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/${this.props.event.url}/check-in-out`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                gdpr_value: response.data.attendee && response.data.attendee.event_attendee && Number(response.data.attendee.event_attendee.gdpr) === 1 ? true : false,
                                status: response.data.status,
                                history: response.data.history,
                                attendee: response.data.attendee,
                                enableEvent: response.data.enableEvent,
                                enableCheckinWithoutLocatiom: response.data.enableCheckinWithoutLocatiom,
                                eventStatusMsg: response.data.eventStatusMsg,
                                checkin: response.data ? response.data.checkin : {},
                                checkInOutSetting: response.data ? response.data.checkInOutSetting : {},
                                preLoader: false,
                            });
                        }
                    }
                },
                error => { }
            );
    }

    updateCheckin(event) {
        event.preventDefault();
        this._isMounted = true;
        this.setState({ preLoader: true });
        if (this.state.enableEvent) {
            service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/check-in-out/save`, this.state)
                .then(
                    response => {
                        if (response.success) {
                            if (this._isMounted) {
                                this.setState({
                                    history: response.data.history,
                                }, () => {
                                    this.loadData();
                                    confirmAlert({
                                        customUI: ({ onClose }) => {
                                            return (
                                                <div className='app-popup-wrapper'>
                                                    <div className="app-popup-container">
                                                        <div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
                                                            {this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Success'}
                                                        </div>
                                                        <div className="app-popup-pane">
                                                            <div className="gdpr-popup-sec">
                                                                <p>{response.data.message}</p>
                                                            </div>
                                                        </div>
                                                        <div className="app-popup-footer">
                                                            <button style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-success" onClick={onClose}>{this.props.event.labels.GENERAL_OK || 'OK'}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            );
                                        }
                                    });
                                });
                            }
                        }
                    },
                    error => { }
                );
        } else {
            this.setState({ preLoader: false });
            confirmAlert({
                customUI: ({ onClose }) => {
                    return (
                        <div className='app-popup-wrapper'>
                            <div className="app-popup-container">
                                <div className="app-popup-header" style={{ backgroundColor: this.props.event.settings.primary_color }}>
                                    <h4>{this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Error'}</h4>
                                </div>
                                <div className="app-popup-pane">
                                    <div className="gdpr-popup-sec">
                                        <p>{this.state.eventStatusMsg}</p>
                                    </div>
                                </div>
                                <div className="app-popup-footer">
                                    <button style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-success" onClick={onClose}>{this.props.event.labels.GENERAL_OK || 'OK'}</button>
                                </div>
                            </div>
                        </div>
                    );
                }
            });
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handleClick = (element) => (e) => {
        e.preventDefault();
        if (element === "gdpr") store.dispatch(GeneralAction.gdpr({ element: element }));
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.props.update && this.props.update.gdpr_value !== prevState.gdpr_value) {
            this.setState({ gdpr_value: this.props.update.gdpr_value })
        }
    }

    render() {
        return (
            <div className="app-gdpr-logbox">
                {this.state.preLoader && <Loader fixed="true" />}
                {!this.state.gdpr_value && Number(this.props.event.gdpr_setting.enable_gdpr) === 1 && this.state.event.settings && Number(this.state.event.settings.desktop_program_screen_sidebar_gdpr) === 1 && (
                    <div className="gdpr-acceptbox gdpr-section">
                        <div className="app-title">{this.props.event.labels.DESKTOP_APP_LABEL_GDPR || 'GDPR'}</div>
                        <div className="btn btn-accept" onClick={this.handleClick('gdpr')}>{this.props.event.labels.GDPR_ACCEPT || 'Accept'}</div>
                    </div>
                )}
                {Number(this.state.checkin.status) === 1 && Number(this.state.checkInOutSetting.show_vp) === 1 && this.state.event.settings && Number(this.state.event.settings.desktop_program_screen_sidebar_checkin) === 1 && (
                    <div className="gdpr-acceptbox checkin-toggle">
                        <div className="app-title">{(this.state.checkin && this.state.checkin.info ? this.state.checkin.info[0].value : '')}</div>
                        <div className="app-label-area">
                            <input
                                id="checkin"
                                disabled={!this.state.enableCheckinWithoutLocatiom}
                                type="radio"
                                className="app-toggle app-toggle-left"
                                value="off"
                                onChange={this.updateCheckin}
                                checked={in_array(this.state.status, ['check-out', 'attended'])}
                            />
                            <label className="btn-label" htmlFor="checkin">
                                {this.props.event.labels.GENERAL_SHORT_CHECKIN_IN || 'In'}
                            </label>
                            <input
                                id="checkout"
                                disabled={!this.state.enableCheckinWithoutLocatiom}
                                type="radio"
                                className="app-toggle app-toggle-right"
                                value="on"
                                onChange={this.updateCheckin}
                                checked={!in_array(this.state.status, ['check-out', 'attended'])}
                            />
                            <label className="btn-label" htmlFor="checkout">
                                {this.props.event.labels.GENERAL_SHORT_CHECKIN_OUT || 'Out'}
                            </label>
                        </div>
                    </div>
                )}
                {this.state.checkin && Number(this.state.checkin.status) === 1 && Number(this.state.checkInOutSetting.show_vp) === 1 && this.state.event.settings && Number(this.state.event.settings.desktop_program_screen_sidebar_checkin) === 1 && (
                    <div className="app-checkin-history">
                        {this.state.history && this.state.history.map((row, key) => {
                            return (
                                (row.checkout && row.checkout !== "0000-00-00 00:00:00" ? (
                                    <React.Fragment key={key}>
                                        <p>
                                            <strong>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_OUT}</strong>
                                            <span>{moment(row.checkout).format('DD-MM-YYYY HH:mm')}</span>
                                        </p>
                                        <p>
                                            <strong>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_IN}</strong>
                                            <span>{moment(row.checkin).format('DD-MM-YYYY HH:mm')}</span>
                                        </p>
                                    </React.Fragment>
                                ) : (
                                        <p key={key}>
                                            <strong>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_IN}</strong>
                                            <span>{moment(row.checkin).format('DD-MM-YYYY HH:mm')}</span>
                                        </p>
                                    ))
                            );
                        })}
                    </div>
                )}
            </div>
        )
    }
}

function mapStateToProps(state) {
    const { event, auth, update } = state;
    return {
        event, auth, update
    };
}

export default connect(mapStateToProps)(Controls);