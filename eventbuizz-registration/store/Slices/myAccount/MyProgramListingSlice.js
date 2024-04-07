import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "store/Slices/GlobalSlice";
import { header } from 'helpers/header'

const initialState = {
  myPrograms: null,
  tracks: null,
  labels:null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
};

export const myProgramListingSlice = createSlice({
  name: "myProgramListing",
  initialState,
  reducers: {
    getMyPrograms: (state) => {
      state.loading = true;
      state.myPrograms = null;
    },
    setMyPrograms: (state, { payload }) => {
      state.myPrograms = payload.data;
      // state.tracks = payload.data.tracks;
      state.labels = state.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.error = null;
      state.myPrograms = null;
      state.tracks = null;
      state.labels = null;
      state.loading = false;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getMyPrograms, setMyPrograms, setError, clearAll } = myProgramListingSlice.actions;

export const myProgramListingSelector = (state) => state.myProgramListing;

export default myProgramListingSlice.reducer;

export const fetchMyPrograms = (url, id) => {
  return async (dispatch) => {
    dispatch(getMyPrograms());
    let endPoint = `/event/${url}/get-attendee-programs`;
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`, { headers:header("GET", id)});
      const res = await response.json();
      dispatch(setMyPrograms({data:res.data, labels:res.labels}));
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