export function alert(state = {}, action) {
  switch (action.type) {
    case "request":
      return {
        type: 'request',
      };
    case "success":
      return {
        class: 'alert-success',
        message: action.message,
        redirect: action.redirect,
        redirect_id: action.authentication_id,
        
        success: true
      };
    case "error":
      return {
        class: 'alert-danger',
        message: action.message,
        ms: action.ms
      };
    case "alert-clear":
      return {
        redirect: action.redirect,
      };
    default:
      return state
  }
}