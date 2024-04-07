import AgoraRTC from 'agora-rtc-sdk'
import EventEmitter from 'events'
import { isFirefox, isCompatibleChrome } from 'helpers';

console.log('agora sdk version: ' + AgoraRTC.VERSION + ' compatible: ' + AgoraRTC.checkSystemRequirements());

AgoraRTC.Logger.enableLogUpload();

export default class RTCClient {
    constructor() {
        this._client = null
        this._joined = false
        this._localStream = null
        this._params = {}
        this._uid = 0
        this._eventBus = new EventEmitter()
        this._showProfile = false
        this._subscribed = false
        this._created = false
    }

    createClient(data) {
        this._client = AgoraRTC.createClient({ mode: data.mode, codec: data.codec })
        return this._client
    }

    destroy() {
        this._created = false
        this._client = null
    }

    on(evt, callback) {
        const customEvents = [
            'localStream-added', 'stopScreenSharing'
        ]

        if (customEvents.indexOf(evt) !== -1) {
            this._eventBus.on(evt, callback)
            return
        }

        this._client.on(evt, callback)
    }

    setClientRole(role) {
        return new Promise((resolve, reject) => {
            this._client.setClientRole(role, (err) => {
                if (err) {
                    return reject(err)
                }
                resolve()
            })
        })
    }

    createRTCStream(data) {
        return new Promise((resolve, reject) => {
            this._uid = this._localStream ? this._localStream.getId() : data.uid
            if (this._localStream) {
                this.unpublish()
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
                microphoneId: data.microphoneId,
                cameraId: data.cameraId
            })

            if (data.resolution && data.resolution !== 'default') {
                rtcStream.setVideoProfile(data.resolution)
            }

            // init local stream
            rtcStream.init(() => {
                this._localStream = rtcStream
                this._eventBus.emit('localStream-added', { stream: this._localStream })
                if (data.muteVideo === false) {
                    this._localStream.muteVideo()
                }
                if (data.muteAudio === false) {
                    this._localStream.muteAudio()
                }
                resolve()
            }, (err) => {
                reject(err)
                // Toast.error("stream init failed, please open console see more detail");
                console.error('init local stream failed ', err)
            })
        })
    }

    createScreenSharingStream(data) {
        return new Promise((resolve, reject) => {
            // create screen sharing stream
            this._uid = this._localStream ? this._localStream.getId() : data.uid
            if (this._localStream) {
                this._uid = this._localStream.getId()
                this.unpublish()
                if (this._localStream.isPlaying()) {
                    this._localStream.stop()
                }
                this._localStream.close()
            }
            const screenSharingStream = AgoraRTC.createStream({
                streamID: this._uid,
                audio: false,
                video: false,
                screen: true,
                microphoneId: data.microphoneId,
                cameraId: data.cameraId
            })

            if (data.resolution && data.resolution !== 'default') {
                screenSharingStream.setScreenProfile(`720p_1`)
            }

            screenSharingStream.on('stopScreenSharing', (evt) => {
                this._eventBus.emit('stopScreenSharing', evt)
            })

            // init local stream
            screenSharingStream.init(() => {
                this._localStream = screenSharingStream

                // run callback
                this._eventBus.emit('localStream-added', { stream: this._localStream });

                resolve()
            }, (err) => {
                resolve(err)
            })
        })
    }

    setStreamFallbackOption(stream, type) {
        this._client.setStreamFallbackOption(stream, type)
    }

    subscribe(stream, callback) {
        this._client.subscribe(stream, callback)
    }

    _createTmpStream() {
        this._uid = 0
        return new Promise((resolve, reject) => {
            if (this._localStream) {
                this._localStream.close()
            }
            // create rtc stream
            const _tmpStream = AgoraRTC.createStream({
                streamID: this._uid,
                audio: true,
                video: true,
                screen: false
            })

            // init local stream
            _tmpStream.init(() => {
                this._localStream = _tmpStream
                resolve()
            }, (err) => {
                reject(err)
                // Toast.error("stream init failed, please open console see more detail");
                console.error('init local stream failed ', err)
            })
        })
    }

    getDevices() {
        return new Promise((resolve, reject) => {
            if (!this._client) { this.createClient({ codec: 'h264', mode: 'live' }) }
            this._createTmpStream().then(() => {
                AgoraRTC.getDevices((devices) => {
                    this._localStream.close()
                    resolve(devices)
                })
            })
        })
    }

    enableDualStream() {
        return new Promise((resolve, reject) => {
            this._client.enableDualStream(resolve, reject)
        })
    }

    setRemoteVideoStreamType(stream, streamType) {
        if (this._client) this._client.setRemoteVideoStreamType(stream, streamType)
    }

    join(data, screen = false) {
        this._joined = 'pending'
        return new Promise((resolve, reject) => {
            /**
             * A class defining the properties of the config parameter in the createClient method.
             * Note:
             *    Ensure that you do not leave mode and codec as empty.
             *    Ensure that you set these properties before calling Client.join.
             *  You could find more detail here. https://docs.agora.io/en/Video/API%20Reference/web/interfaces/agorartc.clientconfig.html
            **/

            this._params = data

            // handle AgoraRTC client event
            // this.handleEvents();

            // init client
            this._client.init(data.appID, () => {
                /**
                 * Joins an AgoraRTC Channel
                 * This method joins an AgoraRTC channel.
                 * Parameters
                 * tokenOrKey: string | null
                 *    Low security requirements: Pass null as the parameter value.
                 *    High security requirements: Pass the string of the Token or Channel Key as the parameter value. See Use Security Keys for details.
                 *  channel: string
                 *    A string that provides a unique channel name for the Agora session. The length must be within 64 bytes. Supported character scopes:
                 *    26 lowercase English letters a-z
                 *    26 uppercase English letters A-Z
                 *    10 numbers 0-9
                 *    Space
                 *    "!", "#", "$", "%", "&", "(", ")", "+", "-", ":", ";", "<", "=", ".", ">", "?", "@", "[", "]", "^", "_", "{", "}", "|", "~", ","
                 *  uid: number | null
                 *    The user ID, an integer. Ensure this ID is unique. If you set the uid to null, the server assigns one and returns it in the onSuccess callback.
                 *   Note:
                 *      All users in the same channel should have the same type (number) of uid.
                 *      If you use a number as the user ID, it should be a 32-bit unsigned integer with a value ranging from 0 to (232-1).
                **/
                this._client.join(data.token ? data.token : null, data.channel, data.uid ? +data.uid : null, (uid) => {
                    this._uid = uid
                    // Toast.notice("join channel: " + data.channel + " success, uid: " + uid);
                    console.log('join channel: ' + data.channel + ' success, uid: ' + uid)
                    this._joined = true

                    data.uid = uid

                    if (screen === true) {
                        if (isFirefox()) {
                            data.mediaSource = 'window'
                        } else if (!isCompatibleChrome()) {
                            // before chrome 72 need install chrome extensions
                            // You can download screen share plugin here https://chrome.google.com/webstore/detail/agora-web-screensharing/minllpmhdgpndnkomcoccfekfegnlikg
                            data.extensionId = 'minllpmhdgpndnkomcoccfekfegnlikg'
                        }
                        this.createScreenSharingStream(data).then(() => {
                            this.setRemoteVideoStreamType(this._localStream, 0)
                            resolve();
                        }).catch((err) => {
                            reject(err)
                        })
                        return
                    } else {
                        this.enableDualStream().then(() => {
                            if (data.host) {
                                this.createRTCStream(data).then(() => {
                                    this.setRemoteVideoStreamType(this._localStream, 0)
                                    resolve()
                                }).catch((err) => {
                                    reject(err)
                                })
                                return
                            }
                            resolve()
                        }).catch(err => {
                            reject(err)
                        });
                        return
                    }
                }, (err) => {
                    this._joined = false
                    reject(err)
                    console.error('client join failed', err)
                })
            }, (err) => {
                this._joined = false
                reject(err)
                console.error(err)
            })
        })
    }

    publish() {
        // publish localStream
        this._client.publish(this._localStream, (err) => {
            console.error(err)
        });
    }

    unpublish() {
        if (!this._client) {
            return
        }
        this._client.unpublish(this._localStream, (err) => {
            console.error(err)
        })
    }

    leave() {
        return new Promise(resolve => {
            if (!this._client) return resolve()
            // leave channel
            this._client.leave(() => {
                this._joined = false
                this.destroy()
                resolve()
            }, (err) => {
                console.log('channel leave failed')
                console.error(err)
            })
        })
    }

    leaveShareScreen() {
        return new Promise(resolve => {
            if (!this._client) return resolve()
            // leave channel
            this._client.leave(() => {
                this._joined = false
                resolve()
            }, (err) => {
                console.log('channel leave failed')
                console.error(err)
            })
        })
    }

    startLiveStreaming() {
        if (!this._client) {
            //Toast.error('Please Join First!')
            return
        }
        const uid = this._params.uid
        const liveTranscoding = {
            '180p': {
                width: 320,
                height: 180,
                videoBitrate: 140,
                videoFramerate: 15,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 1,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 320,
                    height: 180,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            },
            '360p': {
                width: 640,
                height: 360,
                videoBitrate: 400,
                videoFramerate: 30,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 0,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 640,
                    height: 360,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            },
            '720p': {
                width: 1280,
                height: 720,
                videoBitrate: 1130,
                videoFramerate: 24,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 1,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 1280,
                    height: 720,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            }
        }
        const transcodingConfig = liveTranscoding['720p']
        console.log('setLiveTranscoding', transcodingConfig)
        this._client.setLiveTranscoding(transcodingConfig)
        this._client.startLiveStreaming(this._params.url, true);
    }

    updateLiveTranscoding() {
        const uid = +this._params.uid
        const liveTranscoding = {
            '180p': {
                width: 320,
                height: 180,
                videoBitrate: 140,
                videoFramerate: 15,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 1,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 320,
                    height: 180,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            },
            '360p': {
                width: 640,
                height: 360,
                videoBitrate: 400,
                videoFramerate: 30,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 1,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 640,
                    height: 360,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            },
            '720p': {
                width: 1280,
                height: 720,
                videoBitrate: 1130,
                videoFramerate: 24,
                lowLatency: false,
                audioSampleRate: AgoraRTC.AUDIO_SAMPLE_RATE_48000,
                audioBitrate: 48,
                audioChannels: 1,
                videoGop: 30,
                videoCodecProfile: AgoraRTC.VIDEO_CODEC_PROFILE_HIGH,
                userCount: 1,
                backgroundColor: 0x000000,
                transcodingUsers: [{
                    uid,
                    alpha: 1,
                    width: 1280,
                    height: 720,
                    zOrder: 1,
                    x: 0,
                    y: 0
                }],
            }
        }
        const transcodingConfig = liveTranscoding['720p']
        console.log('setLiveTranscoding', transcodingConfig)
        this._client.setLiveTranscoding(transcodingConfig)
    }

    stopLiveStreaming() {
        if (!this._client) {
            //Toast.error('Please Join First!')
            return
        }
        this._client.stopLiveStreaming(this._params.url)
    }
}