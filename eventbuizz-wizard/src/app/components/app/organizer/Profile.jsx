import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import DropDown from '@/app/forms/DropDown';
import { GeneralService } from 'services/general-service';
import { connect } from 'react-redux';
import { GeneralAction } from 'actions/general-action';
import 'react-phone-input-2/lib/style.css';

class Profile extends Component {
	_isMounted = false;
	constructor(props) {
		super(props);
		this.state = {
			id: '',
			first_name: '',
			last_name: '',
			email: '',
			phone: '',
			authentication: 0,
			authentication_type: 1,
			calling_codes: [],

			//errors & loading
			message: false,
			success: true,
			errors: {},
			isLoader: false,
			preLoader: false,

			change: false
		}
	}

	componentDidMount() {
		this._isMounted = true;
		this.metadata();
		this.fetchOrganizer();
	}

	fetchOrganizer() {
		this.setState({ preLoader: true });
		service.get(`${process.env.REACT_APP_URL}/organizer/profile`)
			.then(
				response => {
					if (response.success) {
						if (response.data) {
							if (this._isMounted) {
								this.setState({
									id: response.data.organizer.id,
									first_name: response.data.organizer.first_name,
									last_name: response.data.organizer.last_name,
									email: response.data.organizer.email,
									phone: response.data.organizer.phone,
									code: response.data.organizer.code,
									authentication: response.data.organizer.authentication,
									authentication_type: response.data.organizer.authentication_type,
									preLoader: false,
								});
							}
						}
					}
				},
				error => { }
			);
	}

	metadata() {
		this.setState({ preLoader: true });
		GeneralService.metaData()
			.then(
				response => {
					if (response.success) {
						if (this._isMounted) {
							this.setState({
								calling_codes: response.data.records.country_codes,
							});
						}
					}
				},
				error => { }
			);
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	handleChange = input => e => {
		this.setState({
			[input]: e.target.value,
			change: true
		})
	};

	updateFlag = input => e => {
		this.setState({
			[input]: this.state[input] === 1 ? 0 : 1,
			change: true
		});
	};

	save = e => {
		e.preventDefault();
		this.setState({ isLoader: true });
		service.put(`${process.env.REACT_APP_URL}/organizer/profile`, this.state)
			.then(
				response => {
					if (response.success) {
						this.setState({
							message: response.message,
							success: true,
							isLoader: false,
							errors: {},
							change: false
						}, () => {
							let user = JSON.parse(localStorage.getItem('eventBuizz'));
							user.data.user.first_name = this.state.first_name;
							user.data.user.last_name = this.state.last_name;
							localStorage.setItem('eventBuizz', JSON.stringify(user));
							this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
						});
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

	handleToggle = (input, value) => (e) => {
		e.preventDefault();
		this.setState({
			[input]: value,
			change: true
		});
	};

	handleChangePhone = (input) => e => {
		this.setState({
			[input]: e.value,
			change: true
		})
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
				{t =>
					<React.Fragment>
						<div className="wrapper-content third-step">
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
										<div className="row">
											<div className="col-6">
												<h1 className="section-title">{t('ORGANIZER_BASIC_INFO_LABEL')}</h1>
												<Input
													type='text'
													label={t("ORGANIZER_FIRST_NAME")}
													name='first_name'
													value={this.state.first_name}
													onChange={this.handleChange('first_name')}
													required={true}
												/>
												{this.state.errors.first_name && <p className="error-message">{this.state.errors.first_name}</p>}
												<Input
													type='text'
													label={t("ORGANIZER_LAST_NAME")}
													name='last_name'
													value={this.state.last_name}
													onChange={this.handleChange('last_name')}
													required={false}
												/>
												{this.state.errors.last_name && <p className="error-message">{this.state.errors.last_name}</p>}
												<Input
													type='text'
													label={t("ORGANIZER_EMAIL")}
													name='email'
													value={this.state.email}
													onChange={this.handleChange('email')}
													required={true}
													disabled={true}
												/>
												{this.state.errors.email && <p className="error-message">{this.state.errors.email}</p>}
												<div className="row m-0 d-flex">
													<div className="col-3 p-0 d-flex custom-phone-field-profile">
														{this.state.calling_codes.length > 0 && (
															<DropDown
																label={false}
																listitems={this.state.calling_codes}
																selected={this.state.code}
																selectedlabel={this.getSelectedLabel(this.state.calling_codes, this.state.code)}
																onChange={this.handleChangePhone('code')}
																required={false}
															/>
														)}
													</div>
													<div className="col-9 p-0">
														<Input
															type='text'
															value={`${this.state.phone}`}
															label={t("ORGANIZER_PHONE_NO")}
															pattern='[0-9]*'
															onChange={this.handleChange('phone')}
															required={true}
														/>
														{this.state.errors.phone && <p className="error-message">{this.state.errors.phone}</p>}
													</div>
												</div>
												<div style={{ marginBottom: '0' }} className="checkbox-row inline-box">
													<h4 className="tooltipHeading">
														{t('ORGANIZER_ENABLE_TWO_FACTOR_AUTHENTICATION')}
													</h4>
													<label className="custom-checkbox-toggle">
														<input
															onChange={this.updateFlag('authentication')}
															type="checkbox" defaultChecked={this.state.authentication} /><span></span></label>
												</div>
												{Number(this.state.authentication) === 1 ? (
													<div className="authencation-lables">
														<label onClick={this.handleToggle("authentication_type", 1)}><i className="material-icons">{Number(this.state.authentication_type) === 1
															? "check_box"
															: "check_box_outline_blank"}</i> {t('ORGANIZER_AUTHENTICATION_TYPE_EMAIL_LABEL')}</label>
														<label onClick={this.handleToggle("authentication_type", 2)}><i className="material-icons">{Number(this.state.authentication_type) === 2
															? "check_box"
															: "check_box_outline_blank"}</i> {t('ORGANIZER_AUTHENTICATION_TYPE_SMS_LABEL')}</label>
													</div>
												) : ''}
											</div>
										</div>
									</div>
									<div className="bottom-component-panel clearfix">
										<button disabled={this.state.isLoader ? true : false} className="btn btn btn-save-next" onClick={this.save}>{this.state.isLoader ?
											<span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
										</button>
									</div>
								</React.Fragment>
							)}
						</div>
					</React.Fragment>
				}</Translation>
		)
	}
}

function mapStateToProps(state) {
	const { redirect } = state;
	return {
	  redirect
	};
  }
  
  export default connect(mapStateToProps)(Profile);