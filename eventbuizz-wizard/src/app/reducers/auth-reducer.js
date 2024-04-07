let authInfo = localStorage.getItem('eventBuizz');
const initialState = (authInfo && authInfo !== undefined ? JSON.parse(authInfo) : {});
 
export function auth(state = initialState, action) {
    switch (action.type) {
        case "auth-info":
            if (action.user) {
                localStorage.setItem('eventBuizz', JSON.stringify(action.user));
                return action.user;
            } else {
                return {};
            }
        default:
            return state;
    }
}