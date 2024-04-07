import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import AnswerWidget from '@/app/sub-registration/forms/AnswerWidget';
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";


class FormWidget extends Component {
    constructor(props) {
        super(props);
        this.state = {
            question: (this.props.editData ? this.props.editData.question : ''),
            question_type: (this.props.editData ? this.props.editData.question_type : 'single'),
            required_question: (this.props.editData ? this.props.editData.required_question : '0'),
            enable_comments: (this.props.editData ? this.props.editData.enable_comments : '0'),
            max_options: (this.props.editData ? this.props.editData.max_options : '0'),
            min_options: (this.props.editData ? this.props.editData.min_options : '0'),
            answer: (this.props.editData && this.props.editData.answer ? this.props.editData.answer : [{
                value: '',
                correct: 0,
            }]),
            column: (this.props.editData && this.props.editData.matrix ? this.props.editData.matrix : [{
                value: '',
                correct: 0
            }]),

            change: false
        }

    }

    handleChange = input => e => {
        if (input === 'question_type') {
            this.setState({
                [input]: e.value,
                max_options: (e.value !== 'multiple' ? '' : this.state.max_options),
                change: true
            });
        } else {
            this.setState({
                [input]: e.target.value,
                change: true
            });
        }
    };

    updateFlag = input => e => {
        e.preventDefault();
        this.setState({
            [input]: this.state[input] === '1' ? '0' : '1',
            change: true
        })
    };

    handleData = input => {
        this.setState({
            answer: input,
            change: true
        })
    };

    handleDataColumn = input => {
        this.setState({
            column: input,
            change: true
        });
    };

    getSelectedLabel = (item, id) => {
        if (item && item.length > 0 && id) {
            let obj = item.find(o => o.id.toString() === id.toString());
            return obj.name;
        }
    }

    render() {

        return (
            <Translation>
                {
                    t =>
                        <div className={`option-wrapper subregistrationwrapper ${this.props.editData ? 'isGray' : ''}`}>
                            <ConfirmationModal update={this.state.change} />
                            <h5 className="section-title">{t('SR_ADD_CUSTOM_QUESTION')}</h5>
                            <div className="row">
                                <div className="col-6">
                                    <Input
                                        type='text'
                                        label={t('SR_QUESTION')}
                                        name='question'
                                        value={this.state.question}
                                        onChange={this.handleChange('question')}
                                        required={true}
                                    />
                                    {this.props.errors && this.props.errors.question &&
                                        <p className="error-message">{this.props.errors.question}</p>}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-6">
                                    <DropDown
                                        label={t('SR_QUESTION_TYPE')}
                                        listitems={[{ name: t('SR_OPTION_1'), id: "single" },
                                        { name: t('SR_OPTION_2'), id: 'multiple' },
                                        { name: t('SR_OPTION_3'), id: 'open' },
                                        { name: t('SR_OPTION_4'), id: 'number' },
                                        { name: t('SR_OPTION_5'), id: 'date' },
                                        { name: t('SR_OPTION_6'), id: 'date_time' },
                                        { name: t('ES_OPTION_8'), id: 'matrix' },
                                        { name: t('SR_OPTION_7'), id: 'dropdown' }]}
                                        selected={this.state.question_type}
                                        selectedlabel={this.getSelectedLabel([{ name: t('SR_OPTION_1'), id: "single" },
                                        { name: t('SR_OPTION_2'), id: 'multiple' },
                                        { name: t('SR_OPTION_3'), id: 'open' },
                                        { name: t('SR_OPTION_4'), id: 'number' },
                                        { name: t('SR_OPTION_5'), id: 'date' },
                                        { name: t('SR_OPTION_6'), id: 'date_time' },
                                        { name: t('ES_OPTION_8'), id: 'matrix' },
                                        { name: t('SR_OPTION_7'), id: 'dropdown' }], this.state.question_type)}
                                        onChange={this.handleChange('question_type')}
                                        required={true}
                                        isDisabled={this.props.editData && (this.props.editData.q_responses !== 0 && this.props.editData.q_responses !== undefined) ? true : false}
                                    />
                                    {this.props.errors && this.props.errors.question_type &&
                                        <p className="error-message">{this.props.errors.question_type}</p>}
                                </div>
                            </div>
                            {this.state.question_type === "single" || this.state.question_type === "multiple" || this.state.question_type === "dropdown" ? (
                                <div className="row">
                                    <div className="col-8">
                                        <AnswerWidget
                                            answer={this.state.answer}
                                            question_type={this.state.question_type}
                                            onChange={this.handleData}
                                        />
                                        {this.props.errors && this.props.errors.answer &&
                                            <p className="error-message">{this.props.errors.answer[0]}</p>}
                                    </div>
                                </div>
                            ) : ''}
                            {this.state.question_type === "matrix" ? (
                                <div className="row">
                                    <div className="col-12">
                                        <AnswerWidget
                                            answer={this.state.answer}
                                            question_type={this.state.question_type}
                                            onChange={this.handleData}
                                        />
                                        {this.props.errors && this.props.errors.answer &&
                                            <p className="error-message question-type">{this.props.errors.answer[0]}</p>}
                                        <AnswerWidget
                                            answer={this.state.column}
                                            question_type={this.state.question_type}
                                            onChange={this.handleDataColumn}
                                            column={true}
                                        />
                                        {this.props.errors && this.props.errors.column &&
                                            <p className="error-message question-type">{this.props.errors.column[0]}</p>}
                                    </div>
                                </div>
                            ) : ''}
                            <div className="row">
                                <div className="col-8">
                                    {this.state.question_type === "multiple" && (
                                        <ul style={{ paddingLeft: '24px', margin: '2px 0 6px 0' }} className="d-inline-block question-other-option">

                                            <React.Fragment>

                                                <li style={{ width: '100%', marginTop: '13px', maxWidth: '358px' }} className="d-inline-block">
                                                    <Input
                                                        type='text'
                                                        label={t('ES_MIN_SELECTED_OPTION')}
                                                        name='min_options'
                                                        value={this.state.min_options}
                                                        onChange={this.handleChange('min_options')}
                                                        required={true}
                                                    />
                                                </li>

                                                <li style={{ width: '100%', marginTop: '13px', maxWidth: '358px' }} className="d-inline-block">
                                                    <Input
                                                        type='text'
                                                        label={t('ES_MAX_SELECTED_OPTION')}
                                                        name='max_options'
                                                        value={this.state.max_options}
                                                        onChange={this.handleChange('max_options')}
                                                        required={true}
                                                    />
                                                </li>
                                            </React.Fragment>

                                        </ul>
                                    )}
                                    {this.props.errors && this.props.errors.max_options &&
                                        <p className="error-message question-type">{this.props.errors.max_options[0]}</p>}

                                    {this.props.errors && this.props.errors.min_options &&
                                        <p className="error-message question-type">{this.props.errors.min_options[0]}</p>}
                                    <ul style={this.state.question_type !== "matrix" ? { paddingLeft: '24px', marginBottom: '10px' } : { paddingLeft: '24px', marginBottom: '10px' }} className="d-inline-block question-other-option">
                                        <li className="d-inline-block"><label
                                            onClick={this.updateFlag('required_question')}><i className="material-icons">
                                                {this.state.required_question === '1' ? 'check_box' : 'check_box_outline_blank'}
                                            </i>{t('SR_REQUIRED_QUESTION')}</label></li>
                                        <li className="d-inline-block"><label
                                            onClick={this.updateFlag('enable_comments')}><i className="material-icons">
                                                {this.state.enable_comments === '1' ? 'check_box' : 'check_box_outline_blank'}
                                            </i>{t('SR_ENABLE_COMMENTS')}</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div style={{ paddingLeft: '40px' }} className="bottom-panel-button">
                                <button disabled={this.props.isLoader ? true : false} onClick={() => this.props.questionSave(this.state, this.props.editIndex, "save")}
                                    className="btn">{this.props.isLoader === "save" ?
                                        <span className="spinner-border spinner-border-sm"></span> : (this.props.editData ? t('G_SAVE') : t('G_SAVE'))}</button>
                                {!this.props.editData && (
                                    <button disabled={this.props.isLoader ? true : false} onClick={() => this.props.questionSave(this.state, this.props.editIndex, "save-new")} className="btn save-new">{this.props.isLoader === "save-new" ?
                                        <span className="spinner-border spinner-border-sm"></span> : t("G_SAVE_AND_ADD_ANOTHER")}</button>
                                )}
                                <button onClick={this.props.cancelQuestionElement} className="btn btn-cancel">{t('G_CANCEL')}
                                </button>
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