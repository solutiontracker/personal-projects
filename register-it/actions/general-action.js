export const npm = {
    errors, redirect, update, eventInfo
};

function eventInfo(event) {
    return dispatch => {
        dispatch({ type: "event-info", event: event });
    };
}

function errors(errors) {
    return dispatch => {
        dispatch({ type: "errors", errors: errors });
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
