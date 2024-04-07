export const GeneralAction = {
    errors, stream, redirect, update, auth, video, agora, gdpr
};

function errors(errors) {
    return dispatch => {
        dispatch({ type: "errors", errors: errors });
    };
}

function stream(stream) {
    return dispatch => {
        dispatch({ type: "stream", stream: stream });
    };
}

function video(video) {
    return dispatch => {
        dispatch({ type: "video", video: video });
    };
}

function redirect(redirect) {
    return dispatch => {
        dispatch({ type: "redirect", redirect: redirect });
    };
}

function update(update) {
    return dispatch => {
        dispatch({ type: "update", update: update });
    };
}

function auth(user) {
    return dispatch => {
        dispatch({ type: "auth-info", user: user });
    };
}

function agora(agora) {
    return dispatch => {
        dispatch({ type: "agora", agora: agora });
    };
}

function gdpr(gdpr) {
    return dispatch => {
        dispatch({ type: "gdpr", gdpr: gdpr });
    };
}