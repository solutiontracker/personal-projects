import React from 'react';
import { Route } from 'react-router-dom';
import { connect } from 'react-redux';
import Loader from '@app/modules/Loader';
import { withRouter } from 'react-router-dom';

const AuthLayout = ({ children, ...rest }) => {
    return (
        <div className="container-fluid h-100">
            <div className="row d-flex h-100">
                <div className="col-6 h-100 align-items-center justify-content-center d-flex">
                    {children}
                </div>
                <div style={{ backgroundColor: (children.props.event.settings !== undefined ? children.props.event.settings.primary_color : '') }} className="col-6 align-items-center justify-content-center d-flex">
                    {children.props.event.settings.virtual_app_logo || children.props.event.settings.header_logo ? (
                        <React.Fragment>
                            {children.props.event.settings.virtual_app_logo ? (
                                <img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/virtual-branding/${children.props.event.settings.virtual_app_logo}`} alt="" />
                            ) : (
                                <img src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${children.props.event.settings.header_logo}`} alt="" />
                            )}
                        </React.Fragment>
                    ) : (
                        <img className="logo-event" src={require('images/logo.svg')} alt="logo" />
                    )}
                </div>
            </div>
        </div>
    )
}

class AuthLayoutRoute extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            event: this.props.event,
        };
    }

    static getDerivedStateFromProps(props, state) {
        if (props.event.id !== undefined && state.event.id !== props.event.id) {
            return {
                event: props.event,
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    render() {
        const { component: Component, ...rest } = this.props;
        return (
            <Route {...rest} render={matchProps => (
                this.state.event.id !== undefined ? (
                    <AuthLayout history={this.props.history}>
                        <Component
                            event={this.state.event}
                            {...matchProps} />
                    </AuthLayout>
                ) : (
                    <Loader />
                )
            )} />
        )
    }
};

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(withRouter(AuthLayoutRoute));