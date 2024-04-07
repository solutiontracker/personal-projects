import React, { useState, useEffect } from 'react'
import PropTypes from 'prop-types'

MyTurnListPlayer.propTypes = {
    stream: PropTypes.object
}

export default function MyTurnListPlayer(props) {

    const { stream, domId, uid, currentStream, client } = props

    const [resume, changeResume] = useState(false)

    const [autoplay, changeAutoplay] = useState(false)

    const _isMounted = React.useRef(true);

    const handleClick = () => {
        if (autoplay && !resume) {
            stream.resume()
            changeResume(true)
        }
    }

    useEffect(() => {
        if (!stream) return () => { }
    }, [stream])

    const lockPlay = React.useRef(false)

    useEffect(() => {
        if (!stream || !domId || lockPlay.current || stream.isPlaying()) return

        //set stream type
        if (Number(currentStream.getId()) === uid && client) {
            client.setRemoteVideoStreamType(stream, 0)
        }

        lockPlay.current = true

        stream.play(domId, { fit: 'cover' }, (errState) => {
            if (_isMounted.current) {
                if (errState && errState.status !== 'aborted') {
                    console.log('stream-player play failed ', domId)
                    changeAutoplay(true)
                }
                lockPlay.current = false
            }
        });

        return () => {
            if (stream && stream.isPlaying()) {
                stream.stop()
            }
            _isMounted.current = false;
        }
    }, [stream, domId])

    return (
        <React.Fragment>
            {stream && (
                <React.Fragment>
                    {props.local ? (
                        <div className={`attendee-view`} id={domId} onClick={uid ? handleClick : null}></div>
                    ) : (
                            <div className={`moderator-view`} id={domId} onClick={uid ? handleClick : null}></div>
                        )}
                </React.Fragment>
            )}
        </React.Fragment>
    )
}
