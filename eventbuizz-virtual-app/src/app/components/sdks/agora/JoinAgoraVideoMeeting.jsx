import React from 'react'
import Meeting from '@app/sdks/agora/pages/meeting'
import { BrowserRouterHook } from '@app/sdks/agora/utils/use-router'
import { ContainerProvider } from '@app/sdks/agora/utils/container'
import Join from '@app/sdks/agora/pages/Join'
import { ThemeProvider } from '@material-ui/styles'
import THEME from '@app/sdks/agora/utils/theme'
import '@app/sdks/agora/assets/css/style.css'
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import socketIOClient from "socket.io-client";
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import ReactHtmlParser from 'react-html-parser';

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

class JoinAgoraVideoMeeting extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      preLoader: true,
      video_id: this.props.match.params.video_id,
      channel: this.props.match.params.channel,
      joined: this.props.match.params.joined,
      role: this.props.match.params.role,
      attendee_id: this.props.auth.data.user.id,
      token: "",
      appID: "",
      video: {},
    };
  }

  componentDidMount() {
    this.loadData();
  }

  loadData() {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/create/token`, this.state)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                token: response.data.token,
                appID: response.data.appID,
                video: response.data.video,
                preLoader: false,
              }, () => {
                if (this.state.video && in_array(this.state.video.type, ["agora-panel-disscussions"])) {
                  //socket
                  socket.off(`event-buizz:request_to_speak_action_${this.props.event.id}_${this.state.video.agenda_id}`);
                  socket.on(`event-buizz:request_to_speak_action_${this.props.event.id}_${this.state.video.agenda_id}`, this.getData);
                }
              });
            }
          }
        },
        error => { }
      );
  }

  getData = data => {
    var json = JSON.parse(data.raw_data);
    if (Number(json.current_attendee) === Number(this.props.auth.data.user.id)) {
      if (json.current_action === "stop") {
        window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/agora/join-video-meeting/${this.state.video_id}/${this.state.channel}/audience/1`;
      } else if (json.current_action === "live") {
        confirmAlert({
          customUI: ({ onClose }) => {
            return (
              <div className="app-popup-wrapper">
                <div className="app-popup-container">
                  <div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
                    {this.props.event.labels.DESKTOP_APP_LABEL_REQUEST_TO_SPEAK}
                  </div>
                  <div className="app-popup-pane">
                    <div className="gdpr-popup-sec">
                      <p>{ReactHtmlParser(this.props.event.labels.DESKTOP_APP_LABEL_TRSANSMITTED_TO_LIVE_CONFIRMATION_ALERT)}</p>
                    </div>
                  </div>
                  <div className="app-popup-footer">
                    <button
                      style={{ backgroundColor: this.props.event.settings.primary_color }}
                      className="btn btn-cancel"
                      onClick={() => {
                        onClose();
                      }}
                    >
                      {this.props.event.labels.DESKTOP_APP_LABEL_CANCEL || 'Cancel'}
                    </button>
                    <button
                      style={{ backgroundColor: this.props.event.settings.primary_color }}
                      className="btn btn-success"
                      onClick={() => {
                        onClose();
                        window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/agora/join-video-meeting/${this.state.video_id}/${this.state.channel}/participant/1`;
                      }}
                    >
                      {this.props.event.labels.DESKTOP_APP_LABEL_GO_LIVE || 'Live'}
                    </button>
                  </div>
                </div>
              </div>
            );
          }
        });
      } else if (json.current_action === "reload") {
        window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/agora/join-video-meeting/${this.state.video_id}/${this.state.channel}/participant/1`;
      }
    }
  };

  static getDerivedStateFromProps(props, state) {
    if (props.match.params.joined !== undefined && props.match.params.joined !== state.joined) {
      return {
        joined: props.match.params.joined
      };
    } else if (props.match.params.role !== undefined && props.match.params.role !== state.role) {
      return {
        role: props.match.params.role
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  render() {
    return (
      <ThemeProvider theme={THEME}>
        {this.state.preLoader && <Loader fixed="true" />}
        <ContainerProvider>
          <BrowserRouterHook>
            {this.state.video && ((this.state.video.type !== "agora-rooms" && Number(this.state.joined) === 1) || (this.state.video.type === "agora-rooms")) ? (
              <React.Fragment>
                {this.state.token && this.state.appID && (
                  <React.Fragment>
                    <Meeting history={this.props.history} agora={this.props.agora} event={this.props.event} auth={this.props.auth.data.user} userRole={this.state.role} name="name" channelName={`${this.state.channel}`} token={this.state.token} appID={this.state.appID} video={this.state.video} video_id={this.state.video_id} />
                  </React.Fragment>
                )}
              </React.Fragment>
            ) : (
                <Join history={this.props.history} agora={this.props.agora} event={this.props.event} userRole={this.state.role} name="name" channelName={this.props.match.params.channel} video={this.state.video} video_id={this.state.video_id} />
              )}
          </BrowserRouterHook>
        </ContainerProvider>
      </ThemeProvider>
    )
  }
}

function mapStateToProps(state) {
  const { agora, event, auth } = state;
  return {
    agora, event, auth
  };
}

export default connect(mapStateToProps)(JoinAgoraVideoMeeting);