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
        logged: action.logged,
        success: true
      };
    case "error":
      return {
        class: 'alert-danger',
        message: action.message
      };
    case "alert-clear":
      return {
        redirect: action.redirect,
      };
    default:
      return state
  }
}