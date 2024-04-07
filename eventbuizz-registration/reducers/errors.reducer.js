export function errors(state = { errors: [] }, action) {
  switch (action.type) {
    case "errors":
      return {
        errors: action.errors,
      };
    default:
      return state;
  }
}