export function eventStep(state = 1, action) {
    switch (action.type) {
        case "step":
            return action.step;
        default:
            return state;
    }
}