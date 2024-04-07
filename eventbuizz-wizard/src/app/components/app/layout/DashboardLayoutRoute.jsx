import React from 'react';
import { Route } from 'react-router-dom';
import AppNavbar from '@/AppNavbar';

const DashboardLayout = ({ children, ...rest }) => {
    return (
        <div id="App" className="inner-pages">
            <AppNavbar />
            <main role="main" className="main-section">
                <div className="container">
                    <div className="wrapper-box">
                        {children}
                    </div>
                </div>
            </main>
        </div>
    )
}

const DashboardLayoutRoute = ({ component: Component, ...rest }) => {
    return (
        <Route {...rest} render={matchProps => (
            <DashboardLayout>
                <Component {...matchProps} />
            </DashboardLayout>
        )} />
    )
};

export default DashboardLayoutRoute;