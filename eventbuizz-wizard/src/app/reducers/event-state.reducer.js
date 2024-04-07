let event = localStorage.getItem('eventState');
const initialState = (event && event !== undefined ? JSON.parse(event) : {});

export function eventState(state = initialState, action) { 
    switch (action.type) {
        case "event-state":
            let obj = {};
            if(Array.isArray(action.eventState.detail)) obj.detail = Object.assign({}, action.eventState.detail);
            if(Array.isArray(action.eventState.duration)) obj.duration = Object.assign({}, action.eventState.duration);
            if(Array.isArray(action.eventState.detail)) obj.editData = true;
            localStorage.setItem('eventState', JSON.stringify(obj));
            return action.eventState;
        default:
            return state;
    }
}