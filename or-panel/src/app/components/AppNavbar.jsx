import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { AuthAction } from 'actions/auth/auth-action';
import { withRouter } from 'react-router-dom';
import Img from 'react-image';
import { Translation, initReactI18next } from "react-i18next";
import { connect } from 'react-redux';
import i18n from "i18next";
import { confirmAlert } from "react-confirm-alert";
import { service } from 'services/service';
import { GeneralAction } from 'actions/general-action';

const _languages = ['English', 'Danish', 'Norwegian', 'German', 'Lithuanian', 'Finnish', 'Swedish', 'Dutch', 'Flemish'];
const _lang = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];

class AppNavbar extends React.Component {

	state = {
		event_name: this.props.event.name,
		event_id: this.props.event.id,
		userinfo: [],
		languages: [{ id: 1, name: "English" }, { id: 2, name: "Danish" }],
		interfaceLanguageId: localStorage.getItem('interface_language_id') > 0 ? localStorage.getItem('interface_language_id') : 1
	}

	constructor(props) {
		super(props);
		localStorage.setItem('persistent_event_state', '0');
		this.switchLanguage = this.switchLanguage.bind(this);
	}

	componentDidMount() {
		i18n.use(initReactI18next)
			.init({ lng: _lang[this.state.interfaceLanguageId - 1] });
		let user = JSON.parse(localStorage.getItem('eventBuizz'));
		if (user) {
			this.setState({ userinfo: user.data.user })
		}
	}

	componentDidUpdate(prevProps, prevState) {
		if (prevProps.alert !== this.props.alert) {
			prevProps.history.push((this.props.alert.redirect !== undefined ? this.props.alert.redirect : '/'));
		} else if (prevProps.redirect !== this.props.redirect) {
			let user = JSON.parse(localStorage.getItem('eventBuizz'));
			if (user) {
				this.setState({ userinfo: user.data.user })
			}
		}
	}

	static getDerivedStateFromProps(props, state) {
		if (props.event !== state.event) {
			return {
				event_name: props.event.name
			};
		}
		// Return null to indicate no change to state.
		return null;
	}

	handleSignOut = e => {
		e.preventDefault();
		AuthAction.logout();
	}

	handleDashboard = e => {
		e.preventDefault();
		const persistentEventState = localStorage.getItem('persistent_event_state');
		if (persistentEventState !== undefined && persistentEventState === '1') {
			confirmAlert({
				customUI: ({ onClose }) => {
					return (
						<Translation>
							{
								t =>
									<div className='app-main-popup'>
										<div className="app-header">
											<h4>{t('EE_ON_LEAVE_SCREEN')}</h4>
										</div>
										<div className="app-body">
											<p>{t('EE_ON_LEAVE_SCREEN_MSG')}</p>
										</div>
										<div className="app-footer">
											<button className="btn btn-cancel" onClick={() => {
												onClose();
												this.props.history.push('/');
											}}>{t('G_DISCARD')}
											</button>
											<button className="btn btn-success" onClick={() => {
												onClose();
											}}>{t('G_SAVE')}
											</button>
										</div>
									</div>
							}
						</Translation>
					);
				}
			});
		} else {
			this.props.history.push('/')
		}
	}

	switchLanguage(id) {
		localStorage.setItem('interface_language_id', id);
		this.setState({
			'interfaceLanguageId': id
		});
		i18n.use(initReactI18next).init({ lng: _lang[id - 1] });
		const data_request = { interface_language_id: id }
		service.put(`${process.env.REACT_APP_URL}/user-settings/updateUserInterfaceLanguage`, data_request)
			.then(
				response => {
					if (response.success) {
						this.setState({
							'message': response.message,
							'success': true
						}, () => {
							this.props.dispatch(GeneralAction.update(!this.props.update));
						});
					} else {
						this.setState({
							'message': response.message,
							'success': false
						});
					}
				},
				error => { }
			);
	}

	render() {
		const { languages } = this.state;
		return (
			<Translation>
				{
					t => <header className='header'>
						<div className="container">
							<div className="row bottom-header-elements">
								<div className="col-8">
									{this.props.cancel === "active" && this.state.event_name && !window.location.pathname.includes('/event/create') && !window.location.pathname.includes('/account/organizer/') && (
										<React.Fragment>
										<h4>{this.state.event_name}</h4>
										<h6> Event id: {this.state.event_id}</h6>
										</React.Fragment>
									)}
								</div>
								<div className="col-4 d-flex justify-content-end">
									<ul className="main-navigation">
										<li>
											{this.state.userinfo.first_name} {this.state.userinfo.last_name} <i className="material-icons">expand_more</i>
											<ul>
												<li><NavLink to="/account/organizer/profile">
													<span className="icons">
														<Img alt="" src={require('img/ico-user.svg')} />
													</span>{t('M_MY_ACCOUNT')}</NavLink></li>
												<li><NavLink to="/account/organizer/change-password">
													<span className="icons">
														<Img alt="" src={require('img/ico-lock-lg.svg')} />
													</span>{t('M_CHANGE_PASSWORD')}</NavLink></li>
												<li><a href="#!" onClick={this.handleSignOut.bind(this)} >
													<span className="icons">
														<Img alt="" src={require('img/ico-logout.svg')} />
													</span>{t('LOGOUT')}</a></li>
											</ul>
										</li>
										<li>
											{_languages[this.state.interfaceLanguageId - 1]} <i className="material-icons">expand_more</i>
											<ul>
												{languages.map((value, key) => {
													return (
														<li key={key} onClick={() => this.switchLanguage(value.id)}>
															<a className={_languages[this.state.interfaceLanguageId - 1] === value.name ? 'active' : ''}>
																<span className="icons">
																	<Img alt="" src={require('img/ico-globe-new.svg')} />
																</span>
																{value.name}
															</a>
														</li>
													);
												})}
											</ul>
										</li>
									</ul>
								</div>
								{/* {this.props.cancel === "active" && window.location.pathname.includes('/event/templates') && (
									<div className="col-4 d-flex justify-content-end">
										<Link to="/" onClick={this.handleDashboard.bind(this)}><i className="material-icons">close</i> {t('EL_HOME')}</Link>
									</div>
								)} */}
							</div>
						</div>
					</header>

				}
			</Translation>
		);
	}
}

function mapStateToProps(state) {
	const { event, alert, redirect, update } = state;
	return {
		event, alert, redirect, update
	};
}

export default connect(mapStateToProps)(withRouter(AppNavbar));
