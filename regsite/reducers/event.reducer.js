export function event(state = {}, action) {
  switch (action.type) {
    case "event-info":
      if (action.event) {
        return action.event;
      } else {
        return {};
      }
    default:
      return state;
  }
}
