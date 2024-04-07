import React, { Component } from 'react';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import Controls from '@app/program/Controls';
import { store } from 'helpers';
import { GeneralAction } from 'actions/general-action';
import DeviceDetect from '@app/device-detect/DeviceDetect';
import { withCookies, Cookies } from 'react-cookie';
import { confirmAlert } from 'react-confirm-alert';
import { instanceOf } from 'prop-types';

import 'react-confirm-alert/src/react-confirm-alert.css';

var windowWidth;
class Stream extends Component {

  _isMounted = false;

  static propTypes = {
    cookies: instanceOf(Cookies).isRequired
  };

  constructor(props) {

    super(props);

    const { cookies } = props;

    this.state = {
      event: this.props.event,
      attendee_detail: {},
      url: '',
      program_id: (this.props.match.params.program_id !== undefined ? this.props.match.params.program_id : ''),
      current_video: (this.props.match.params.current_video !== undefined ? this.props.match.params.current_video : ''),
      is_iframe: 0,
      plateform: '',
      type: '',
      preLoader: true,
      data: [],
      program_setting: {},
      openSettings: false,
      myturnlist_setting: (this.props.event && this.props.event.myturnlist_setting ? this.props.event.myturnlist_setting : {}),
      checkin_settings: {},
    };

    this.videoResizing = this.videoResizing.bind(this);

    this.updateCheckin = this.updateCheckin.bind(this);
  }

  componentDidMount() {
    this.loadData();
    windowWidth = window.innerWidth;
  }

  loadData(click = false) {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.post(`${process.env.REACT_APP_URL}/${this.state.event.url}/program/videos`, this.state)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              console.log(Number(this.state.myturnlist_setting.ask_to_apeak))
              console.log(Number(response.attendee_detail.event_attendee.ask_to_apeak))
              this.setState({
                current_video: (!this.props.video.popover ? response.data.current_video : this.props.video.current_video),
                data: response.data,
                url: response.data.url,
                is_iframe: response.data.is_iframe,
                plateform: response.data.plateform,
                program_setting: response.data.program_setting,
                type: response.data.type,
                program_id: (response.data.program_data ? response.data.program_data.id : ''),
                preLoader: false,
                event: (response.event ? response.event : this.state.event),
                attendee_detail: response.attendee_detail,
                checkin_settings: response.checkin_settings,
                openSettings: ((Number(response.attendee_detail.event_attendee.ask_to_apeak) === 1 || Number(this.state.myturnlist_setting.ask_to_apeak) === 1) && !this.props.cookies.get('camera-setting') ? true : false),
              }, () => {
                if (response.data.url && click === true) {
                  store.dispatch(GeneralAction.video({ url: response.data.url, is_iframe: response.data.is_iframe ? 1 : 0, popover: this.props.video.popover, current_video: response.data.current_video, plateform: response.data.plateform, type: response.data.type, agenda_id: this.state.program_id }));
                } else if (response.data.url && !this.props.video.popover) {
                  store.dispatch(GeneralAction.video({ url: response.data.url, is_iframe: response.data.is_iframe ? 1 : 0, popover: false, current_video: response.data.current_video, plateform: response.data.plateform, type: response.data.type, agenda_id: this.state.program_id }));
                }
                window.addEventListener('resize', this.videoResizing, false);
                this.videoResizing();
                setTimeout(() => {
                  var evt = window.document.createEvent('UIEvents');
                  evt.initUIEvent('resize', true, false, window, 0);
                  window.dispatchEvent(evt)
                }, 0);
              });
            }
          }
        },
        error => { }
      );
  }

  componentWillUnmount() {

    this._isMounted = false;

    const _target = document.getElementById('videoPlayer');

    if (typeof (_target) !== 'undefined' && _target !== null && !_target.classList.contains('ProgramVideoWrapperBottom')) {
      window.removeEventListener('resize', this.videoResizing, false);
      _target.style.display = 'none';
    }

  }

  videoResizing() {
    const _idparent = document.getElementById('videoWrapperOrignal');
    if (typeof (_idparent) !== 'undefined' && _idparent !== null) {
      const _id = _idparent.getBoundingClientRect();
      const _target = document.getElementById('videoPlayer');
      const _dimention = window.innerWidth;
      if ((_target && _target !== undefined && _target !== null) && _id !== undefined) {
        const _header = document.getElementById('main-header');
        const _on = _header.classList.contains('on');
        _target.style.width = `${_id.width}px`;
        _target.style.height = `${_id.height}px`;
        _target.style.top = `${_id.top}px`;
        _target.style.left = `${_on && _dimention > 800 ? _id.left : _id.left}px`;
        _target.style.display = 'block';

        if (_dimention !== windowWidth) {
          setTimeout(() => {
            var evt = window.document.createEvent('UIEvents');
            evt.initUIEvent('resize', true, false, window, 0);
            window.dispatchEvent(evt)
            windowWidth = _dimention
          }, 300);
        }

      }
    }
  }

  handleClick = (url, program_id, is_iframe, video_id, plateform, type) => (e) => {
    e.preventDefault();
    if (Number(is_iframe) === 1) {
      this.setState({
        url: url,
        is_iframe: 1,
        program_id: program_id,
        current_video: video_id,
        plateform: plateform,
        type: type,
      }, () => {
        this.loadData(true);
      });
    } else {
      window.open(url, '_blank');
    }
  }

  updateCheckin(program) {

    this.setState({ preLoader: true });

    service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/check-in-out/save`, { ...this.state, action: 'program-checkin', ...program, 'type': 'checkin' })
      .then(
        response => {
          if (this._isMounted) {
            if (response.data.success) {
              this.loadData();
              this.setState({ preLoader: false });
              confirmAlert({
                customUI: ({ onClose }) => {
                  return (
                    <div className='app-popup-wrapper'>
                      <div className="app-popup-container">
                        <div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
                          {this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Success'}
                        </div>
                        <div className="app-popup-pane">
                          <div className="gdpr-popup-sec">
                            <p>{response.data.message}</p>
                          </div>
                        </div>
                        <div className="app-popup-footer">
                          <button style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-success" onClick={onClose}>{this.props.event.labels.GENERAL_OK || 'OK'}</button>
                        </div>
                      </div>
                    </div>
                  );
                }
              });
            } else {
              this.setState({ preLoader: false });
              confirmAlert({
                customUI: ({ onClose }) => {
                  return (
                    <div className='app-popup-wrapper'>
                      <div className="app-popup-container">
                        <div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
                          {this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Success'}
                        </div>
                        <div className="app-popup-pane">
                          <div className="gdpr-popup-sec">
                            <p>{response.data.message}</p>
                          </div>
                        </div>
                        <div className="app-popup-footer">
                          <button style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-success" onClick={onClose}>{this.props.event.labels.GENERAL_OK || 'OK'}</button>
                        </div>
                      </div>
                    </div>
                  );
                }
              });
            }
          }
        },
        error => { }
      );
  }

  
  render() {
    return (
      <div className="left-app-section h-100 w-100">
        {this.state.preLoader && <Loader fixed="true" />}
        {this.state.openSettings && this.state.event.settings && Number(this.state.event.settings.desktop_camera_mic) === 1 && <DeviceDetect event={this.props.event} onClose={() => this.setState({ openSettings: false }, () => {
          this.props.cookies.set('camera-setting', "check", { path: "/", maxAge: 14400 });
        })} onSave={() => this.setState({ openSettings: false }, () => {
          this.props.cookies.set('camera-setting', "check", { path: "/", maxAge: 14400 });
        })} />}
        {this.state.event.settings && Number(this.state.event.settings.desktop_camera_mic) === 1 && (
          <span onClick={() => this.setState({ openSettings: true })} className="btn_open_settings">
            <i className="material-icons">tune</i>
          </span>
        )}
        <style dangerouslySetInnerHTML={{
          __html: `
          .checkin-toggle .btn-label {
              border: 2px solid ${this.state.event.settings.primary_color} !important;
            }
            .checkin-toggle input[type="radio"].app-toggle + label::after {
              background: ${this.state.event.settings.primary_color} !important;
            }
          `}} />
        <div className="video-grid-section">
          <div className="left-grid-section">
            <div id="videoWrapperOrignal">
              <div className="picture-wrapp">
                <i className="material-icons">picture_in_picture_alt</i> <br />
                {this.state.url && this.state.data && this.state.data.program_data ? (
                  <p>{this.state.event.labels.DESKTOP_APP_LABEL_VIDEO_PICTURE_MODE}</p>
                ) : (
                  <p>{this.state.event.labels.DESKTOP_APP_LABEL_NO_VIDEO_FOUND}</p>
                )}
              </div>
            </div>
            {this.state.data && this.state.data.program_data && this.state.data.program_data.info && this.state.data.program_data.info.videos && ((this.state.data.program_data.info.videos_count > 1 && this.state.current_video) || (this.state.data.program_data.info.videos_count > 0 && !this.state.current_video)) && (
              <div className="app-related-video">
                <h2>{this.state.event.labels.DESKTOP_APP_LABEL_RELATED_VIDEOS}</h2>
                <div className="app-realted-video-wrapper">
                  {this.state.data && this.state.data.program_data && this.state.data.program_data.info && this.state.data.program_data.info.videos && this.state.data.program_data.info.videos.map((row, key) => {
                    return (
                      Number(row.status) === 1 && Number(row.id) !== Number(this.state.current_video) && (
                        <div key={key} onClick={this.handleClick(row.url, this.state.data.program_data.id, row.is_iframe, Number(row.id), row.plateform, row.type)} className="app-video-box">
                          <span className="app-video">
                            {
                              (() => {
                                if (row.image)
                                  return <img src={row.image} alt="" />
                                else
                                  return <img src={require("images/video.png")} alt="" />
                              })()
                            }
                          </span>
                          <div className="app-title">{row.name.substring(0, 35)}</div>
                        </div>
                      )
                    );
                  })}
                </div>
              </div>
            )}
          </div>
          <div className="right-grid-section">
            <Controls />
            {this.state.event.settings && Number(this.state.event.settings.desktop_program_screen_sidebar_program) === 1 && (
              <React.Fragment>
                <h2 style={{ marginTop: '20px' }}>{this.state.event.labels.DESKTOP_APP_LABEL_PROGRAM}</h2>
                <div className="app-video-platforms">
                  {this.state.data && this.state.data.program_videos && Object.values(this.state.data.program_videos).map((date, d_key) => {
                    return (
                      <React.Fragment key={d_key}>
                        {date.map((program, p_key) => {
                          return (
                            Number(program.videos_count) > 0 && (
                              <React.Fragment key={p_key}>
                                {Number(p_key) === 0 && (
                                  <div className="program-startdate">{program.heading_date}</div>
                                )}
                                <div className="program-wrapp-section">
                                  <div className="program-title-wrapper d-flex">
                                    <h5 className='program-title w-100'>{program.prg_detail.topic}</h5>
                                    {this.state.data && this.state.data.checkin_settings && this.state.data.checkin_settings.program_checkin === 1 && (
                                      <div className='program-checkin program-checkin-sm mr-2'>
                                        <div className="gdpr-acceptbox checkin-toggle">
                                          <div className="app-title">{(this.state.checkin && this.state.checkin.info ? this.state.checkin.info[0].value : '')}</div>
                                          <div className="app-label-area">
                                            <input
                                              id={`checkin-${program.id}`}
                                              type="radio"
                                              className="app-toggle app-toggle-left"
                                              value="off"
                                              onChange={(event) => {
                                                event.preventDefault();
                                                this.updateCheckin(program);
                                              }}
                                              checked={program.checkin_status === 2 ? true : false}
                                            />
                                            <label className="btn-label" htmlFor={`checkin-${program.id}`}>
                                              {this.props.event.labels.GENERAL_SHORT_CHECKIN_IN || 'In'}
                                            </label>
                                            <input
                                              id={`checkout-${program.id}`}
                                              type="radio"
                                              className="app-toggle app-toggle-right"
                                              value="on"
                                              onChange={(event) => {
                                                event.preventDefault();
                                                this.updateCheckin(program);
                                              }}
                                              checked={program.checkin_status === 1 ? true : false}
                                            />
                                            <label className="btn-label" htmlFor={`checkout-${program.id}`}>
                                              {this.props.event.labels.GENERAL_SHORT_CHECKIN_OUT || 'Out'}
                                            </label>
                                          </div>
                                        </div>
                                      </div>
                                    )}
                                  </div>
                                  {Number(this.state.program_setting.agenda_display_time) === 1 && (
                                    <div className="program-time">{program.start_time} - {program.end_time}</div>
                                  )}
                                  {program.videos && program.videos.map((video, key) => {
                                    return (
                                      Number(video.status) === 1 && (
                                        <div style={{ cursor: 'pointer' }} key={key} onClick={this.handleClick(video.url, program.id, video.is_iframe, Number(video.id), video.plateform, video.type)} className="program-video-list">
                                          <div className={`program-video-box ${Number(this.state.current_video) === Number(video.id) ? 'app-active' : ''}`}>
                                            <div className="app-video sm-video-icon">
                                              <img src={require("images/video-stream.png")} alt="" />
                                            </div>
                                            <div className="app-title">
                                              {video.name.substring(0, 35)}
                                            </div>
                                          </div>
                                        </div>
                                      )
                                    );
                                  })}
                                </div>
                              </React.Fragment>
                            )
                          );
                        })}
                      </React.Fragment>
                    );
                  })}

                </div>
              </React.Fragment>
            )}
          </div>
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  const { event, video } = state;
  return {
    event, video
  };
}

export default connect(mapStateToProps)(withCookies(Stream));
