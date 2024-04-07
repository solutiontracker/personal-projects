import React, { useState, useEffect } from 'react';
import { Route } from 'react-router-dom';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import Header from '@app/layout/Header';
import KinesisStreamTest from '@app/modules/KinesisStreamTest';
import KinesisStreamLive from '@app/modules/KinesisStreamLive';
import AgoraOpenTokStreamLive from '@app/modules/AgoraOpenTokStreamLive';
import { store } from 'helpers';
import { GeneralAction } from 'actions/general-action';
import { Link } from 'react-router-dom';
import Tooltip from '@material-ui/core/Tooltip'
import socketIOClient from "socket.io-client";
import ReactGA from 'react-ga';

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

const Iframe = ({ url }) => {
	return (
		<React.Fragment>
			<iframe title="side-iframe" src={url} width="100%" height="100%"></iframe>
		</React.Fragment>
	)
}

const MasterLayout = ({ children, ...rest, history }) => {
	const [count, setCount] = useState(true);
	const [picture, setPicture] = useState(false);
	const [stream, setStream] = useState(false);
	const [streamInfo, setStreamInfo] = useState(children.props.stream);
	const [url, setUrl] = useState(children.props.video.url !== undefined ? children.props.video.url : '');
	const [type, setType] = useState(children.props.video.type !== undefined ? children.props.video.type : '');
	const _stream = children.props.history.location.pathname.includes('streaming');
	const [video, setVideo] = useState(children.props.video !== undefined ? children.props.video : '');
	const [fullScreen, setFullScreen] = useState(false);

	const handleClick = () => {
		setPicture(!picture);
		if (children.props.video.url) {
			store.dispatch(GeneralAction.video({ url: children.props.video.url, is_iframe: children.props.video.is_iframe ? 1 : 0, popover: !picture, current_video: children.props.video.current_video, agenda_id: children.props.video.agenda_id }));
		}
	}
	const handleFullScreen = () => {
		setFullScreen(!fullScreen);
	}

	useEffect(() => {
		if (_stream || picture) {
			setStream(true);
		} else {
			setStream(false)
		}

		if (children.props.video.url !== undefined || (children.props.video !== undefined && children.props.video.id !== video.id)) {
			setUrl(children.props.video.url);
			setType(children.props.video.type);
			setPicture(children.props.video.popover);
			setVideo(children.props.video);
			if (children.props.video.id !== video.id) setFullScreen(false);
		}

		//set stream info
		setStreamInfo(children.props.stream);

		return () => {
			//destroy socket
			if (Object.keys(streamInfo).length > 0) {
				//destroy socket
				socket.off(`event-buizz:event-streaming-actions-${children.props.event.id}-${streamInfo.attendee_id}`);
				socket.off(`event-buizz:request_to_speak_action_${children.props.event.id}_${streamInfo.agenda_id}`);
			}
		};
	});

	useEffect(() => {
		//socket
		socket.off(`event-buizz:event-streaming-common-actions-${children.props.video.current_video}`); 
		socket.on(`event-buizz:event-streaming-common-actions-${children.props.video.current_video}`, data => {
			var json = JSON.parse(data.data_info);
			if (json.control === "started-live-streaming") {
				console.log("stream refresh!")
				setTimeout(() => {
					store.dispatch(GeneralAction.video({ url: children.props.video.url+"?video="+Math.random(), is_iframe: children.props.video.is_iframe, popover: children.props.video.popover, current_video: children.props.video.current_video, agenda_id: children.props.video.agenda_id }));
				}, 3000);
			}
		});

		return () => {
			//destroy socket
			socket.off(`event-buizz:event-streaming-common-actions-${video.current_video}`);
		};
	});

	const handleiframe = (type) => (e) => {
		e.preventDefault();
		const iframe = document.querySelectorAll('.app-iframe-area iframe')[0].contentWindow;
		iframe.postMessage(type, process.env.REACT_APP_EVENTCENTER_URL);
	}

	const _backgroundImage = (Number(children.props.event.settings.desktop_theme) === 1 ? require('images/banner.jpg') : require('images/bannertheme2.jpg'));

	return (
		<div style={{ backgroundImage: 'url(' + (children.props.event && children.props.event.settings && children.props.event.settings.desktop_background_color ? process.env.REACT_APP_EVENTCENTER_URL + '/assets/event/branding/' + children.props.event.settings.desktop_background_color : _backgroundImage) + ')' }} className="app-main-wrapper h-100">
			<Header />
			<div className="app-main-screen h-100">
				<div className="app-iframe-area">
					<Iframe id="webapp" url={`${process.env.REACT_APP_EVENTCENTER_URL}/event/${children.props.event.url}/autologin/${children.props.auth.data && children.props.auth.data.autologin_token}`} />
					<div className="clearfix">
						<button onClick={handleiframe('back')} className="btn "><span className="material-icons">arrow_back</span></button>
						<button onClick={handleiframe('forward')} className="btn "><span className="material-icons">arrow_forward</span></button>
						<button onClick={handleiframe('reload')} className="btn float-right"><span className="material-icons">replay</span></button>
					</div>
				</div>
				<div style={{ position: `${fullScreen ? 'relative' : ''}` }} className="app-main-content-area h-100">
					<Route {...rest} render={matchProps => (
						<div className="container-fluid h-100 p-0">
							{(!Object.keys(streamInfo).length ? children : '')}
						</div>
					)} />
					{stream && url && (
						<div className={`${!_stream || picture ? 'ProgramVideoWrapperBottom' : ''} ${type === "agora-rooms" ? "agora-room" : ""} ${fullScreen ? "full-screen" : ""}`} id="videoPlayer">
							{!in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar', "agora-panel-disscussions", "agora-rooms"]) && (
								<span onClick={handleClick} className="btn_picture_in_picture"><i className="material-icons">{!picture ? 'picture_in_picture_alt' : 'close'}</i></span>
							)}
							{in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar', "agora-panel-disscussions"]) && (
								<Tooltip title={(fullScreen ? 'Exit full screen' : 'Full screen')}>
									<img style={{ zIndex: '9999', position: 'absolute', right: 10, bottom: 10, cursor: 'pointer', background: '#444' }} onClick={handleFullScreen} width="35" src={`${fullScreen ? require('images/exit-full-screen.png') : require('images/full-screen.png')}`} alt="" />
								</Tooltip>
							)}
							{!Object.keys(streamInfo).length ? (
								type === "agora-rooms" ? (
									<Link style={{ backgroundColor: children.props.event.settings.primary_color }} to={url} className="btn">{children.props.event.labels.DESKTOP_APP_LABEL_JOIN}</Link>
								) : (
									<iframe className="expected-video" title="videoplayer" width="100%" src={url} frameBorder="0" allowFullScreen allow="camera;microphone"></iframe>
								)
							) : ''}
						</div>
					)}
					{video.type !== "agora-panel-disscussions" && (
						<React.Fragment>
							{children.props.event && children.props.event.myturnlist_setting && children.props.event.myturnlist_setting.streaming_option === "agora" ? (
								<AgoraOpenTokStreamLive video={video} />
							) : (
								<KinesisStreamLive video={video} />
							)}
						</React.Fragment>
					)}
				</div>
			</div>
			<KinesisStreamTest count={count} onClick={() => setCount(!count)} />
		</div>
	)
}

class MasterLayoutRoute extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			event: null
		};
	}

	componentDidMount() {
		if (process.env.REACT_APP_ENVIRONMENT === "live") {
			ReactGA.initialize(this.props.event.settings.google_analytics);
			ReactGA.pageview(window.location.pathname + window.location.search);
		}
	}

	render() {
		const { component: Component, ...rest } = this.props;
		return (
			<Route {...rest} render={matchProps => (
				<MasterLayout history={this.props.history}>
					<Component
						event={this.props.event}
						video={this.props.video}
						stream={this.props.stream}
						auth={this.props.auth}
						{...matchProps} />
				</MasterLayout>
			)} />
		)
	}
};

function mapStateToProps(state) {
	const { event, stream, video, auth } = state;
	return {
		event, stream, video, auth
	};
}

export default connect(mapStateToProps)(withRouter(MasterLayoutRoute));