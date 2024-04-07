export const EventAction = {
  eventInfo
};

function eventInfo(event) {
  return dispatch => {
    dispatch({ type: "event-info", event: event });
  };
}