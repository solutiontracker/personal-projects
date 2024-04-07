import React, { Component } from 'react';
import Img from 'react-image';
import DropDown from '@/app/forms/DropDown';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { connect } from 'react-redux';

class SubRegistrationQuestionResults extends Component {

    constructor(props) {
        super(props);
        this.state = {
						response: [],
					questions: [],
            question_id: this.props.question_id,
            answer_id: this.props.answer_id,
            sort_by: 'first_name',
            order_by: 'ASC',

            //errors & loading
            preLoader: true,
        }

        this.onSorting = this.onSorting.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        this.results();
				document.body.addEventListener('click', this.removePopup.bind(this));
	}
	removePopup = e => {
		if (e.target.className !== 'btn active') {
			const items = document.querySelectorAll(".parctical-button-panel .btn");
			for (let i = 0; i < items.length; i++) {
				const element = items[i];
				element.classList.remove("active");
			}
		}
	}
    componentWillUnmount() {
        this._isMounted = false;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.question_id !== this.state.question_id) {
            this.results();
        } else if (this.state.order_by !== prevState.order_by || this.state.sort_by !== prevState.sort_by) {
            this.results();
        }
    }
    results = (loader = false) => {
        this.setState({ preLoader: (!loader ? true : false) });
        service.post(`${process.env.REACT_APP_URL}/sub-registration/question/results`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    response: response.data.data,
                                    preLoader: false,
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    handleChange = (input) => e => {
        this.setState({
            [input]: e.value
        })
    }
	handleDropdown = e => {
		e.stopPropagation();
		const items = document.querySelectorAll(".parctical-button-panel .btn");
		for (let i = 0; i < items.length; i++) {
			const element = items[i];
			if (element.classList === e.target.classList) {
				e.target.classList.toggle("active");
			} else {
				element.classList.remove("active");
			}
		}
	};
    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    render() {
			this.getSelectedLabel = (item, id) => {
				if (item && item.length > 0 && id) {
					let obj = item.find(o => o.id.toString() === id.toString());
					return (obj ? obj.name : '');
				}
			}
        return (
            <Translation>
                {t => (
                    <div className="data-popup-charts wrapper-import-file-wrapper">
                        <div className="wrapper-import-file popup-subregistration">
                            <div style={{ marginBottom: 0 }} className="header-popup-subregistration">
                                <div className="row d-flex">
                                    <div className="col-6 d-flex align-items-center">
                                        <div className="heading-area-popup">
                                            <h2>{t('DSB_SUB_REGISTRATION')}</h2>
                                            <p>{this.props.event.name}</p>
                                        </div>
                                    </div>
																		<div className="col-6">
																			<div className="new-panel-buttons">
																				<button>
																					<Img src={require('img/ico-plus-lg.svg')} />
																				</button>
																				<button>
																					<Img src={require('img/ico-mail.svg')} />
																				</button>
																				<button>
																					<Img src={require('img/ico-excel.svg')} />
																				</button>
																				<button>
																					<Img src={require('img/ico-pdf.svg')} />
																				</button>
																			</div>
																		</div>
                                </div>
																<div className="new-header">
																	<div className="row d-flex">
																		<div className="col-3">
																			<input name="query" type="text" placeholder="Search" value="" />
																		</div>
																		<div className="col-3">
																			<div className="right-top-header">
																				<label className="label-select-alt">
																					<DropDown
																						label={t("DSB_FILTER_BY")}
																						listitems={this.state.questions}
																						selectedlabel={this.getSelectedLabel(this.state.questions, this.state.question_id)}
																						onChange={this.handleChange('question_id')}
																						required={true}
																					/>
																				</label>
																			</div>
																		</div>
																	</div>
																</div>
                            </div>
                            {this.state.preLoader &&
                                <Loader />
                            }
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div className="wrapper-module">
                                        <div className="top-section-wrapper row d-flex">
                                            <div className="col-6">
																						<div className="popup-list-box">
																							{this.state.response.question && (
																								<p><strong>{t('DSB_QUESTION')}:</strong>{this.state.response.question}?</p>
																							)}
																							{this.state.response.question && (
																								<p><strong>{t('DSB_ANSWER')}:</strong><span>{this.state.response.answer}</span></p>
																							)}
																						</div>
                                            </div>
                                            <div className="col-6">
																							<div style={{marginBottom: '45px'}} className="panel-right-table d-flex justify-content-end">
																								<div className="parctical-button-panel">
																									<div className="dropdown">
																										<button onClick={this.handleDropdown.bind(this)} className="btn" style={{ minWidth: '54px' }}>10<i className="material-icons">keyboard_arrow_down</i></button>
																										<div className="dropdown-menu"><button className="dropdown-item">20</button><button className="dropdown-item">50</button><button className="dropdown-item">500</button><button className="dropdown-item">1000</button></div>
																									</div>
																								</div>
																							</div>
                                                <p className="text-right text-respondents">
                                                    {t('DSB_RESPONDENTS')} <a href="#!">{this.state.response.total_results}</a>
                                                </p>
                                            </div>
                                        </div>
                                        {this.state.response.results && this.state.response.results.length > 0 && (
                                            <div className="hotel-management-records attendee-records-template">
                                                <header className="header-records row d-flex">
                                                    <div className="col-2">
                                                        <strong>{t('ATTENDEE_COMPANY_NAME')}</strong>
                                                        <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="company_name" onClick={this.onSorting} className="material-icons">
                                                            {(this.state.order_by === "ASC" && this.state.sort_by === "company_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "company_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                        </i>
                                                    </div>
                                                    <div className="col-2">
                                                        <strong>{t('ATTENDEE_FIRST_NAME')}</strong>
                                                        <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                                                            {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                        </i>
                                                    </div>
                                                    <div className="col-2">
                                                        <strong>{t('ATTENDEE_LAST_NAME')}</strong>
                                                        <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="last_name" onClick={this.onSorting} className="material-icons">
                                                            {(this.state.order_by === "ASC" && this.state.sort_by === "last_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "last_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                        </i>
                                                    </div>
                                                    <div className="col-2">
                                                        <strong>{t('ATTENDEE_EMAIL')}</strong>
                                                        <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                            {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                        </i>
                                                    </div>
                                                    <div className="col-2">
                                                        <strong>{t('DSB_ANSWER')}</strong>
                                                        <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="answer" onClick={this.onSorting} className="material-icons">
                                                            {(this.state.order_by === "ASC" && this.state.sort_by === "answer" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "answer" ? "keyboard_arrow_up" : "unfold_more"))}
                                                        </i>
                                                    </div>
                                                </header>
                                                {this.state.response.results && this.state.response.results.map((row, k) => {
                                                    return (
                                                        <div className="row d-flex" key={row.id}>
                                                            <div className="col-2">
                                                                <p>{row.company_name}</p>
                                                            </div>
                                                            <div className="col-2">
                                                                <p>{row.first_name}</p>
                                                            </div>
                                                            <div className="col-2">
                                                                <p>{row.last_name}</p>
                                                            </div>
                                                            <div className="col-2">
                                                                <p>{row.email}</p>
                                                            </div>
                                                            <div className="col-2">
                                                                <p>{row.answer}</p>
                                                            </div>
                                                        </div>
                                                    )
                                                })
                                                }
                                            </div>
                                        )}
                                    </div>
                                    <div className="bottom-component-panel bottom-panel-import">
                                        <button onClick={this.props.close(false)} className="btn btn-import">Close</button>
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                    </div>
                )}
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(SubRegistrationQuestionResults);