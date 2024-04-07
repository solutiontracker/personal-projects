import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  attendees: null,
  labels: null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
  currentPage: 1,
};

export const attendeeSlice = createSlice({
  name: "attendee",
  initialState,
  reducers: {
    getAttendees: (state, {payload}) => {
      state.loading = true;
      state.currentPage = payload.page;
      if(payload.mount){
       state.attendees = null;
      }
    },
    setAttendees: (state, { payload }) => {
      state.attendees = state.currentPage > 1 ? [...state.attendees, ...payload.data]: payload.data;
      state.total = payload.meta.total;
      state.labels = payload.labels
      state.totalPages = Math.ceil(payload.meta.total / payload.meta.per_page);
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getAttendees, setAttendees, setError } = attendeeSlice.actions;

export const attendeeSelector = (state) => state.attendee;

export default attendeeSlice.reducer;

export const fetchAttendees = (url, page, limit, search, mount) => {
    return async (dispatch) => {
    dispatch(getAttendees({page, mount}));
    let endPoint = `/event/${url}/attendees?page=${page}&limit=${limit}`;
    if (search !== "") {
      endPoint = `/event/${url}/attendees?query=${search}&page=${page}&limit=${limit}`;
    }  
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setAttendees(res));
      if (mount) {
        dispatch(incrementFetchLoadCount());
      }
    } catch (error) {
      dispatch(setError());
    }
  };
};


