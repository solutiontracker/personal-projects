export const EventAction = {
    eventInfo, eventState, template, invitation
};

function eventInfo(event) {
    return dispatch => {
        dispatch({ type: "event-info", event: event });
    };
}

function eventState(eventState = {}) {
    return dispatch => {
        dispatch({ type: "event-state", eventState: eventState });
    };
}

function template(template) {
    return dispatch => {
        dispatch({ type: "template", template: template });
    };
}

function invitation(invitation = null) {
    return dispatch => {
        dispatch({ type: "invitation", invitation: invitation });
    };
}
