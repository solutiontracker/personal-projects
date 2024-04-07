let agoraInfo = localStorage.getItem('agoraInfo');
const initialState = (agoraInfo && agoraInfo !== undefined ? JSON.parse(agoraInfo) : {});

export function agora(state = initialState, action) {
    switch (action.type) {
        case "agora":
            if (action.agora) {
                localStorage.setItem('agoraInfo', JSON.stringify(action.agora));
                return action.agora;
            } else {
                return {};
            }
        default:
            return state;
    }
}