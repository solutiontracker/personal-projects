import React, { Component } from 'react';
import AgoraRTC from 'agora-rtc-sdk';
import Select from 'react-select';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import { service } from 'services/service';
import { Translation } from "react-i18next";

var audioLevel;
class Camera extends Component {
  constructor(props) {
    super(props);
    this._localStream = null;
    this._uid = 0;
    this.state = {
      camera: null,
      microphone: null,
      cameraList: [],
      camPermission: false,
      camLabel: props.label.deviceLabel,
      userInput: props.label.userInput,
      stream: 'camera-player'
    }
  }

  componentDidMount() {
    this.getDevices("videoinput");
  }

  getDevices(type) {
    var videos = [];
    AgoraRTC.getDevices(function (items) {
      for (var i = 0; i < items.length; i++) {
        var item = items[i]
        if (type === item.kind) {
          var name = item.label
          var value = item.deviceId
          if (!name) {
            name = "camera-" + videos.length
          }
          videos.push({
            label: name,
            value: value,
          });
        }
      }
    });
    this.setState({
      cameraList: videos,
      camPermission: true
    }, () => {
      setTimeout(() => {
        if (this.state.cameraList.length > 0) {
          this.createRTCStream(this.state.cameraList[0].value).then((stream) => {
            if (stream) {
              this.setState({
                camera: this.state.cameraList[0],
                camLabel: this.state.cameraList[0].label
              });
            }
          }).catch((err) => {
            confirmAlert({
              customUI: ({ onClose }) => {
                return (
                  <Translation>
                    {t => (
                      <div className='app-popup-wrapper'>
                        <div className="app-popup-container">
                          <div className="app-popup-header" style={{ backgroundColor: this.props.event.settings.primary_color }}>
                            <h4>{t('G_MISSING_PERMISSION')}</h4>
                          </div>
                          <div className="app-popup-pane">
                            <div className="gdpr-popup-sec">
                              <p>{t('G_MISSING_PERMISSION_DESCRIPTION')}</p>
                            </div>
                          </div>
                          <div className="app-popup-footer">
                            <button
                              style={{ backgroundColor: this.props.event.settings.primary_color }}
                              className="btn btn-success"
                              onClick={() => {
                                onClose();
                                this.props.loadMenu(2, 'statusCam', this.state.camLabel, false);
                              }}
                            >
                              {this.props.event.labels.GENERAL_OK || 'OK'}
                            </button>
                          </div>
                        </div>
                      </div>
                    )}
                  </Translation>
                );
              },
              onClickOutside: () => {
                this.props.loadMenu(2, 'statusCam', this.state.camLabel, false);
              }
            });
          });
        }
      }, 500);
    });
  }

  createRTCStream(cameraId) {
    return new Promise((resolve, reject) => {
      this._uid = 1;
      if (this._localStream) {
        if (this._localStream.isPlaying()) {
          this._localStream.stop()
        }
        this._localStream.close()
      }
      // create rtc stream
      const rtcStream = AgoraRTC.createStream({
        streamID: this._uid,
        audio: true,
        video: true,
        screen: false,
        cameraId: cameraId,
      });

      // init local stream
      rtcStream.init(() => {
        this._localStream = rtcStream;
        if (document.getElementById("camera-player") !== null) this._localStream.play('camera-player');
        resolve(this._localStream);
      }, (err) => {
        reject(err)
      })
    })
  }

  handleChange = camera => {
    this.setState({
      camera: camera,
      camLabel: camera.label,
      camPermission: true
    }, () => {
      if (this._localStream !== undefined && this._localStream !== null && this._localStream.isPlaying()) {
        this._localStream.switchDevice('video', camera.value, function () {
          console.log('successfully switched to new device with id: ' + JSON.stringify(camera.value));
        }, function () {
          console.log('failed to switch to new device with id: ' + JSON.stringify(camera.value));
        });
      }
    });
  };

  componentWillUnmount() {
    if (this._localStream) {
      if (this._localStream.isPlaying()) {
        this._localStream.stop()
      }
      this._localStream.close()
    }
  }

  render() {
    const { camera } = this.state;

    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }

    return (
      <div className="device-audio-container">
        <div className="form-group">
          <Select
            value={camera}
            onChange={this.handleChange}
            options={this.state.cameraList}
          />
        </div>
        <div className="device-cam-detail">
          <div className="icon-microphone">
            <div className={`${!this.state.camPermission && 'no-videowrapper'} video-wrapper`} id={this.state.stream}>
              {!this.state.camPermission && <img src={require('images/nocam.png')} alt="" />}
            </div>
          </div>
          <p className="text-center">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_CAN_YOU_SEE_SCREEN_LABEL}</p>
        </div>
        <div className="device-test-panel">
          <button onClick={this.props.onClick(2, 'statusCam', this.state.camLabel, false)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_NO_LABEL}</button>
          {this.state.camera && (
            <button onClick={this.props.onClick(2, 'statusCam', this.state.camLabel, true)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_YES_LABEL}</button>
          )}
        </div>
      </div>
    )
  }
}

class Audio extends Component {
  constructor(props) {
    super(props);
    this.state = {
      camera: null,
      microphone: null,
      microphoneList: [],
      barWidth: 0,
      micPermission: false,
      microPhoneLabel: props.label.deviceLabel,
      userInput: props.label.userInput,
      stream: 'audio-player'
    }
  }

  componentDidMount() {
    this.getDevices("audioinput");
  }

  getDevices(type) {
    var audios = [];
    AgoraRTC.getDevices(function (items) {
      for (var i = 0; i < items.length; i++) {
        var item = items[i]
        if (type === item.kind) {
          var name = item.label
          var value = item.deviceId
          if (!name) {
            name = "camera-" + audios.length
          }
          audios.push({
            label: name,
            value: value,
          });
        }
      }
    });
    this.setState({
      microphoneList: audios,
      micPermission: true
    }, () => {
      setTimeout(() => {
        if (this.state.microphoneList.length > 0) {
          this.createRTCStream(this.state.microphoneList[0].value).then((stream) => {
            if (stream) {
              this.setState({
                barWidth: stream.getAudioLevel() * 100,
                microphone: this.state.microphoneList[0],
                microPhoneLabel: this.state.microphoneList[0].label,
              });
              audioLevel = setInterval(() => {
                const level = stream.getAudioLevel();
                this.setState({
                  barWidth: level * 100
                })
              }, 100);
            }
          }).catch((err) => {
            this.props.loadMenu(2, 'statusCam', this.state.camLabel, false);
          });
        }
      }, 500);
    });
  }

  createRTCStream(microphoneId) {
    return new Promise((resolve, reject) => {
      this._uid = 2;
      if (this._localStream) {
        if (this._localStream.isPlaying()) {
          this._localStream.stop()
        }
        this._localStream.close()
      }
      // create rtc stream
      const rtcStream = AgoraRTC.createStream({
        streamID: this._uid,
        audio: true,
        video: false,
        screen: false,
        microphoneId: microphoneId,
      });

      // init local stream
      rtcStream.init(() => {
        this._localStream = rtcStream;
        if (document.getElementById("audio-player") !== null) this._localStream.play('audio-player');
        resolve(this._localStream);
      }, (err) => {
        reject(err)
      })
    })
  }

  handleChange = microphone => {
    this.setState({
      microphone: microphone,
      microPhoneLabel: microphone.label,
      micPermission: true
    }, () => {
      if (this._localStream !== undefined && this._localStream.isPlaying()) {
        this._localStream.switchDevice('video', microphone.value, function () {
          console.log('successfully switched to new device with id: ' + JSON.stringify(microphone.value));
        }, function () {
          console.log('failed to switch to new device with id: ' + JSON.stringify(microphone.value));
        });
      }
    });
  };

  componentWillUnmount() {
    if (this._localStream) {
      if (this._localStream.isPlaying()) {
        this._localStream.stop()
      }
      this._localStream.close()
    }

    clearInterval(audioLevel);
  }

  render() {
    const { microphone } = this.state;
    return (
      <div className="device-audio-container">
        <div className="form-group">
          <Select
            value={microphone}
            onChange={this.handleChange}
            options={this.state.microphoneList}
          />
        </div>
        <div className="device-progress">
          <div className="progress-bar" style={{ width: this.state.barWidth + '%' }}></div>
        </div>
        <div className="device-mic-detail">
          <div className="icon-microphone" id={this.state.stream}>
            {this.state.micPermission ? <img src={require('images/micshadow.png')} alt="" /> : <img src={require('images/micshadowmute.png')} alt="" />}
          </div>
          <p>{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_MICROPHONE_INFO}</p>
        </div>
        <div className="device-test-panel">
          <button onClick={this.props.onClick(3, 'statusMicrophone', this.state.microPhoneLabel, false)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_NO_LABEL}</button>
          <button onClick={this.props.onClick(3, 'statusMicrophone', this.state.microPhoneLabel, true)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_YES_LABEL}</button>
        </div>
      </div>
    )
  }
}
class Speaker extends Component {
  state = {
    audioPlayPause: false,
    audioVolumne: this.props.label.deviceLabel,
  }

  handleVolume = (e) => {
    e.preventDefault();
    this.setState({
      audioVolumne: e.target.value / 100
    }, () => {
      const _audio = document.getElementById('audiowrapper');
      _audio.volume = this.state.audioVolumne;
    })
  }

  handleClick = (e) => {
    e.preventDefault();
    const _audio = document.getElementById('audiowrapper');
    _audio.volume = this.state.audioVolumne;
    this.setState({
      audioPlayPause: !this.state.audioPlayPause
    }, () => {
      if (this.state.audioPlayPause) {
        _audio.play();
      } else {
        _audio.pause();
      }
    })
  }

  componentWillUnmount() {
    const _audio = document.getElementById('audiowrapper');
    _audio.pause();
  }

  render() {
    return (
      <div className="device-audio-container">
        <div className="device-progress">
          <div className="progress-bar" style={{ width: this.state.audioVolumne * 100 + '%' }}></div>
        </div>
        <div className="device-volume-control">
          <input onChange={this.handleVolume.bind(this)} type="range" value={this.state.audioVolumne * 100} min="0" max="100" id="" />
          <p><strong>Volume:</strong> {Math.floor(this.state.audioVolumne * 100)}</p>
        </div>
        <div className="device-audio-player">
          <div onClick={this.handleClick.bind(this)} className={`${this.state.audioPlayPause && "pause"} icon-play-pause`}>
            <audio id="audiowrapper" src={require('images/test_audio.mp3')} loop></audio>
          </div>
          <p>{!this.state.audioPlayPause ? this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_SPEAKER_INFO : this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_SPEAKER_INFO_WHILE_PLAYING}</p>
        </div>
        {this.state.audioPlayPause && <div className="device-test-panel">
          <button onClick={this.props.onClick(4, 'statusSpeaker', this.state.audioVolumne, false)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_NO_LABEL}</button>
          <button onClick={this.props.onClick(4, 'statusSpeaker', this.state.audioVolumne, true)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_YES_LABEL}</button>
        </div>}
      </div>
    )
  }
}
class Result extends Component {

  save = () => {
    service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/event/camera/access/${(this.props.camera.userInput && this.props.microphone.userInput ? 1 : 0)}`, {})
      .then(
        response => {
          if (response.success) {
            this.props.onSave();
          }
        },
        error => { }
      );
  }

  render() {
    return (
      <div className="device-audio-container">
        <div className="test-results-wrapp">
          <div className="test-item">
            <div className="label">
              <img width="35" src={require('images/cam.png')} alt="" />
              {this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_CAMERA_LABEL}:
              </div>
            <div className="detail">
              {this.props.camera.userInput ? <i className="material-icons">check</i> : <i className="material-icons error">close</i>}
              {this.props.camera.deviceLabel && <p>{this.props.camera.deviceLabel}</p>}
            </div>
          </div>
          <div className="test-item">
            <div className="label">
              <img width="35" src={require('images/mic.png')} alt="" />
              {this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_MICROPHONE_LABEL}:
              </div>
            <div className="detail">
              {this.props.microphone.userInput ? <i className="material-icons">check</i> : <i className="material-icons error">close</i>}
              {this.props.microphone.deviceLabel && <p>{this.props.microphone.deviceLabel}</p>}
            </div>
          </div>
          <div className="test-item">
            <div className="label">
              <img width="35" src={require('images/speaker.png')} alt="" />
              {this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_SPEAKER_LABEL}:
              </div>
            <div className="detail">
              {this.props.speaker.userInput ? <i className="material-icons">check</i> : <i className="material-icons error">close</i>}
            </div>
          </div>
        </div>

        <div className="device-test-panel">
          <button onClick={this.props.onClick(1)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_TEST_AGAIN_LABEL}</button>
          <button onClick={this.save.bind(this)} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_CLOSE_LABEL}</button>
        </div>
      </div>
    )
  }
}
export default class DeviceDetect extends Component {
  state = {
    activeIndex: 1,
    statusCam: {
      userInput: false,
      deviceLabel: false,
    },
    statusMicrophone: {
      userInput: false,
      deviceLabel: false,
    },
    statusSpeaker: {
      userInput: false,
      deviceLabel: 0.5
    }
  }

  handleMenu = (index, type, label, status) => e => {
    e.preventDefault();
    if (status !== undefined) {
      this.setState({
        activeIndex: index,
        [type]: { deviceLabel: label, userInput: status }
      })
    } else {
      this.setState({
        activeIndex: index
      })
    }
  }

  loadMenu = (index, type, label, status) => {
    this.setState({
      activeIndex: index,
      [type]: { deviceLabel: label, userInput: status }
    })
  }

  render() {
    return (
      <div className="device-wrapper">
        <div className="device-container">
          <div className="device-menu">
            <div className="header">{this.state.activeIndex !== 4 ? this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_HEADING : this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_RESULTS_HEADING}</div>
            {this.state.activeIndex !== 4 && <React.Fragment>
              <div onClick={this.handleMenu(1)} className={this.state.activeIndex === 1 ? 'active device-item' : 'device-item'}>
                <div className="device-icon">
                  <img src={require('images/cam.png')} alt="" />
                </div>
                <div className="title">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_CAMERA_LABEL}</div>
              </div>
              <div onClick={this.handleMenu(2)} className={this.state.activeIndex === 2 ? 'active device-item' : 'device-item'}>
                <div className="device-icon">
                  <img src={require('images/mic.png')} alt="" />
                </div>
                <div className="title">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_MICROPHONE_LABEL}</div>
              </div>
              <div onClick={this.handleMenu(3)} className={this.state.activeIndex === 3 ? 'active device-item' : 'device-item'}>
                <div className="device-icon">
                  <img src={require('images/speaker.png')} alt="" />
                </div>
                <div className="title">{this.props.event.labels.DESKTOP_APP_LABEL_TEST_CAMERA_SPEAKER_LABEL}</div>
              </div>
            </React.Fragment>}
          </div>
          <div className="device-viewpoint">
            <div className="w-100">
              <span className="btn-close-viewpoints" onClick={this.props.onClose}><i className="material-icons">close</i></span>
              {this.state.activeIndex === 1 && <Camera
                label={this.state.statusCam}
                onClick={this.handleMenu.bind(this)}
                loadMenu={this.loadMenu}
                event={this.props.event}
              />}
              {this.state.activeIndex === 2 && <Audio
                label={this.state.statusMicrophone}
                onClick={this.handleMenu.bind(this)}
                loadMenu={this.loadMenu}
                event={this.props.event}
              />}
              {this.state.activeIndex === 3 && <Speaker
                label={this.state.statusSpeaker}
                onClick={this.handleMenu.bind(this)}
                loadMenu={this.loadMenu}
                event={this.props.event}
              />}
              {this.state.activeIndex === 4 && <Result
                camera={this.state.statusCam}
                microphone={this.state.statusMicrophone}
                speaker={this.state.statusSpeaker}
                onClick={this.handleMenu.bind(this)}
                loadMenu={this.loadMenu}
                onClose={this.props.onClose}
                onSave={this.props.onSave}
                event={this.props.event}
              />}
            </div>
          </div>
        </div>
      </div>
    )
  }
}
