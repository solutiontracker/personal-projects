import { AuthService } from 'services/auth/auth-service';
import { store } from 'helpers';
import { GeneralAction } from 'actions/general-action';

export const AuthAction = {
    login,
    verification,
    logout,
    passwordRequest,
    passwordReset,
    formValdiation,
    nemIDAuthentication,
    cprVerification
};

function login(email, password, url) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.login(email, password, url)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function verification(screen, provider, code, url, authentication_id) {
    return dispatch => {
        dispatch(request({ screen }));
        AuthService.verification(screen, provider, code, url, authentication_id)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function passwordRequest(email, url) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.passwordRequest(email, url)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function passwordReset(email, password, password_confirmation, url) {
    return dispatch => {
        dispatch(request({ email }));
        AuthService.passwordReset(email, password, password_confirmation, url)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function nemIDAuthentication(response, url) {
    return dispatch => {
        dispatch(request({ response }));
        AuthService.nemIDAuthentication(response, url)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function cprVerification(cpr, pid, url) {
    return dispatch => {
        dispatch(request({ cpr }));
        AuthService.cprVerification(cpr, pid, url)
            .then(
                response => {
                    if (response.success) {
                        dispatch(success(response));
                        if (response && response.data && response.data.user) dispatch(GeneralAction.auth(response));
                    } else {
                        dispatch(failure(response));
                    }
                },
                error => {
                    dispatch(failure({ message: error }));
                }
            );
    };
}

function request(response) { return { type: "request", response } }

function success(response) { return { type: "success", "redirect": response.redirect, "authentication_id": (response.data !== undefined ? response.data.authentication_id : ''), "message": response.message } }

function failure(response) { return { type: "error", "message": response.message, "ms": (response.data !== undefined ? response.data.ms : '') } }

function logout(url) {
    AuthService.logout(url).then(
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

