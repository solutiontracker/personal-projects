import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  sponsorsByCategories: null,
  labels: null,
  loading: false,
  error: null,
};

export const sponsorSlice = createSlice({
  name: "sponsor",
  initialState,
  reducers: {
    getSponsors: (state, {payload}) => {
      state.loading = true;
    },
    setSponsors: (state, { payload }) => {
      state.sponsorsByCategories = payload.data.sponsors;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getSponsors, setSponsors, setError } = sponsorSlice.actions;

export const sponsorSelector = (state) => state.sponsor;

export default sponsorSlice.reducer;

export const fetchSponsors = (url) => {
  return async (dispatch) => {
    dispatch(getSponsors());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/sponsors`);
      const res = await response.json();
      dispatch(setSponsors(res));
      dispatch(incrementLoadedSection());
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError(error.message));
    }
  };
};
