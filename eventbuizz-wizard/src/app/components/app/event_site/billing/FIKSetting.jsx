import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import { AuthAction } from 'actions/auth/auth-action';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import 'sass/billing.scss';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FIKSetting extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			type: 'fik-setting',
			billing_type: 0,
			invoice_type: 0,
			debitor_number: 0,

			//errors & loading
			message: false,
			success: true,
			errors: {},
			isLoader: false,
			preLoader: false,

			prev: "/event_site/billing-module/ean-invoice",
			next: "/event_site/billing-module/items",

			change: false
		}
	}

	componentDidMount() {
		this._isMounted = true;
		this.loadSettingsData();
	}

	loadSettingsData = () => {
		this._isMounted = true;
		this.setState({ preLoader: true });
		service.get(`${process.env.REACT_APP_URL}/eventsite/billing/fik-settings`)
			.then(
				response => {
					if (response.success) {
						if (response.data) {
							if (this._isMounted) {
								this.setState({
									billing_type: response.data.payment_setting.billing_type,
									invoice_type: response.data.payment_setting.invoice_type,
									debitor_number: response.data.payment_setting.debitor_number,
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

	handleChange = (input, item, type) => e => {
		var value = (type === "dropdown" ? e.value : e.target.value);
		if (item && type) {
			const { dispatch } = this.props;
			const validate = dispatch(AuthAction.formValdiation(type, value));
			if (validate.status) {
				this.setState({
					[input]: value,
					[item]: 'success',
					change: true
				})
			} else {
				this.setState({
					[input]: value,
					[item]: 'error',
					change: true
				})
			}
		} else {
			this.setState({
				[input]: value,
				change: true
			})
		}
	}

	handleClick = (input, value) => e => {
		this.setState({
			[input]: value,
			change: true
		});
	};

	saveData = e => {
		e.preventDefault();
		const type = e.target.getAttribute('data-type');
		this.setState({ isLoader: type });
		service.put(`${process.env.REACT_APP_URL}/eventsite/billing/fik-settings`, this.state)
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
						if (type === "save-next") this.props.history.push(this.state.next);
					} else {
						this.setState({
							message: response.message,
							success: false,
							isLoader: false,
							errors: response.errors
						});
					}
				},
				error => { }
			);
	}

	render() {
		return (
			<Translation>
				{
					t =>
						<React.Fragment>
							<div className="wrapper-content third-step main-billing-page ">
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
										<div style={{ height: '100%' }}>
											<div className="row header-section">
												<div className="col-6">
													<h1 className="section-title">{t("BILLING_FIK_SETTING_MAIN_HEADING")}</h1>
													{Number(this.state.billing_type) !== 1 && (
														<p>{t("BILLING_FIK_SETTING_MAIN_SUB_HEADING")}</p>
													)}
												</div>
											</div>
											<div className="row">
												{Number(this.state.billing_type) === 1 && (
													<div className="col-6">
														<Input
															type='text'
															label={t("BILLING_FIK_SETTING_DEBITOR_NUMBER")}
															name='name'
															required={true}
															value={this.state.debitor_number ? this.state.debitor_number : ''}
															onChange={this.handleChange('debitor_number', '', 'text')}
														/>
														{this.state.errors.debitor_number && <p className="error-message">{this.state.errors.debitor_number}</p>}
														<div className="fik-select-types">
															<h4 className="component-heading">{t("BILLING_FIK_SETTING_SELECT_TYPE")}</h4>
															<div>
																<span onClick={this.handleClick('invoice_type', 0)}>
																	71<i className="material-icons">{Number(this.state.invoice_type) === 0 ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
																</span>
																<span onClick={this.handleClick('invoice_type', 1)}>
																	73<i className="material-icons">{Number(this.state.invoice_type) === 1 ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
																</span>
															</div>
														</div>
													</div>
												)}
											</div>
										</div>
										<div className="bottom-component-panel clearfix">
											<NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
												<i className='material-icons'>remove_red_eye</i>
												{t("G_PREVIEW")}
											</NavLink>
											{this.state.prev !== undefined && (
												<NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
													keyboard_backspace</span></NavLink>
											)}
											<button style={{ minWidth: '124px' }} data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.saveData}>{this.state.isLoader === "save" ?
												<span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
											</button>
											<button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.saveData}>{this.state.isLoader === "save-next" ?
												<span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
											</button>
										</div>
									</React.Fragment>
								)}
							</div>
						</React.Fragment>
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

export default connect(mapStateToProps)(FIKSetting);