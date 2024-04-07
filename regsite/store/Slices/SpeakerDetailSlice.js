import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  speaker: null,
  labels: null,
  loading: false,
  error: null,
};

export const speakerDetailSlice = createSlice({
  name: "speakerDetail",
  initialState,
  reducers: {
    getSpeaker: (state) => {
      state.loading = true;
      state.speaker = null;
    },
    setSpeaker: (state, { payload }) => {
      state.speaker = payload.data;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.speaker = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getSpeaker, setSpeaker, setError, clearAll } = speakerDetailSlice.actions;

export const speakerDetailSelector = (state) => state.speakerDetail;

export default speakerDetailSlice.reducer;

export const fetchSpeakerDetail = (url, id) => {
    return async (dispatch) => {
    dispatch(getSpeaker());
    let endPoint = `/event/${url}/speakers/${id}`; 
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setSpeaker(res));
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError());
    }
  };
};

export const clearState = () => {
  return async (dispatch) => {
    dispatch(clearAll());    
  };
};
