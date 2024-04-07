import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import 'react-phone-input-2/lib/style.css';

export default class Profile extends Component {
	_isMounted = false;
	constructor(props) {
		super(props);
		this.state = {
			id: '',
			current_password: '',
			password: '',
			password_confirmation: '',

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
		this.fetchOrganizer();
	}

	componentWillUnmount() {
		this._isMounted = false;
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
									preLoader: false,
								});
							}
						}
					}
				},
				error => { }
			);
	}

	handleChange = input => e => {
		this.setState({
			[input]: e.target.value,
			change: true
		})
	};

	save = e => {
		e.preventDefault();
		this.setState({ isLoader: true });
		service.put(`${process.env.REACT_APP_URL}/organizer/change-password`, this.state)
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
												<h1 className="section-title">{t('ORGANIZER_CHANGE_PASSWORD_LABEL')}</h1>
												<Input
													type='password'
													label={t("ORGANIZER_CURRENT_PASSWORD")}
													name='current_password'
													value={this.state.current_password}
													onChange={this.handleChange('current_password')}
													required={true}
												/>
												{this.state.errors.current_password && <p className="error-message">{this.state.errors.current_password}</p>}
												<Input
													type='password'
													label={t("ORGANIZER_NEW_PASSWORD")}
													name='password'
													value={this.state.password}
													onChange={this.handleChange('password')}
													required={true}
												/>
												{this.state.errors.password && <p className="error-message">{this.state.errors.password}</p>}
												<Input
													type='password'
													label={t("ORGANIZER_CONFIRM_NEW_PASSWORD")}
													name='password_confirmation'
													value={this.state.password_confirmation}
													onChange={this.handleChange('password_confirmation')}
													required={true}
												/>
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
