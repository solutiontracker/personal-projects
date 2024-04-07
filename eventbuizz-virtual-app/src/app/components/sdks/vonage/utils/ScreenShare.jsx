import React from 'react';
import { OTPublisher } from 'opentok-react';
import { connect } from 'react-redux';
import { withStyles } from '@material-ui/core/styles';

const styles = () => ({
    menu: {
        height: '150px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: '10'
    },
    customBtn: {
        width: '50px',
        height: '50px',
        borderRadius: '26px',
        backgroundColor: 'rgba(0, 0, 0, 0.4)',
        backgroundSize: '50px',
        cursor: 'pointer'
    },
    leftAlign: {
        display: 'flex',
        flex: '1',
        justifyContent: 'space-evenly'
    },
    rightAlign: {
        display: 'flex',
        flex: '1',
        justifyContent: 'center'
    },
    menuContainer: {
        width: '100%',
        height: '100%',
        position: 'absolute',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'flex-end'
    }
});

class ScreenShare extends React.Component {

    _isMounted = false;

    constructor(props) {
        super(props);

        this.state = {
            uid: this.props.uid,
            error: null,
        };

        this.publisherEventHandlers = {
            streamCreated: event => {
                console.log('Share stream created!');
                this.props.dispatch({ type: 'shareStream', payload: null });
                this.props.dispatch({ type: 'publisherShareStream', payload: this.state.uid });
            },
            streamDestroyed: event => {
                event.preventDefault();
                this.props.dispatch({ type: 'publisherShareStream', payload: null });
            },
            accessDenied: event => {
                event.preventDefault();
                this.props.dispatch({ type: 'publisherShareStream', payload: null });

            },
            mediaStopped: event => {
                event.preventDefault();
                this.props.dispatch({ type: 'publisherShareStream', payload: null });
            }
        };
    }

    onError = (err) => {
        this.setState({ error: `Failed to publish: ${err.message}` });
    }

    componentDidMount() {
        this._isMounted = true;
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    render() {

        return (
            <div className={`stream-player grid-player ${(Number(this.props.vonage.publisherShareStream) === Number(this.state.uid) ? 'share-stream-thumb' : '')} ${(this.props.main ? 'main-stream-player-alt' : '')}`} id={`stream-player-${this.state.uid}`}>
                <OTPublisher
                    session={this.props.session}
                    properties={{
                        name: this.props.uid + " | " + this.props.name,
                        showControls: false,
                        videoSource: 'screen',
                        height: '100%',
                        width: '100%'
                    }}
                    eventHandlers={this.publisherEventHandlers}
                    style={{ height: '100%', width: '100%' }}
                    onError={this.onError}
                />
            </div>
        );
    }
}

function mapStateToProps(state) {
    const { event, vonage } = state;
    return {
        event, vonage
    };
}

export default connect(mapStateToProps)(withStyles(styles)(ScreenShare));
