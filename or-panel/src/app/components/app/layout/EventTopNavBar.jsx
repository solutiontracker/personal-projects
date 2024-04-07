import * as React from 'react';
import { Translation } from "react-i18next";
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import { ReactSVG } from 'react-svg';
import { store } from 'helpers';
import { withRouter } from 'react-router-dom';
import { service } from 'services/service';
class EventTopNavBar extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            default_template_id: (this.props.event !== undefined ? this.props.event.default_template_id : null),
            event: this.props.event,
            checkin: { status: 1 }
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/event-settings/module/alerts`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data) {
                                this.setState({
                                    checkin: response.data.module
                                });
                            }
                        }
                    }
                },
                error => { }
            );
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

    componentWillUnmount() {
        this._isMounted = false;
    }

    render() {
        return (
            <Translation>
                {t => (
                    <div className="col-9">
                        <nav className="dashboard-nav ">
                            <ul>
                                <li>
                                    <NavLink to={`/event/template/edit/${this.state.default_template_id}`}>
                                        <ReactSVG wrapper="span" className="icons" src={require('img/icon-templates.svg')} />
                                        {t('M_TEMAPLETS')}
                                    </NavLink>
                                </li>
                                <li>
                                    <a className={window.location.pathname.includes('/event/invitation/send-invitation') ? 'active' : ''} href={null} onClick={() => { store.dispatch({ type: "invitation", invitation: null }); this.props.history.push('/event/invitation/send-invitation'); }}>
                                        <ReactSVG wrapper="span" className="icons" src={require('img/icon-sendinvite.svg')} />
                                        {t('M_SEND_INVITE')}
                                    </a>
                                </li>
                                <li>
                                    <NavLink to={`/event/reports`}>
                                        <ReactSVG wrapper="span" className="icons" src={require('img/icon-reports.svg')} />
                                        {t('M_REPORTS')}
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink to={`/dashboard`}>
                                        <ReactSVG wrapper="span" className="icons" src={require('img/icon-analytics.svg')} />
                                        {t('M_ANALYTICS')}
                                    </NavLink>
                                </li>
                                <li style={{ pointerEvents: this.state.checkin && Number(this.state.checkin.status) === 0 ? 'none' : 'fill' }}>
                                    <NavLink to={`/event/news/alerts`}>
                                        <ReactSVG wrapper="span" className="icons" src={require('img/icon-sendnews.svg')} />
                                        {t('M_SEND_NEWS')}
                                    </NavLink>
                                </li>
                            </ul>
                        </nav>
                    </div>
                )}
            </Translation>
        );
    }
};

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(withRouter(EventTopNavBar));

