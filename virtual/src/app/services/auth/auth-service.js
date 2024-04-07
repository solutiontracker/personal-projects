
import { handleResponse, header } from "helpers";

export const AuthService = {
    login,
    verification,
    resend,
    passwordRequest,
    passwordReset,
    logout,
    nemIDAuthentication,
    cprVerification
};

function login(email, password, url) {
    const form = new FormData();
    form.append('email', email);
    form.append('password', password);

    const requestOptions = {
        method: "POST",
        headers: header(),
        body: form
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/login`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && user.data && user.data.access_token) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.removeItem('streamInfo');
                localStorage.removeItem('agoraInfo');
                localStorage.removeItem('videoInfo');
            }
            return user;
        });
}

function verification(screen, provider, code, url, authentication_id) {
    const form = new FormData();
    form.append('screen', screen);
    form.append('code', code);
    form.append('provider', provider);

    const requestOptions = {
        method: "POST",
        headers: header(),
        body: form
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/verification/${authentication_id}`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && (user.data && user.data.access_token)) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.removeItem('streamInfo');
                localStorage.removeItem('agoraInfo');
                localStorage.removeItem('videoInfo');
            }
            return user;
        });
}

function resend(screen, provider, code, url, authentication_id) {

    const form = new FormData();
    form.append('screen', screen);
    form.append('code', code);
    form.append('provider', provider);

    const requestOptions = {
        method: "POST",
        headers: header(),
        body: form
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/verification/${authentication_id}`, requestOptions)
        .then(handleResponse);
}

function passwordRequest(email, url) {
    const form = new FormData();
    form.append('email', email);
    form.append('url', url);

    const requestOptions = {
        method: "POST",
        headers: header(),
        body: form
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/password/email`, requestOptions)
        .then(handleResponse);
}

function passwordReset(email, password, password_confirmation, url) {
    const form = new FormData();
    form.append('email', email);
    form.append('password', password);
    form.append('password_confirmation', password_confirmation);
    form.append('url', url);

    const requestOptions = {
        method: "POST",
        headers: header(),
        body: form
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/password/reset`, requestOptions)
        .then(handleResponse);
}

function nemIDAuthentication(response, url) {

    const requestOptions = {
        method: 'PUT',
        headers: header(),
        body: JSON.stringify({ response })
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/cpr-login`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && user.data && user.data.access_token) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.removeItem('streamInfo');
                localStorage.removeItem('agoraInfo');
                localStorage.removeItem('videoInfo');
            }
            return user;
        });
}

function cprVerification(cpr, pid, url) {

    const requestOptions = {
        method: 'PUT',
        headers: header(),
        body: JSON.stringify({ cpr, pid })
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/cpr-verification`, requestOptions)
        .then(handleResponse)
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user.success && user.data && user.data.access_token) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('eventBuizz', JSON.stringify(user));
                localStorage.removeItem('streamInfo');
                localStorage.removeItem('agoraInfo');
                localStorage.removeItem('videoInfo');
            }
            return user;
        });
}

function logout(url) {

    const requestOptions = {
        method: 'POST',
        headers: header(),
    };

    return fetch(`${process.env.REACT_APP_URL}/${url}/auth/logout`, requestOptions)
        .then(handleResponse)
        .then(response => {
            localStorage.removeItem('eventBuizz');
            return response;
        });
}

