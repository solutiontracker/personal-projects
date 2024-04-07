import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  exhibitors: null,
  exhibitorCategories:null,
  labels: null,
  loading: false,
  error: null,
};

export const exhibitorListingSlice = createSlice({
  name: "exhibitorListing",
  initialState,
  reducers: {
    getExhibitors: (state, {payload}) => {
      state.loading = true;
    },
    setExhibitors: (state, { payload }) => {
      state.exhibitors = payload.data.exhibitors;
      state.labels = payload.labels;
      state.exhibitorCategories = payload.data.exhibitorCategories;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll:(state, {payload})=>{
      state.exhibitors = null;
      state.labels = null;
      state.exhibitorCategories = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getExhibitors, setExhibitors, setError, clearAll } = exhibitorListingSlice.actions;

export const exhibitorListingSelector = (state) => state.exhibitorListing;

export default exhibitorListingSlice.reducer;

export const fetchExhibitors = (url) => {
  return async (dispatch) => {
    dispatch(getExhibitors());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/exhibitors-listing`);
      const res = await response.json();
      dispatch(setExhibitors(res));
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

