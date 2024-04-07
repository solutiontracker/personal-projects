export function redirect(state = false, action) {
    switch (action.type) {
        case "redirect":
            return action.redirect;
        default:
            return state;
    }
}