
import { handleResponse, guestHeader, authHeader } from "helpers";

export const AuthService = {
    login,
    passwordRequest,
    passwordReset,
    logout,
    autoLogin
};

function login(email, password, logged) {

    const requestOptions = {
        method: 'POST',
        headers: guestHeader(),
        body: JSON.stringify({ email, password, logged })
    };

    return fetch(`${process.env.REACT_APP_URL}/auth/login`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && !user.logged) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.setItem('interface_language_id', user.data.inferface_language_id);
            }
            return user;
        });
}

function autoLogin(token) {

    const requestOptions = {
        method: 'GET',
        headers: guestHeader()
    };

    return fetch(`${process.env.REACT_APP_URL}/auth/auto-login/${token}`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && !user.logged) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.setItem('interface_language_id', user.data.inferface_language_id);
            }
            return user;
        });
}

function passwordRequest(email) {

    const requestOptions = {
        method: 'POST',
        headers: guestHeader(),
        body: JSON.stringify({ email })
    };

    return fetch(`${process.env.REACT_APP_URL}/auth/password/email`, requestOptions)
        .then(handleResponse);
}

function passwordReset(email, password, password_confirmation, token) {

    const requestOptions = {
        method: 'POST',
        headers: guestHeader(),
        body: JSON.stringify({ email, password, password_confirmation, token })
    };

    return fetch(`${process.env.REACT_APP_URL}/auth/password/reset`, requestOptions)
        .then(handleResponse);
}

function logout() {

    const requestOptions = {
        method: 'POST',
        headers: authHeader(),
    };

    return fetch(`${process.env.REACT_APP_URL}/auth/logout`, requestOptions)
        .then(handleResponse)
        .then(response => {
            localStorage.removeItem('eventBuizz');
            return response;
        });
}

