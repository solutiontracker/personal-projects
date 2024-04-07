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
import '@/app/event_site/billing/style/style.css';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class PaymentProvider extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			type: 'payment-providers',
			billing_merchant_type: '0',
			eventsite_merchant_id: '',
			eventsite_order_prefix: '',
			billing_yourpay_language: '',
			swed_bank_password: '',
			swed_bank_language: '',
			swed_bank_region: '',
			SecretKey: '',
			bambora_secret_key: '',
			mistertango_markets: '',
			qp_agreement_id: '',
			qp_secret_key: '',
			qp_auto_capture: '',
			wc_customer_id: '',
			wc_shop_id: '',
			wc_secret: '',
			stripe_api_key: '',
			stripe_secret_key: '',
			merchantTypes: [],
			markets: [],
			billingLanguages: [],
			swed_bank_regions: [],
			swed_langauges: [],
			payment_cards: [],

			// Valdiation
			validate_billing_merchant_type: 'success',
			validate_eventsite_merchant_id: 'success',
			validate_swed_bank_password: 'success',
			validate_qp_agreement_id: 'success',
			validate_qp_secret_key: 'success',
			validate_wc_customer_id: 'success',
			validate_wc_shop_id: 'success',
			validate_wc_secret: 'success',
			validate_billing_yourpay_language: 'success',
			validate_stripe_api_key: 'success',
			validate_stripe_secret_key: 'success',
			validate_bambora_secret_key: 'success',

			//errors & loading
			message: false,
			success: true,
			errors: {},
			isLoader: false,
			preLoader: false,

			prev: "/event_site/billing-module/payment-methods",
			next: (this.props.event && this.props.event.eventsite_secion_fields && Number(this.props.event.eventsite_secion_fields.company_detail.company_public_payment.status) === 1 ? "/event_site/billing-module/ean-invoice" : (this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.eventsite_billing_fik) === 1 ? "/event_site/billing-module/fik-setting" : "/event_site/billing-module/items")),

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
		service.get(`${process.env.REACT_APP_URL}/eventsite/billing/payment-providers`)
			.then(
				response => {
					if (response.success) {
						if (response.data) {
							if (this._isMounted) {
								this.setState({
									merchantTypes: response.data.merchantTypes,
									markets: response.data.markets,
									mistertango_markets: response.data.mistertango_markets,
									billingLanguages: response.data.billingLanguages,
									swed_bank_regions: response.data.swed_bank_regions,
									swed_bank_region: response.data.swed_bank_region,
									swed_langauges: response.data.swed_langauges,
									swed_bank_language: response.data.swed_bank_language,
									payment_cards: response.data.payment_cards,
									billing_merchant_type: response.data.payment_setting.billing_merchant_type.toString(),
									eventsite_merchant_id: response.data.payment_setting.eventsite_merchant_id,
									eventsite_order_prefix: response.data.payment_setting.eventsite_order_prefix,
									billing_yourpay_language: response.data.payment_setting.billing_yourpay_language,
									swed_bank_password: response.data.payment_setting.swed_bank_password,
									SecretKey: response.data.payment_setting.SecretKey,
									bambora_secret_key: response.data.payment_setting.bambora_secret_key,
									qp_agreement_id: response.data.payment_setting.qp_agreement_id,
									qp_secret_key: response.data.payment_setting.qp_secret_key,
									qp_auto_capture: response.data.payment_setting.qp_auto_capture,
									wc_customer_id: response.data.payment_setting.wc_customer_id,
									wc_shop_id: response.data.payment_setting.wc_shop_id,
									wc_secret: response.data.payment_setting.wc_secret,
									stripe_api_key: response.data.payment_setting.stripe_api_key,
									stripe_secret_key: response.data.payment_setting.stripe_secret_key,

									//validations
									validate_billing_merchant_type: (response.data.payment_setting.billing_merchant_type.toString() ? "success" : "error"),
									validate_eventsite_merchant_id: (response.data.payment_setting.eventsite_merchant_id ? "success" : "error"),
									validate_swed_bank_password: (response.data.payment_setting.swed_bank_password ? "success" : "error"),
									validate_qp_agreement_id: (response.data.payment_setting.qp_agreement_id ? "success" : "error"),
									validate_qp_secret_key: (response.data.payment_setting.qp_secret_key ? "success" : "error"),
									validate_wc_customer_id: (response.data.payment_setting.wc_customer_id ? "success" : "error"),
									validate_wc_shop_id: (response.data.payment_setting.wc_shop_id ? "success" : "error"),
									validate_wc_secret: (response.data.payment_setting.wc_secret ? "success" : "error"),
									validate_billing_yourpay_language: (response.data.payment_setting.billing_yourpay_language ? "success" : "error"),
									validate_bambora_secret_key: (response.data.payment_setting.bambora_secret_key ? "success" : "error"),
									validate_stripe_api_key: (response.data.payment_setting.stripe_api_key ? "success" : "error"),
									validate_stripe_secret_key: (response.data.payment_setting.stripe_secret_key ? "success" : "error"),
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

	mistertangoMarketsChange = (mistertango_markets) => {
		this.setState({
			mistertango_markets: mistertango_markets,
			change: true
		});
	}

	swedBankRegionChange = (swed_bank_region) => {
		this.setState({
			swed_bank_region: swed_bank_region,
			change: true
		});
	}

	swedBankLanguageChange = (swed_bank_language) => {
		this.setState({
			swed_bank_language: swed_bank_language,
			change: true
		});
	}

	updateFlag = input => e => {
		this.setState({
			[input]: this.state[input] === 1 ? 0 : 1,
			change: true
		});
	};

	handleCheckChieldElement = (event) => {
		let payment_cards = this.state.payment_cards;
		payment_cards.forEach(payment_card => {
			if (payment_card.id === event.target.value)
				payment_card.isChecked = event.target.checked
		})

		this.setState({
			payment_cards: payment_cards,
			change: true
		})
	}

	saveData = e => {
		e.preventDefault();
		const type = e.target.getAttribute('data-type');
		if ((this.state.billing_merchant_type === "0" && this.state.validate_eventsite_merchant_id === 'success') || (this.state.billing_merchant_type === "2" && this.state.validate_eventsite_merchant_id === 'success' && this.state.validate_billing_yourpay_language === 'success') || (this.state.billing_merchant_type === "4" && this.state.validate_eventsite_merchant_id === 'success' && this.state.validate_swed_bank_password === 'success') || (this.state.billing_merchant_type === "5" && this.state.validate_eventsite_merchant_id === 'success' && this.state.validate_qp_agreement_id === 'success' && this.state.validate_qp_secret_key === 'success') || (this.state.billing_merchant_type === "6" && this.state.validate_wc_customer_id === 'success' && this.state.validate_wc_shop_id === 'success' && this.state.validate_wc_secret === 'success') || (this.state.billing_merchant_type === "7" && this.state.validate_stripe_api_key === 'success' && this.state.validate_stripe_secret_key === 'success') || (this.state.billing_merchant_type === "8" && this.state.validate_eventsite_merchant_id === 'success' && this.state.validate_bambora_secret_key === 'success')) {
			this.setState({ isLoader: type });
			service.put(`${process.env.REACT_APP_URL}/eventsite/billing/payment-providers`, this.state)
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
													<h1 className="section-title">{t('BILLING_PAYMENT_PROVIDER_MAIN_HEADING')}</h1>
													<p>{t('BILLING_PAYMENT_PROVIDER_SUB_HEADING')}</p>
												</div>
											</div>
											<div className="row">
												<div className="col-6">
													<DropDown
														className={this.state.validate_billing_merchant_type}
														label={t('BILLING_PAYMENT_PROVIDER_MERCHANT_TYPE')}
														listitems={this.state.merchantTypes}
														required={true}
														selected={this.state.billing_merchant_type}
														isSearchable='false'
														selectedlabel={this.getSelectedLabel(this.state.merchantTypes, this.state.billing_merchant_type)}
														onChange={this.handleChange('billing_merchant_type', 'validate_billing_merchant_type', 'dropdown')}
													/>
													{this.state.errors.billing_merchant_type && <p className="error-message">{this.state.errors.billing_merchant_type}</p>}
													{this.state.validate_billing_merchant_type === 'error' &&
														<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}

													{(this.state.billing_merchant_type === '0' || this.state.billing_merchant_type === '2' || this.state.billing_merchant_type === '4' || this.state.billing_merchant_type === '5' || this.state.billing_merchant_type === '8') && (
														<React.Fragment>
															<Input
																className={this.state.validate_eventsite_merchant_id}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_MERCHANT_ID")}
																name='name'
																value={this.state.eventsite_merchant_id}
																onChange={this.handleChange('eventsite_merchant_id', 'validate_eventsite_merchant_id', 'text')}
																required={true}
															/>
															{this.state.errors.eventsite_merchant_id && <p className="error-message">{this.state.errors.eventsite_merchant_id}</p>}
															{this.state.validate_eventsite_merchant_id === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															{this.state.billing_merchant_type === '0' && (
																<Input
																	type='text'
																	label={t("BILLING_PAYMENT_PROVIDER_DIBS_PREFIX")}
																	name='name'
																	value={this.state.eventsite_order_prefix}
																	onChange={this.handleChange('eventsite_order_prefix', '', 'text')}
																/>
															)}
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '2' && (
														<React.Fragment>
															<Input
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_MISTERTANGO_CALLBACK_URL")}
																name='name'
																value={`${process.env.REACT_APP_EVENTCENTER_URL}/event/${this.props.event.url}/detail/mister_tango_success`}
																required={false}
																readOnly
															/>
															<Input
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_MISTERTANGO_SECRET_KEY")}
																name='name'
																value={this.state.SecretKey}
																onChange={this.handleChange('SecretKey')}
																required={false}
															/>
															<DropDown
																label={t("BILLING_PAYMENT_PROVIDER_MISTERTANGO_MARKETS")}
																listitems={this.state.markets}
																selected={this.state.mistertango_markets}
																isSearchable='false'
																onChange={this.mistertangoMarketsChange}
																isMulti={true}
															/>
															<DropDown
																className={this.state.validate_billing_yourpay_language}
																label={t("BILLING_PAYMENT_PROVIDER_PAYMENT_LANGUAGE")}
																listitems={this.state.billingLanguages}
																required={true}
																selected={this.state.billing_yourpay_language}
																isSearchable='false'
																selectedlabel={this.getSelectedLabel(this.state.billingLanguages, this.state.billing_yourpay_language)}
																onChange={this.handleChange('billing_yourpay_language', 'validate_billing_yourpay_language', 'dropdown')}
															/>
															{this.state.errors.billing_yourpay_language && <p className="error-message">{this.state.errors.billing_yourpay_language}</p>}
															{this.state.validate_billing_yourpay_language === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '4' && (
														<React.Fragment>
															<Input
																className={this.state.validate_swed_bank_password}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_SWED_BANK_PASSWORD_LABEL")}
																name='name'
																required={true}
																value={this.state.swed_bank_password}
																onChange={this.handleChange('swed_bank_password', 'validate_swed_bank_password', 'text')}
															/>
															{this.state.errors.swed_bank_password && <p className="error-message">{this.state.errors.swed_bank_password}</p>}
															{this.state.validate_swed_bank_password === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<DropDown
																label={t("BILLING_PAYMENT_PROVIDER_SWED_BANK_REGION")}
																listitems={this.state.swed_bank_regions}
																selected={this.state.swed_bank_region}
																isSearchable='false'
																onChange={this.swedBankRegionChange}
																isMulti={true}
															/>
															<DropDown
																label={t("BILLING_PAYMENT_PROVIDER_SWED_BANK_LANGUAGE")}
																listitems={this.state.swed_langauges}
																selected={this.state.swed_bank_language}
																isSearchable='false'
																onChange={this.swedBankLanguageChange}
																isMulti={true}
															/>
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '8' && (
														<React.Fragment>
															<Input
																className={this.state.validate_bambora_secret_key}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_BAMBORA_SECRET_KEY")}
																name='name'
																value={this.state.bambora_secret_key}
																onChange={this.handleChange('bambora_secret_key', 'validate_bambora_secret_key', 'text')}
																required={true}
															/>
															{this.state.validate_bambora_secret_key === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '5' && (
														<React.Fragment>
															<Input
																className={this.state.validate_qp_agreement_id}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_QUICK_AGRREMENT_ID")}
																name='name'
																required={true}
																value={this.state.qp_agreement_id}
																onChange={this.handleChange('qp_agreement_id', 'validate_qp_agreement_id', 'text')}
															/>
															{this.state.errors.qp_agreement_id && <p className="error-message">{this.state.errors.qp_agreement_id}</p>}
															{this.state.validate_qp_agreement_id === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<Input
																className={this.state.validate_qp_secret_key}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_QUICK_SECRET_ID")}
																name='name'
																required={true}
																value={this.state.qp_secret_key}
																onChange={this.handleChange('qp_secret_key', 'validate_qp_secret_key', 'text')}
															/>
															{this.state.errors.qp_secret_key && <p className="error-message">{this.state.errors.qp_secret_key}</p>}
															{this.state.validate_qp_secret_key === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<div className="checkbox-row">
																<h5>{t("BILLING_PAYMENT_PROVIDER_QUICK_AUTO_CAPTURE")}</h5>
																<label className="custom-checkbox-toggle"><input onChange={this.updateFlag('qp_auto_capture')}
																	defaultChecked={this.state.qp_auto_capture} type="checkbox" /><span></span></label>
															</div>
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '6' && (
														<React.Fragment>
															<Input
																className={this.state.validate_wc_customer_id}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_WIRE_CARD_ID")}
																name='name'
																required={true}
																value={this.state.wc_customer_id}
																onChange={this.handleChange('wc_customer_id', 'validate_wc_customer_id', 'text')}
															/>
															{this.state.errors.wc_customer_id && <p className="error-message">{this.state.errors.wc_customer_id}</p>}
															{this.state.validate_wc_customer_id === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<Input
																className={this.state.validate_wc_shop_id}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_WIRE_SHOP_ID")}
																name='name'
																required={true}
																value={this.state.wc_shop_id}
																onChange={this.handleChange('wc_shop_id', 'validate_wc_shop_id', 'text')}
															/>
															{this.state.errors.wc_shop_id && <p className="error-message">{this.state.errors.wc_shop_id}</p>}
															{this.state.validate_wc_shop_id === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<Input
																className={this.state.validate_wc_secret}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_WIRE_SECRET_ID")}
																name='name'
																required={true}
																value={this.state.wc_secret}
																onChange={this.handleChange('wc_secret', 'validate_wc_secret', 'text')}
															/>
															{this.state.errors.wc_secret && <p className="error-message">{this.state.errors.wc_secret}</p>}
															{this.state.validate_wc_secret === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
														</React.Fragment>
													)}
													{this.state.billing_merchant_type === '7' && (
														<React.Fragment>
															<Input
																className={this.state.validate_stripe_api_key}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_STRIPE_API_KEY")}
																name='name'
																required={true}
																value={this.state.stripe_api_key}
																onChange={this.handleChange('stripe_api_key', 'validate_stripe_api_key', 'text')}
															/>
															{this.state.errors.stripe_api_key && <p className="error-message">{this.state.errors.stripe_api_key}</p>}
															{this.state.validate_stripe_api_key === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
															<Input
																className={this.state.validate_stripe_secret_key}
																type='text'
																label={t("BILLING_PAYMENT_PROVIDER_STRIPE_SECRET_KEY")}
																name='name'
																required={true}
																value={this.state.stripe_secret_key}
																onChange={this.handleChange('stripe_secret_key', 'validate_stripe_secret_key', 'text')}
															/>
															{this.state.errors.stripe_secret_key && <p className="error-message">{this.state.errors.stripe_secret_key}</p>}
															{this.state.validate_stripe_secret_key === 'error' &&
																<p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
														</React.Fragment>
													)}
													<div className="payment-methods-list">
														<h4>{t("BILLING_PAYMENT_PROVIDER_PAYMENT_CARD_LABEL")}</h4>
														<p>{t("BILLING_PAYMENT_PROVIDER_SELECT_PAYMENT_CARD_INFO")}</p>
														<div className="card-check-list">
															{
																this.state.payment_cards.map((row, index) => {
																	return (<label key={index}><input value={row.id} type="checkbox" onClick={this.handleCheckChieldElement} defaultChecked={row.isChecked} /><span>{row.name}</span></label>)
																})
															}

														</div>
													</div>
												</div>
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

export default connect(mapStateToProps)(PaymentProvider);