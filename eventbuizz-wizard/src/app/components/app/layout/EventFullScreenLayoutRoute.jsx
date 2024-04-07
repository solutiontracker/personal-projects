import React from 'react';
import { Route, NavLink } from 'react-router-dom';
import Img from 'react-image';
import AppNavbar from '@/AppNavbar';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import { Translation } from "react-i18next";
import EventTopNavBar from '@/app/layout/EventTopNavBar';

const MasterLayout = ({ children, ...rest, history }) => {
    return (
        <Translation>
            {t => (
                <div id="App" className="inner-pages">
                    <AppNavbar cancel="active" children={children} />
                    <main role="main" className="main-section">
                        <div className="container">
                            <div className="wrapper-box">
                                <div className="top-landing-page">
                                    <div className="row d-flex">
                                        <div className="col-3">
                                            <div className="logo innerpageLogo">
                                                <NavLink to={`/`}>
                                                    <Img width="180" src={require("img/logos.svg")} />
                                                </NavLink>
                                            </div>
                                        </div>
                                        {!window.location.pathname.includes('/event/create') && !window.location.pathname.includes('/account/organizer/') && (
                                            <EventTopNavBar children={children} />
                                        )}
                                    </div>
                                </div>
                                <div className="col-12">
                                    <br></br>
                                    <div className="btn-return-navigation mb-0">
                                        <NavLink to={`/event/edit/${children.props.event.id}`}><i className='material-icons'>arrow_back</i>{t('M_RETURN_TO_EDIT_EVENT')}</NavLink>
                                    </div>
                                    {children}
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            )}
        </Translation>
    );
}

class EventFullScreenLayoutRoute extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            default_template_id: (this.props.event !== undefined ? this.props.event.default_template_id : null),
            event: this.props.event,
        };
    }

    static getDerivedStateFromProps(props, state) {
        if (props.event.id !== undefined && state.event.id !== props.event.id) {
            return {
                event: props.event,
                default_template_id: (props.event !== undefined ? props.event.default_template_id : null),
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    render() {
        const { component: Component, ...rest } = this.props;
        return (
            <Route {...rest} render={matchProps => (
                <MasterLayout history={this.props.history}>
                    <Component
                        event={this.state.event}
                        default_template_id={this.state.default_template_id}
                        {...matchProps} />
                </MasterLayout>
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
export default connect(mapStateToProps)(withRouter(EventFullScreenLayoutRoute));