let streamInfo = localStorage.getItem('streamInfo');
const initialState = (streamInfo && streamInfo !== undefined ? JSON.parse(streamInfo) : {});

export function stream(state = initialState, action) {
    switch (action.type) {
        case "stream":
            if (action.stream) {
                if (!Object.keys(action.stream).length) {
                    localStorage.removeItem('myturnlist_streaming_agenda_id');
                }
                localStorage.setItem('streamInfo', JSON.stringify(action.stream));
                return action.stream;
            } else {
                return {};
            }
        default:
            return state;
    }
}