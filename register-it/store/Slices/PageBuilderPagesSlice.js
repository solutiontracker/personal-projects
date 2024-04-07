import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  page: null,
  labels: null,
  loading: false,
  error: null,
};

export const pageBuilderPageSlice = createSlice({
  name: "pageBuilderPage",
  initialState,
  reducers: {
    getPage: (state) => {
      state.loading = true;
    },
    setPage: (state, { payload }) => {
      state.page = payload.data;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.page = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getPage, setPage, setError, clearAll } = pageBuilderPageSlice.actions;

export const pageBuilderPageSelector = (state) => state.pageBuilderPage;

export default pageBuilderPageSlice.reducer;

export const fetchPage = (url, _id) => {
  return async (dispatch) => {
    dispatch(getPage());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/page/${_id}`);
      const res = await response.json();
      dispatch(setPage(res));
      dispatch(incrementFetchLoadCount());
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
