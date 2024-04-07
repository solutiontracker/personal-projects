let videoInfo = localStorage.getItem('videoInfo');
const initialState = (videoInfo && videoInfo !== undefined ? JSON.parse(videoInfo) : {});

export function video(state = initialState, action) {
  switch (action.type) {
    case "video":
      if (action.video) {
        localStorage.setItem('videoInfo', JSON.stringify(action.video));
        return action.video;
      } else {
        return {};
      }
      default:
        return state;
  }
}