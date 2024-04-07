import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
class Lobby extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      event: this.props.event,
      checkin: {},
      checkInOutSetting: {},
      preLoader: true,
    };
  }

  componentDidMount() {
    if (Number(this.state.event.settings.desktop_program_mode) === 0) {
      this.props.history.push(`/event/${this.state.event.url}/streaming`);
    }
    this.loadData();
  }

  loadData() {
    this._isMounted = true;
    service.get(`${process.env.REACT_APP_URL}/${this.state.event.url}/dashboard/lobby`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                preLoader: false,
                checkin: response.data.checkin,
                checkInOutSetting: response.data.checkInOutSetting,
                event: (response.event ? response.event : this.state.event),
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

  render() {
    return (
      <React.Fragment>
        {this.state.preLoader && <Loader fixed="true" />}
        <div className={`desktop-section-area h-100 ${Number(this.state.event.settings.desktop_theme) === 2 ? 'app-secondry-theme' : ''}`}>
          <div className="row d-flex m-0 h-100 align-items-center justify-content-center">
            <div className="row d-flex justify-content-center">
              <div className="col-12 text-center heading-lobby">
                <h2>{this.state.event.labels.DESKTOP_APP_LABEL_WELCOME}</h2>
              </div>
              <React.Fragment>
                {this.state.event.settings && Number(this.state.event.settings.desktop_activate_checkin) === 1 && (
                  <div className="col-4 text-center">
                    <Link className="slidebox sliboxtheme_1" to={`/event/${this.state.event.url}/check-in`}>
                      <span style={{ backgroundColor: this.state.event.settings.primary_color }} className="app-title">
                        <strong>{this.state.event.labels.DESKTOP_APP_LOBY_LABEL_CHECKIN}</strong>
                      </span>
                    </Link>
                  </div>
                )}
                {this.state.event.settings && Number(this.state.event.settings.desktop_activate_programs) === 1 && (
                  <div className="col-4 text-center">
                    <Link className="slidebox sliboxtheme_2" to={`/event/${this.state.event.url}/timetable`}>
                      <span style={{ backgroundColor: this.state.event.settings.primary_color }} className="app-title">
                        <strong>{this.state.event.labels.DESKTOP_APP_LOBY_LABEL_PROGRAM}</strong>
                      </span>
                    </Link>
                  </div>
                )}
                {this.state.event.settings && Number(this.state.event.settings.desktop_activate_streaming) === 1 && (
                  <div className="col-4 text-center">
                    <Link className="slidebox sliboxtheme_3" to={`/event/${this.state.event.url}/streaming`}>
                      <span style={{ backgroundColor: this.state.event.settings.primary_color }} className="app-title">
                        <strong>{this.state.event.labels.DESKTOP_APP_LABEL_LIVE_STREAM}</strong>
                      </span>
                    </Link>
                  </div>
                )}
              </React.Fragment>
            </div>
          </div>
        </div>
      </React.Fragment>
    )
  }
}

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(Lobby);
