import * as React from 'react';
import { Switch, Redirect, Route } from 'react-router-dom';
import AuthLayoutRoute from "@app/auth/layout/AuthLayoutRoute";
import MasterLayoutRoute from "@app/layout/MasterLayoutRoute";
import { ltrim } from 'helpers';
import Login from '@app/auth/Login';
import ChooseProvider from '@app/auth/ChooseProvider';
import Verification from '@app/auth/Verification';
import CprVerification from '@app/auth/CprVerification';
import ResetPasswordRequest from '@app/auth/ResetPasswordRequest';
import CprLogin from '@app/auth/CprLogin';
import ResetPassword from '@app/auth/ResetPassword';
import Lobby from '@app/dashboard/Lobby';
import Stream from '@app/program/Stream';
import TimeSchedule from '@app/program/TimeSchedule';
import CheckInOut from '@app/check-in-out/Index';
import JoinAgoraVideoMeeting from '@app/sdks/agora/JoinAgoraVideoMeeting';
import JoinVonageMeeting from '@app/sdks/vonage/Index';
import AgoraMyTurnListLive from '@app/sdks/agora/MyTurnListLive';
import VonageMyTurnListLive from '@app/sdks/vonage/MyTurnListLive';
import PageCrashed from '@app/PageCrashed';
import Error404 from '@app/Error404';
class RouterOutlet extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    let path = ltrim(window.location.pathname, "/");
    let params = path.split("/");
    this.state = {
      url: (params.length >= 2 ? params[1] : ''),
    };
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  render() {
    return (
      <React.Fragment>
        {!localStorage.getItem('eventBuizz') ? (
          <Switch>
            {/*Auth Routes*/}
            <AuthLayoutRoute path="/event/:url" component={Login} exact />
            <AuthLayoutRoute path="/event/:url/login" component={Login} exact />
            <AuthLayoutRoute path="/event/:url/choose-provider/:id" component={ChooseProvider} exact />
            <AuthLayoutRoute path="/event/:url/verification/:id" component={Verification} exact />
            <AuthLayoutRoute path="/event/:url/cpr-verification/:pid" component={CprVerification} exact />
            <AuthLayoutRoute path="/event/:url/reset-password-request" component={ResetPasswordRequest} exact />
            <AuthLayoutRoute path="/event/:url/cpr-login" component={CprLogin} exact />
            <AuthLayoutRoute path="/event/:url/reset-password" component={ResetPassword} exact />

            {/*Live Streaming Routes => Load balancing testing*/}
            <Route path="/event/:url/vonage/join-video-meeting/:video_id/:channel/:role(host|audience|participant)/:joined/623742331" component={JoinVonageMeeting} exact />
            <Route path="/event/:url/agora/join-video-meeting/:video_id/:channel/:role(host|audience|participant)/:joined/623742331" component={JoinAgoraVideoMeeting} exact />

            <Redirect from="*" to={`/event/${this.state.url}/login`} />
          </Switch>
        ) : (
            <Switch>
              {/*Dashboard Routes*/}
              <MasterLayoutRoute path="/event/:url/lobby" component={Lobby} exact />

              {/*Program Routes*/}
              <MasterLayoutRoute path="/event/:url/streaming/:program_id?/:request_to_speak_program_id?/:current_video?" component={Stream} exact />
              <MasterLayoutRoute path="/event/:url/streaming-live/:program_id/:request_to_speak_program_id?/:current_video?" component={Stream} exact />
              <MasterLayoutRoute path="/event/:url/timetable" component={TimeSchedule} exact />

              {/*CheckInOut Routes*/}
              <MasterLayoutRoute path="/event/:url/check-in" component={CheckInOut} exact />

              {/*Live Streaming Routes*/}
              <MasterLayoutRoute path="/event/:url/agora/join-video-meeting/:video_id/:channel/:role(host|participant)" component={JoinAgoraVideoMeeting} exact />
              <Route path="/event/:url/agora/join-video-meeting/:video_id/:channel/:role(host|audience|participant)/:joined" component={JoinAgoraVideoMeeting} exact />
              <Route path="/event/:url/agora/myturnlist/live/:channel" component={AgoraMyTurnListLive} exact />
              <MasterLayoutRoute path="/event/:url/vonage/join-video-meeting/:video_id/:channel/:role(host|participant)" component={JoinVonageMeeting} exact />
              <Route path="/event/:url/vonage/join-video-meeting/:video_id/:channel/:role(host|audience|participant)/:joined" component={JoinVonageMeeting} exact />
              <Route path="/event/:url/vonage/myturnlist/live/:channel" component={VonageMyTurnListLive} exact />

              <Redirect from="*" to={`/event/${this.state.url}/lobby`} />

            </Switch>
          )}
        <Switch>
          {/*Application Common Routes*/}
          <MasterLayoutRoute path="/error" component={PageCrashed} exact />
          <MasterLayoutRoute path="/404" component={Error404} exact />    
        </Switch>
      </React.Fragment>
    );
  }
}

export default RouterOutlet;
