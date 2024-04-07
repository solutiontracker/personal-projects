import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from './GlobalSlice';

const initialState = {
  attendee: null,
  loading: false,
  labels:null,
  error: null,
};

export const attendeeDetailSlice = createSlice({
  name: "attendeeDetail",
  initialState,
  reducers: {
    getAttendee: (state, {payload}) => {
      state.loading = true;
      state.attendee = null;
    },
    setAttendee: (state, { payload }) => {
      state.attendee = payload.data;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state, ) => {
      state.attendee = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getAttendee, setAttendee, setError, clearAll } = attendeeDetailSlice.actions;

export const attendeeDetailSelector = (state) => state.attendeeDetail;

export default attendeeDetailSlice.reducer;

export const fetchAttendeeDetail = (url,  id) => {
    return async (dispatch) => {
    dispatch(getAttendee());
    let endPoint = `/event/${url}/attendees/${id}`; 
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setAttendee(res));
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
