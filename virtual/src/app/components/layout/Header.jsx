import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import Gdpr from '@app/modules/Gdpr';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import { AuthAction } from 'actions/auth/auth-action';
import { withRouter } from 'react-router-dom';
import { GeneralAction } from 'actions/general-action';
import { store } from 'helpers';
import { withCookies } from 'react-cookie';
class Header extends Component {

	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			toggle: (this.props.match.path.includes('/event/:url/streaming') ? true : false),
			gdpr: (Number(this.props.event.gdpr_log_count) === 0 ? false : false),
			preLoader: false,
			current_path: this.props.match.path
		};
	}

	handleClick = (element) => (e) => {
		e.preventDefault();
		if (element === "cancel" || element === "accept") {
			this.updateGdpr(element);
		} else {
			this.setState({
				[element]: !this.state[element],
			}, () => {
				if (element === 'toggle') {
					const toggle = document.getElementById('menuToggle');
					const _video = document.getElementById('videoWrapperOrignal');
					const _timeline = document.getElementById('timelinearea');
					const _videothumb = document.getElementById('videoPlayer');
					toggle.classList.add('disabled');
					if (_video && _videothumb) {
						if (!_videothumb.classList.contains('ProgramVideoWrapperBottom')) {
							_video.style.opacity = '0';
						}
					}
					setTimeout(() => {
						toggle.classList.remove('disabled');
						var resizeEvent = window.document.createEvent('UIEvents');
						if ((_video && _videothumb)) {
							if (!_videothumb.classList.contains('ProgramVideoWrapperBottom')) {
								resizeEvent.initUIEvent('resize', true, false, window, 0);
								window.dispatchEvent(resizeEvent);
								_video.style.opacity = '1';
							}
						}
						if (_timeline) {
							resizeEvent.initUIEvent('resize', true, false, window, 0);
							window.dispatchEvent(resizeEvent);
						}
					}, 10);
				}
			});
		}
	}

	updateGdpr(element) {
		this._isMounted = true;
		this.setState({ preLoader: true });
		service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/setting/update/gdpr/${element}`, this.state)
			.then(
				response => {
					if (response.success) {
						if (this._isMounted) {
							this.setState({
								preLoader: false,
								gdpr: false,
							});
							store.dispatch(GeneralAction.update({ update: !this.props.update, gdpr_value: (element === "accept" ? true : false) }));
						}
					}
				},
				error => { }
			);
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	handleSignOut = e => {
		e.preventDefault();
		this.setState({ preLoader: true }, () => {
			const iframe = document.querySelectorAll('.app-iframe-area iframe')[0].contentWindow;
			iframe.postMessage("logout", process.env.REACT_APP_EVENTCENTER_URL);
			this.props.cookies.set('camera-setting', "", { path: "/", maxAge: 14400 });
			setTimeout(() => {
				AuthAction.logout(this.props.event.url);
			}, 2000);
		});
	}

	componentDidUpdate(prevProps, prevState) {
		if (prevProps.alert !== this.props.alert) {
			this.props.history.push(`/event/${this.props.event.url}/login`);
		} else if (this.props.gdpr.element && prevProps.gdpr.element !== this.props.gdpr.element) {
			this.setState({
				gdpr: true
			}, () => {
				store.dispatch(GeneralAction.gdpr({}));
			});
		}
	}

	static getDerivedStateFromProps(props, state) {
		if (state.current_path !== props.match.path) {
			return {
				current_path: props.match.path,
				toggle: (props.match.path.includes('/event/:url/streaming') ? true : false),
			};
		}
		// Return null to indicate no change to state.
		return null;
	}

	render() {
		return (
			<React.Fragment>
				{this.state.preLoader && <Loader fixed="true" />}
				<header id="main-header" className={`app-header-area ${this.state.toggle && 'on'}`}>
					<div className="container-fluid">
						<div className="row d-flex align-items-center">
							<div className="col-3 d-flex align-items-center app-event-logo">
								<div className="app-header-right pr-4">
									<span id="menuToggle" onClick={this.handleClick('toggle')} className={`btn_menuToggle ${this.state.toggle && 'off'}`}>
										<span style={{ backgroundColor: this.props.event.settings.primary_color }} className="menu-bar bar1"></span>
										<span style={{ backgroundColor: this.props.event.settings.primary_color }} className="menu-bar bar2"></span>
										<span style={{ backgroundColor: this.props.event.settings.primary_color }} className="menu-bar bar3"></span>
									</span>
								</div>
								<Link to={`/event/${this.props.event.url}/lobby`}>
									{this.props.event.settings.header_logo ? (
										<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${this.props.event.settings.header_logo}`} alt="" />
									) : (
											<img src={`${process.env.REACT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`} alt="" />
										)}
								</Link>
							</div>
							<div className="col-6  app-main-title">
								<h1>{this.props.event.name}</h1>
							</div>
							<div className="col-3 d-flex justify-content-end align-items-center">
								<div className="app-header-right">
									{Number(this.props.event.gdpr_setting.enable_gdpr) === 1 && (
										<span onClick={this.handleClick('gdpr')} className="element">
											<span style={{ backgroundColor: this.props.event.settings.primary_color }} className="iconwrapp"><img src={require('images/ico-lock2.svg')} alt="" /></span>
											<strong>{this.props.event.labels.DESKTOP_APP_LABEL_GDPR}</strong>
										</span>
									)}
									<a href="#!" onClick={this.handleSignOut.bind(this)} className="element">
										<span style={{ backgroundColor: this.props.event.settings.primary_color }} className="iconwrapp"><img src={require('images/ico-logout.svg')} alt="" /></span>
										<strong>{this.props.event.labels.LOGOUT || 'Logout'}</strong>
									</a>
								</div>
							</div>
						</div>
					</div>
				</header>
				{this.state.gdpr && <Gdpr handleClick={this.handleClick} event={this.props.event} />}
			</React.Fragment>
		)
	}
}

function mapStateToProps(state) {
	const { event, alert, update, gdpr } = state;
	return {
		event, alert, update, gdpr
	};
}

export default connect(mapStateToProps)(withRouter(withCookies(Header))); 
