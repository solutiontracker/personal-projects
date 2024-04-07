import { createSlice } from "@reduxjs/toolkit";
import { objectToArray } from "helpers/helper";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  programs: null,
  tracks: null,
  labels:null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
};

export const programListingSlice = createSlice({
  name: "programListing",
  initialState,
  reducers: {
    getPrograms: (state, {payload}) => {
      state.loading = true;
      state.programs = null;
    },
    setPrograms: (state, { payload }) => {
      state.programs = payload.data.programs;
      state.tracks = payload.data.tracks;
      state.labels = state.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getPrograms, setPrograms, setError } = programListingSlice.actions;

export const programListingSelector = (state) => state.programListing;

export default programListingSlice.reducer;

export const fetchPrograms = (url) => {
  return async (dispatch) => {
    dispatch(getPrograms());
    let endPoint = `/event/${url}/programs`;
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setPrograms({data:res.data, labels:res.labels}));
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError());
    }
  };
};
