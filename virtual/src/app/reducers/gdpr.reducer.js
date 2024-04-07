export function gdpr(state = {}, action) {
    switch (action.type) {
        case "gdpr":
            return action.gdpr;
        default:
            return state;
    }
}