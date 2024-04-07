import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  sponsors: null,
  sponsorCategories:null,
  labels: null,
  loading: false,
  error: null,
};

export const sponsorListingSlice = createSlice({
  name: "sponsorListing",
  initialState,
  reducers: {
    getSponsors: (state, {payload}) => {
      state.loading = true;
    },
    setSponsors: (state, { payload }) => {
      state.sponsors = payload.data.sponsors;
      state.labels = payload.labels;
      state.sponsorCategories = payload.data.sponsorCategories;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state, { payload }) => {
      state.sponsors =null;
      state.labels = null;
      state.sponsorCategories = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getSponsors, setSponsors, setError, clearAll } = sponsorListingSlice.actions;

export const sponsorListingSelector = (state) => state.sponsorListing;

export default sponsorListingSlice.reducer;

export const fetchSponsors = (url) => {
  return async (dispatch) => {
    dispatch(getSponsors());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/sponsors-listing`);
      const res = await response.json();
      dispatch(setSponsors(res));
      setTimeout(()=>{
        dispatch(incrementFetchLoadCount());
      }, 50)
    } catch (error) {
      dispatch(setError(error.message));
    }
  };
};

export const clearState = () => {
  return async (dispatch) => {
    dispatch(clearAll());    
  };
};
