import React from 'react';
import { Route } from 'react-router-dom';

const AuthLayout = ({ children, ...rest }) => {
    return (
        <div id="App" className="signup-wrapper">
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

const AuthLayoutRoute = ({ component: Component, ...rest }) => {
    return (
        <Route {...rest} render={matchProps => (
            <AuthLayout>
                <Component {...matchProps} />
            </AuthLayout>
        )} />
    )
};

export default AuthLayoutRoute;