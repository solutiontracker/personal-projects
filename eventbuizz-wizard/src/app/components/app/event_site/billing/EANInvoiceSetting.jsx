import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import { AuthAction } from 'actions/auth/auth-action';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import 'sass/billing.scss';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class EANInvoiceSetting extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			type: 'ean-setting',
			billing_type: 0,
			auto_invoice: 0,
			account_number: "",
			bank_name: "",
			payment_date: "",
			paymentTerms: [],

			//errors & loading
			message: false,
			success: true,
			errors: {},
			isLoader: false,
			preLoader: false,

			prev: "/event_site/billing-module/payment-providers",
			next: (this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.eventsite_billing_fik) === 1 ? "/event_site/billing-module/fik-setting" : "/event_site/billing-module/items"),

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
		service.get(`${process.env.REACT_APP_URL}/eventsite/billing/ean-settings`)
			.then(
				response => {
					if (response.success) {
						if (response.data) {
							if (this._isMounted) {
								this.setState({
									paymentTerms: response.data.paymentTerms,
									billing_type: response.data.payment_setting.billing_type,
									auto_invoice: response.data.payment_setting.auto_invoice,
									account_number: response.data.payment_setting.account_number,
									bank_name: response.data.payment_setting.bank_name,
									payment_date: response.data.payment_setting.payment_date,
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

	updateFlag = input => e => {
		this.setState({
			[input]: this.state[input] === 1 ? 0 : 1,
			change: true
		});
	};

	saveData = e => {
		e.preventDefault();
		const type = e.target.getAttribute('data-type');
		this.setState({ isLoader: type });
		service.put(`${process.env.REACT_APP_URL}/eventsite/billing/ean-settings`, this.state)
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
						<React.Fragment>
							<div className="wrapper-content third-step main-billing-page">
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
													<h1 className="section-title">{t("BILLING_PAYMENT_EAN_INVOICE_MAIN_HEADING")}</h1>
													{Number(this.state.billing_type) !== 1 && (
														<p>{t("BILLING_PAYMENT_EAN_INVOICE_SUB_HEADING")}</p>
													)}
												</div>
											</div>
											<div className="row">
												{Number(this.state.billing_type) === 1 && (
													<div className="col-6">
														<div className="checkbox-row checkbox-flex-style">
															<p>
																{t("BILLING_PAYMENT_EAN_INVOICE_AUTO_INVOICE_LABEL")}
															</p>
															<label className="custom-checkbox-toggle"><input onChange={this.updateFlag('auto_invoice')}
																defaultChecked={this.state.auto_invoice} type="checkbox" disabled={this.state.active_orders > 0 ? true : false} /><span></span></label>
														</div>
														<Input
															type='text'
															label={t("BILLING_PAYMENT_EAN_INVOICE_ACCOUNT_NUMBER")}
															name='name'
															required={false}
															value={this.state.account_number}
															onChange={this.handleChange('account_number', '', 'text')}
														/>
														<Input
															type='text'
															label={t("BILLING_PAYMENT_EAN_INVOICE_BANK_NAME")}
															name='name'
															required={false}
															value={this.state.bank_name}
															onChange={this.handleChange('bank_name', '', 'text')}
														/>
														<DropDown
															label={t("BILLING_PAYMENT_EAN_INVOICE_PAYMENT_TERMS")}
															listitems={this.state.paymentTerms}
															required={false}
															selected={this.state.payment_date}
															isSearchable='false'
															selectedlabel={this.getSelectedLabel(this.state.paymentTerms, this.state.payment_date)}
															onChange={this.handleChange('payment_date', '', 'dropdown')}
														/>
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

export default connect(mapStateToProps)(EANInvoiceSetting);