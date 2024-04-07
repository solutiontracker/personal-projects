let eventInfo = localStorage.getItem('eventInfo');
const initialState = (eventInfo && eventInfo !== undefined ? JSON.parse(eventInfo) : {});

export function event(state = initialState, action) {
    switch (action.type) {
        case "event-info":
            if (action.event) {
                localStorage.setItem('event_id', action.event.id);
                localStorage.setItem('language_id', action.event.language_id);
                localStorage.setItem('eventInfo', JSON.stringify(action.event));
                return action.event;
            } else {
                return {};
            }
        default:
            return state;
    }
}