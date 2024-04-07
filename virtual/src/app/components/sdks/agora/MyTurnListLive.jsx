import React from 'react'
import MyTurnListMeeting from '@app/sdks/agora/pages/MyTurnListMeeting'
import { BrowserRouterHook } from '@app/sdks/agora/utils/use-router'
import { ContainerProvider } from '@app/sdks/agora/utils/container'
import { ThemeProvider } from '@material-ui/styles'
import THEME from '@app/sdks/agora/utils/theme'
import '@app/sdks/agora/assets/css/style.css'
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';

class MyTurnListLive extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            preLoader: true,
            channel: this.props.match.params.channel,
            role: "participant",
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
            <ThemeProvider theme={THEME}>
                {this.state.preLoader && <Loader fixed="true" />}
                <ContainerProvider>
                    <BrowserRouterHook>
                        {this.state.token && this.state.appID && (
                            <React.Fragment>
                                <MyTurnListMeeting history={this.props.history} agora={this.props.agora} event={this.props.event} auth={this.props.auth.data.user} userRole={this.state.role} name="name" channelName={`${this.state.channel}`} token={this.state.token} appID={this.state.appID} video={this.state.video} />
                            </React.Fragment>
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

export default connect(mapStateToProps)(MyTurnListLive);