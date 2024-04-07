let templateInfo = localStorage.getItem('template');
const initialState = (templateInfo && templateInfo !== undefined ? JSON.parse(templateInfo) : {});

export function template(state = initialState, action) {
    switch (action.type) {
        case "template":
            localStorage.setItem('template', JSON.stringify(action.template));
            return action.template;
        default:
            return state;
    }
}