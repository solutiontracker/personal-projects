let invitationInfo = localStorage.getItem('invitationInfo');
const initialState = (invitationInfo && invitationInfo !== undefined ? JSON.parse(invitationInfo) : {});

export function invitation(state = initialState, action) {
    switch (action.type) {
        case "invitation":
            if (action.invitation) {
                localStorage.setItem('invitationInfo', JSON.stringify(action.invitation));
                return action.invitation;
            } else {
                localStorage.removeItem('invitationInfo');
                return {};
            }

        default:
            return state;
    }
}