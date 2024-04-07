import * as React from 'react';
import DateTime from '@/app/forms/DateTime';
import Input from '@/app/forms/Input';
import Timepicker from '@/app/forms/Timepicker';
import { connect } from 'react-redux';
import moment from 'moment';
import SimpleReactValidator from 'simple-react-validator';
import { service } from 'services/service';
import { Translation, withTranslation } from "react-i18next";
import Loader from '@/app/forms/Loader';

class Clone extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            name: '',
            location_name: '',
            start_date: '',
            start_time: '',
            end_date: '',
            end_time: '',

            //errors & loading
            message: false,
            success: true,
            errors: {},
            preLoader: false,

            change: false
        }

        this.validator = new SimpleReactValidator({
            element: message => <p className="error-message">{message}</p>,
            messages: {
                required: this.props.t("EE_FIELD_IS_REQUIRED")
            },
        })
    }

    handleChange = (input, type) => e => {
        var value = (type === "dropdown" ? (e !== null ? e.value : null) : e.target.value);
        if (value === undefined || value === null) {
            this.setState({
                [input]: '',
                change: true
            })
        } else {
            this.setState({
                [input]: value,
                change: true
            })
        };
    }

    handleDateChange = (input) => e => {
        if (e !== undefined && e !== 'Invalid date' && e !== 'cleardate') {
            var date = moment(new Date(e)).format('YYYY-MM-DD');
            if (input === 'start_date') {
                const timedifference = moment(new Date(date)).diff(moment(new Date(this.state.end_date)), 'days');
                this.setState({
                    [input]: date,
                    end_date: timedifference > 0 ? date : this.state.end_date,
                    change: true
                });
            } else {
                this.setState({
                    [input]: date,
                    change: true
                });
            }
        } else {
            if (input === 'start_date') {
                this.setState({
                    [input]: '',
                    end_date: '',
                    change: true
                });
            } else {
                this.setState({
                    [input]: '',
                    change: true
                });
            }
        }
    }

    handleTimeChange = (input, value, validate = null) => {
        if (value !== '') {
            this.setState({
                [input]: value,
                change: true
            })
        } else {
            this.setState({
                [input]: '',
                change: true
            })
        }
    }

    save = e => {
        if (this.validator.allValid()) {
            this.setState({ preLoader: true });
            service.post(`${process.env.REACT_APP_URL}/event/copy/${this.props.eventID}`, this.state)
                .then(
                    response => {
                        if (response.success) {
                            this.setState({
                                message: response.message,
                                success: true,
                                preLoader: false,
                                errors: {},
                                change: false
                            });
                            this.props.openEvent(response.event);
                        } else {
                            this.setState({
                                message: response.message,
                                success: false,
                                preLoader: false,
                                errors: response.errors
                            });
                        }
                    },
                    error => { }
                );
        } else {
            this.validator.showMessages();
            this.forceUpdate();
        }
    }

    render() {

        return (
            <Translation>
                {(t) => (
                    <div id="react-confirm-alert">
                        {this.state.preLoader && <Loader className='fixed' />}
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div style={{ maxWidth: '480px' }} className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t("G_CREATE_EVENT")}</h4>
                                    </div>
                                    <div style={{ padding: '30px 15px 0' }} className="app-body">
                                        <Input
                                            type='text'
                                            label={t('ED_NAME')}
                                            value={this.state.name}
                                            onChange={this.handleChange('name', 'text')}
                                            required={true}
                                            onBlur={() => this.validator.showMessageFor('name')}
                                        />
                                        {this.validator.message('name', this.state.name, 'required')}
                                        {this.state.errors.name && (
                                            <p className="error-message">{this.state.errors.name}</p>
                                        )}
                                        <Input
                                            type='text'
                                            label={t('ED_LOCATION_NAME')}
                                            value={this.state.location_name}
                                            onChange={this.handleChange('location_name', 'text')}
                                            required={true}
                                            onBlur={() => this.validator.showMessageFor('location_name')}
                                        />
                                        {this.validator.message('location_name', this.state.location_name, 'required')}
                                        {this.state.errors.location_name && (
                                            <p className="error-message">{this.state.errors.location_name}</p>
                                        )}
                                        <div className="row d-flex shrink-row">
                                            <div className="col-6">
                                                <DateTime onBlur={() => this.validator.showMessageFor('start_date')} fromDate={new Date()} value={this.state.start_date} onChange={this.handleDateChange('start_date')} label={t('ED_START_DATE')} required={true} />
                                                {this.validator.message('start_date', this.state.start_date, 'required')}
                                                {this.state.errors.start_date && (
                                                    <p className="error-message">{this.state.errors.start_date}</p>
                                                )}
                                            </div>
                                            <div className="col-6">
                                                <Timepicker
                                                    label={t('ED_START_TIME')}
                                                    value={this.state.start_time}
                                                    onChange={this.handleTimeChange.bind(this)}
                                                    stateName='start_time'
                                                    required={true}
                                                    onBlur={() => this.validator.showMessageFor('start_time')}
                                                />
                                                {this.validator.message('start_time', this.state.start_time, 'required')}
                                                {this.state.errors.start_time && (
                                                    <p className="error-message">{this.state.errors.start_time}</p>
                                                )}
                                            </div>
                                            <div className="col-6">
                                                <DateTime onBlur={() => this.validator.showMessageFor('end_date')} fromDate={(this.state.start_date ? new Date(this.state.start_date) : new Date())} value={this.state.end_date} onChange={this.handleDateChange('end_date')} label={t('ED_END_DATE')} required={true} />
                                                {this.validator.message('end_date', this.state.end_date, 'required')}
                                                {this.state.errors.end_date && (
                                                    <p className="error-message">{this.state.errors.end_date}</p>
                                                )}
                                            </div>
                                            <div className="col-6">
                                                <Timepicker
                                                    label={t('ED_END_TIME')}
                                                    value={this.state.end_time}
                                                    onChange={this.handleTimeChange.bind(this)}
                                                    stateName='end_time'
                                                    required={true}
                                                    onBlur={() => this.validator.showMessageFor('end_time')}
                                                />
                                                {this.validator.message('end_time', this.state.end_time, 'required')}
                                                {this.state.errors.end_time && (
                                                    <p className="error-message">{this.state.errors.end_time}</p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={this.props.onClose}>Cancel</button>
                                        <button className="btn btn-success"
                                            disabled={this.state.preLoader ? true : false}
                                            onClick={this.save.bind(this)}
                                        >
                                            {t("G_CREATE_EVENT")}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
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

export default connect(mapStateToProps)(withTranslation()(Clone));