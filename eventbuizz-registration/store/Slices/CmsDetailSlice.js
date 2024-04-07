import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  cmsPage: null,
  labels: null,
  loading: false,
  error: null,
};

export const cmsDetailSlice = createSlice({
  name: "cmsDetail",
  initialState,
  reducers: {
    getCms: (state) => {
      state.loading = true;
    },
    setCms: (state, { payload }) => {
      state.cmsPage = payload.data;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.cmsPage = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getCms, setCms, setError, clearAll } = cmsDetailSlice.actions;

export const cmsDetailSelector = (state) => state.cmsDetail;

export default cmsDetailSlice.reducer;

export const fetchCmsPage = (url, module_name, cms_id) => {
  return async (dispatch) => {
    dispatch(getCms());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/${module_name}/page/${cms_id}`);
      const res = await response.json();
      dispatch(setCms(res));
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
