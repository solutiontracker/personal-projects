export function update(state = false, action) {
    switch (action.type) {
        case "update":
            return action.update;
        default:
            return state;
    }
}