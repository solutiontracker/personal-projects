import * as React from 'react';
import moment from 'moment';
import { connect } from 'react-redux';
import Input from '@/app/forms/Input';
import { Translation, withTranslation } from "react-i18next";
import TextArea from '@/app/forms/TextArea';
import DateTime from '@/app/forms/DateTime';
import Timepicker from '@/app/forms/Timepicker';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { ProgramService } from "services/program/program-service";
import 'react-phone-input-2/lib/style.css';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import SimpleReactValidator from 'simple-react-validator';

class FormWidget extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            id: '',
            topic: '',
            location: '',
            description: '',
            date: '',
            time: '',
            start_time: '',
            end_time: '',
            display: true,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            change: false
        }

        //Validation
        this.validator = new SimpleReactValidator({
            element: message => <p className="error-message">{message}</p>,
            messages: {
                required: this.props.t("EE_FIELD_IS_REQUIRED")
            },
        });
    }

    componentDidMount() {
        const { editdata } = this.props;
        this.setState({
            id: (editdata ? editdata.id : ''),
            topic: (editdata ? editdata.topic : ''),
            location: editdata ? editdata.location : this.props._location,
            description:  (editdata ? editdata.description : ''),
            date: (editdata && editdata.date ? moment(new Date(editdata.date)).utc().format('MM/DD/YYYY') : (this.props.date ?  moment(new Date(this.props.date)).utc().format('MM/DD/YYYY') : '')),
            time: (editdata ? editdata.start_time !== '' ? `${editdata.start_time}-${editdata.end_time}` : '' : ''),
            start_time: (editdata ? editdata.start_time : this.props.end_time),
            end_time: (editdata ? editdata.end_time : ''),
        });
    }

    timeChange = (input, date) => {
        if (input === 'time') {
            this.setState({
                [input]: date,
                start_time: date.split('-')[0],
                end_time: date.split('-')[1],
                change: true
            })
        } else {
            this.setState({
                [input]: date,
                change: true
            })
        }
    }

    handleTimeChange = (input, value, validate) => {
        if (value !== '') {
            if (input === "start_time" && !this.state.end_time) {
                this.setState({
                    start_time: value,
                    end_time: value,
                    change: true
                })
            } else {
                this.setState({
                    [input]: value,
                    [validate]: 'success',
                    change: true
                })
            }
        } else {
            this.setState({
                [input]: '',
                [validate]: 'error',
                change: true
            })
        }

    }

    handleChange = (input, type) => e => {
        if (e.target.value === undefined) {
            this.setState({
                [input]: [],
                change: true
            })
        } else {
            if (type) {
                const { dispatch } = this.props;
                const validate = dispatch(AuthAction.formValdiation(type, e.target.value));
                if (validate.status) {
                    this.setState({
                        [input]: e.target.value,
                        change: true
                    })
                } else {
                    this.setState({
                        [input]: e.target.value,
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

    handleDateChange = (input) => e => {
        if (e !== undefined && e !== 'cleardate') {
            var month = e.getMonth() + 1;
            var day = e.getDate();
            var year = e.getFullYear();
            var daydigit = (day.toString().length === 2) ? day : '0' + day;
            var date = month + '/' + daydigit + '/' + year;
            this.setState({
                [input]: date,
                change: true
            });
        } else {
            this.setState({
                [input]: '',
                change: true
            });
        }
    }

    toggleButton = () => {
        this.setState({
            display: this.state.display === true ? false : true,
            change: true
        })
    };

    saveData = (e) => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        if (this.validator.allValid()) {
            this.setState({ isLoader: type });
            if (this.props.editdata) {
                const request_data = {
                    id: this.state.id,
                    topic: this.state.topic,
                    location: this.state.location,
                    description: this.state.description,
                    date: this.state.date,
                    time: this.state.time,
                    start_time: this.state.start_time,
                    end_time: this.state.end_time
                };
                const id = this.state.id;
                ProgramService.update(request_data, id)
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
                                this.props.data(1, true, type, {});
                            } else {
                                this.setState({
                                    'message': response.message,
                                    'success': false,
                                    'isLoader': false,
                                    'errors': response.errors
                                });
                            }
                        },
                        error => { });
            } else {
                const request_data = {
                    id: this.state.id,
                    topic: this.state.topic,
                    location: this.state.location,
                    description: this.state.description,
                    date: this.state.date,
                    time: this.state.time,
                    start_time: this.state.start_time,
                    end_time: this.state.end_time
                };
                ProgramService.create(request_data)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    displayElement: false,
                                    editElement: false,
                                    message: response.message,
                                    success: true,
                                    isLoader: false,
                                    errors: {},
                                    change: false
                                });

                                this.props.data(1, false, type, {location: this.state.location, date: this.state.date, end_time: this.state.end_time});
                                
                            } else {
                                this.setState({
                                    message: response.message,
                                    success: false,
                                    isLoader: false,
                                    errors: response.errors
                                });
                            }
                        },
                        error => { });
            }
        } else {
            this.validator.showMessages();
            this.forceUpdate();
        }
    }

    render() {
        console.log(this.state.date)
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
                            <h4 className="component-heading">{(this.props.editElement ? t('PROGRAM_EDIT_SESSION') : t('PROGRAM_ADD_SESSION'))}</h4>
                            <div className="row d-flex">
                                <div className="col-6">
                                    <div className="row d-flex">
                                        <div className="col-12">
                                            <Input
                                                className={this.state.topic ? 'success' : 'error'}
                                                type='text'
                                                label={t('PROGRAM_TOPIC')}
                                                value={this.state.topic}
                                                name='topic'
                                                onChange={this.handleChange('topic', 'text')}
                                                required={true}
                                            />
                                            {this.state.errors.topic &&
                                                <p className="error-message">{this.state.errors.topic}</p>}
                                                {this.validator.message('topic', this.state.topic, 'required')}
                                        </div>
                                    </div>
                                    <div className="row d-flex">
                                        <div className="col-6">
                                            <Input
                                                type='text'
                                                label={t('PROGRAM_LOCATION')}
                                                name='location'
                                                value={this.state.location}
                                                onChange={this.handleChange('location')}
                                                required={false}
                                                className="googlepin"
                                            />
                                            {this.state.errors.location &&
                                                <p className="error-message">{this.state.errors.location}</p>}
                                        </div>
                                        <div className="col-6 PriceNight">
                                            <DateTime fromDate={new Date(this.props.event.start_date)}
                                                toDate={new Date(this.props.event.end_date)}
                                                className="date" value={this.state.date}
                                                onChange={this.handleDateChange('date')}
                                                label={t('PROGRAM_DATE')} required={true} />
                                            {this.state.errors.date &&
                                                <p className="error-message">{this.state.errors.date}</p>}
                                            {this.validator.message('date', this.state.date, 'required')}
                                        </div>

                                    </div>
                                    <div className="row d-flex">
                                        <div className="col-6 PriceNight">
                                            <Timepicker
                                                label={t('PROGRAM_START_TIME')}
                                                value={this.state.start_time}
                                                onChange={this.handleTimeChange.bind(this)}
                                                stateName='start_time'
                                                validateName='start_time_validate'
                                                required={true}
                                                seconds={true}
                                            />
                                            {this.state.errors.start_time &&
                                                <p className="error-message">{this.state.errors.start_time}</p>}
                                            {this.validator.message('start_time', this.state.start_time, 'required')}
                                        </div>
                                        <div className="col-6 PriceNight">
                                            <Timepicker
                                                label={t('PROGRAM_END_TIME')}
                                                value={this.state.end_time}
                                                onChange={this.handleTimeChange.bind(this)}
                                                stateName='end_time'
                                                validateName='end_time_validate'
                                                required={true}
                                                seconds={true}
                                            />
                                            {this.state.errors.end_time &&
                                                <p className="error-message">{this.state.errors.end_time}</p>}
                                            {this.validator.message('end_time', this.state.end_time, 'required')}
                                        </div>
                                    </div>
                                </div>
                                <div className="col-6 PriceNight">
                                    <TextArea
                                        type='textarea'
                                        label={t('PROGRAM_DESCRIPTION')}
                                        name='description'
                                        value={this.state.description}
                                        onChange={this.handleChange('description')}
                                        required={false}
                                        height="169px"
                                    />
                                    {this.state.errors.description &&
                                        <p className="error-message">{this.state.errors.description}</p>}
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
    const { alert, event } = state;
    return {
        alert, event
    };
}

export default connect(mapStateToProps)(withRouter(withTranslation()(FormWidget)));