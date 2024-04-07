import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  exhibitorsByCategories: null,
  labels: null,
  loading: false,
  error: null,
};

export const exhibitorSlice = createSlice({
  name: "exhibitor",
  initialState,
  reducers: {
    getExhibitors: (state, {payload}) => {
      state.loading = true;
    },
    setExhibitors: (state, { payload }) => {
      state.exhibitorsByCategories = payload.data.exhibitors;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getExhibitors, setExhibitors, setError } = exhibitorSlice.actions;

export const exhibitorSelector = (state) => state.exhibitor;

export default exhibitorSlice.reducer;

export const fetchExhibitors = (url) => {
  return async (dispatch) => {
    dispatch(getExhibitors());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/exhibitors`);
      const res = await response.json();
      dispatch(setExhibitors(res));
      dispatch(incrementLoadedSection());
      dispatch(incrementFetchLoadCount());

    } catch (error) {
      dispatch(setError(error.message));
    }
  };
};

