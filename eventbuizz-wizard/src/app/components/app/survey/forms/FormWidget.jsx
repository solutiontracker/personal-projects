import React, { Component } from 'react';
import { ReactSVG } from 'react-svg';
import Input from '@/app/forms/Input';
import { SurveyService } from "services/survey/survey-service";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { withRouter } from 'react-router-dom';
import { connect } from 'react-redux';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FormWidget extends Component {
    constructor(props) {
        super(props);
        this.state = {
            id: '',
            name: '',
            display: true,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            change: false
        }
    }

    componentDidMount() {
        const { editdata } = this.props;
        if (this.props.editdata) {
            this.setState({
                id: editdata.id,
                name: editdata.info.name,
            });
        }
    }

    handleChange = input => e => {
        this.setState({
            [input]: e.target.value,
            change: true
        })
    }

    saveData = (e) => {
        this.setState({ isLoader: true });
        if (this.props.editdata) {
            const request_data = this.state;
            const id = this.state.id;
            SurveyService.update(request_data, 'survey', id)
                .then(
                    response => {
                        if (response.success) {
                            this.setState({
                                message: response.message,
                                success: true,
                                isLoader: false,
                                errors: {},
                                change: false
                            });
                            this.props.listing(1, true);
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
        } else {
            const request_data = this.state;
            SurveyService.create(request_data, 'survey')
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

                            setTimeout(() => { this.props.history.push(`/event/manage/survey/questions/${response.data.id}`) }, 1000)

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
    }

    render() {
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
                            <h4 className="component-heading"><ReactSVG className="icons" wrapper="span" src={require('img/ico-clipboard.svg')} /> {(this.props.editElement ? t('ES_EDIT') : t('ES_CREATE'))}</h4>
                            <div className="row d-flex">
                                <Input
                                    type='text'
                                    label={t('ES_NAME_LABEL')}
                                    value={this.state.name}
                                    name='name'
                                    onChange={this.handleChange('name')}
                                    required={true}
                                />
                                {this.state.errors.name && <p className="error-message">{this.state.errors.name}</p>}
                            </div>
                            <div className="bottom-panel-button">
                                <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-new="0" className="btn">{this.state.isLoader ?
                                    <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t('G_SAVE'))}</button>
                                <button className="btn btn-cancel" onClick={this.props.datacancel}>{t('G_CANCEL')}</button>
                            </div>
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

export default connect(mapStateToProps)(withRouter(FormWidget));