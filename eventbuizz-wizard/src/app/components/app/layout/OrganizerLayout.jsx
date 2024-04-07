import React from 'react';
import { Route, NavLink } from 'react-router-dom';
import Img from 'react-image';
import AppNavbar from '@/AppNavbar';
import { withRouter } from 'react-router-dom';
import { Translation } from "react-i18next";

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
                                    </div>
                                </div>
                                <div className="col-12">
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

class OrganizerLayout extends React.Component {

    render() {
        const { component: Component, ...rest } = this.props;
        return (
            <Route {...rest} render={matchProps => (
                <MasterLayout history={this.props.history}>
                    <Component {...matchProps} />
                </MasterLayout>
            )} />
        )
    }
};

export default withRouter(OrganizerLayout);