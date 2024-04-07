import { AuthService } from 'services/auth/auth-service';
import { store } from 'helpers';
import { GeneralAction } from 'actions/general-action';

export const AuthAction = {
    login,
    logout,
    passwordRequest,
    passwordReset,
    formValdiation,
    autoLogin
};

function login(email, password, logged = false) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.login(email, password, logged)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response.message));
                    }
                },
                error => {
                    dispatch(failure(error));
                }
            );
    };
}

function autoLogin(token) {
    return dispatch => {
        dispatch(request({ token }));
        AuthService.autoLogin(token)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response.message));
                    }
                },
                error => {
                    dispatch(failure(error));
                }
            );
    };
}

function passwordRequest(email) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.passwordRequest(email)
            .then(
                response => {
                    if (response.success) {
                        dispatch({ type: "success", "redirect": "/reset-password", "message": response.message });
                    } else {
                        dispatch(failure(response.message));
                    }
                },
                error => {
                    dispatch(failure(error));
                }
            );
    };
}

function passwordReset(email, password, password_confirmation, token) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.passwordReset(email, password, password_confirmation, token)
            .then(
                response => {
                    if (response.success) {
                        dispatch({ type: "success", "redirect": "/login", "message": response.message });
                    } else {
                        dispatch(failure(response.message));
                    }
                },
                error => {
                    dispatch(failure(error));
                }
            );
    };
}

function request(response) { return { type: "request", response } }

function success(response) { return { type: "success", "redirect": response.redirect, "message": response.message, "logged": (response.logged ? response.logged : false) } }

function failure(message) { return { type: "error", message } }

function logout() {
    AuthService.logout().then(
        response => {
            if (response.success) {
                store.dispatch(success(response));
            }
        },
        error => {
            store.dispatch(failure(error));
        }
    );;
}

function formValdiation(type, value) {
    switch (type) {
        case 'url':
            var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!]))?/;
            if (!regex.test(value)) {
                return { type: type, status: false };
            } else {
                return { type: type, status: true };
            }
        case 'number':
            var regexR = /^[0-9]+$/;
            if (!regexR.test(value)) {
                return { type: type, status: false };
            } else {
                return { type: type, status: true };
            }
        case 'email':
            var regex2 = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (!regex2.test(value)) {
                return { type: type, status: false };
            } else {
                return { type: type, status: true };
            }
        default:
            if (value.length > 0) {
                return { type: type, status: true };
            } else {
                return { type: type, status: false };
            }

    }

}

