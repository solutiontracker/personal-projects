import * as React from "react";
import { withRouter } from 'react-router-dom';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import '@/app/survey/assets/css/style.css'
import socketIOClient from "socket.io-client";

const in_array = require("in_array");

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

class SurveyQuestionFullScreenProjector extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            dataContainer: [],
            question: [],
            questionTotalResponses: [],
            setting: [],
            template: [],
            question_id: this.props.match.params.id,
            event_id: this.props.match.params.event_id,
            logo: '',
            total_vote_label: '',

            //errors & loading
            preLoader: true,
            message: false,
            success: true,
            errors: {}
        }
    }

    componentDidMount() {
        this._isMounted = true;
        this.fetchData();
        this.socket();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    fetchData(loader = false) {
        this.setState({ preLoader: (!loader ? true : false) });
        service.get(`${process.env.REACT_APP_URL}/survey/question/full-screen-projector/${this.state.event_id}/${this.state.question_id}`)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            this.setState({
                                question: response.data.question,
                                questionTotalResponses: response.data.questionTotalResponses,
                                setting: response.data.setting,
                                template: response.data.template,
                                logo: response.data.logo,
                                total_vote_label: response.data.total_vote_label,
                                preLoader: false
                            });
                        }
                    }
                },
                error => { }
            );
    }

    socket() {
        let _this = this;
        socket.off(`event-buizz:full_screen_survey_block_${this.props.event.id}_${this.state.question_id}`);
        socket.on(`event-buizz:full_screen_survey_block_${this.props.event.id}_${this.state.question_id}`, function (data) {
            if (data.html) {
                _this.fetchData(true);
            };
        });
    }

    render() {
        console.log(this.state);
        return (
            <Translation>
                {
                    t =>
                        <React.Fragment>
                            {this.state.preLoader &&
                                <Loader />
                            }
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div id="pageWrapper">
                                        <div id="contentWrapper">
                                            <div id="rightColumn" style={{ width: "920px" }}>
                                                {
                                                    (() => {
                                                        if (in_array(this.state.question.question_type, ['number', 'date', 'date_time']))
                                                            return this.state.question.detail.question;
                                                        else if(in_array(this.state.question.question_type, ['matrix']))
                                                            return t('PROJECTOR_MATRIX_QUESTION_NOT_SUPPORTED');
                                                        else
                                                            return (
                                                                <React.Fragment>
                                                                    <div className="header_logo_poll" style={{ padding: "25px 20px 25px 0px" }}>
                                                                        <img src={this.state.logo} alt="" width="250" />
                                                                    </div>
                                                                    <div className="pageHeading">{this.state.question.detail.question}</div>
                                                                    <div className="question">
                                                                        {!in_array(this.state.question.question_type, ['open', 'number', 'date', 'date_time']) && (
                                                                            <React.Fragment>
                                                                                <div className="cls">&nbsp;</div>
                                                                                <div className="listingWrapper" style={{ paddingLeft: "5px; float: left; width: 100%" }}>
                                                                                    {this.state.question.answer.map((row, key) => {
                                                                                        return (
                                                                                            <Translation key={key}>
                                                                                                {
                                                                                                    t =>
                                                                                                        <React.Fragment key={key}>
                                                                                                            <span className="listingWrapper-span">{row.result_count}</span>
                                                                                                            <div className="listingWrapper-div">
                                                                                                                {Number(row.correct) === 1 ? (
                                                                                                                    <span>{row.answer}</span>
                                                                                                                ) : (
                                                                                                                        row.answer
                                                                                                                    )}
                                                                                                            </div>
                                                                                                            <div style={{ clear: 'both' }}></div>
                                                                                                        </React.Fragment>
                                                                                                }
                                                                                            </Translation>
                                                                                        )
                                                                                    })}
                                                                                    <div className="vote-attendee-count">
                                                                                        <span className="vote-attendee-count-span"> <b>
                                                                                        </b> {this.state.total_vote_label}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </React.Fragment>
                                                                        )}
                                                                        {in_array(this.state.question.question_type, ['open']) && (
                                                                            <div className="listingWrapper">
                                                                                {this.state.question.answer.map((row, key) => {
                                                                                    return (
                                                                                        <Translation key={key}>
                                                                                            {
                                                                                                t =>
                                                                                                    <React.Fragment key={key}>
                                                                                                        <div className="people_list myevent" style={{ paddingBottom: "20px" }}>
                                                                                                            <div className="people_name" style={{ lineHeight: "30px", fontSize: "22px" }}>
                                                                                                                {row.answer}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </React.Fragment>
                                                                                            }
                                                                                        </Translation>
                                                                                    )
                                                                                })}
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                </React.Fragment>
                                                            )
                                                    })()
                                                }

                                            </div>
                                            <div style={{ backgroundImage: 'url(' + (this.state.template && this.state.template.preview_image !== "none.png" && (in_array(this.state.template.position, ['custom_right', 'custom_bottom']) ? `${process.env.REACT_APP_EVENTCENTER_URL}/assets/poll_settings/${this.state.template.name}` : `${process.env.REACT_APP_EVENTCENTER_URL}/assets/PollSurveySettings/${this.state.template.preview_image}`)) + ')' }} className={this.state.template && this.state.template.position.includes("right") ? 'bg_right' : ''}></div>
                                            <div style={{ clear: "both" }}></div>
                                        </div>
                                    </div>
                                    <div style={{ backgroundImage: 'url(' + (this.state.template && this.state.template.preview_image !== "none.png" && (in_array(this.state.template.position, ['custom_right', 'custom_bottom']) ? `${process.env.REACT_APP_EVENTCENTER_URL}/assets/poll_settings/${this.state.template.name}` : `${process.env.REACT_APP_EVENTCENTER_URL}/assets/PollSurveySettings/${this.state.template.preview_image}`)) + ')' }} className={this.state.template && this.state.template.position.includes("bottom") ? 'bg_bottom' : ''}></div>
                                </React.Fragment>
                            )}
                        </React.Fragment>
                }
            </Translation>
        )
    }
}

function mapStateToProps(state) {
    const { event, update } = state;
    return {
        event, update
    };
}

export default connect(mapStateToProps)(withRouter(SurveyQuestionFullScreenProjector));
