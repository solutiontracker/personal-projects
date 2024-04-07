import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  banner: null,
  banner_sort: null,
  settings: null,
  loading: false,
  error: null,
  loadCount: 0,
  loadedSections: 0,
  fetchLoadCount:0,
  showLogin:false,
  corporateLogin:false,
};

export const globalSlice = createSlice({
  name: "global",
  initialState,
  reducers: {
    getBanner: (state) => {
      state.loading = true;
    },
    setBanner: (state, { payload }) => {
      state.banner = payload.banner_top;
      state.banner_sort = payload.banner_sort;
      state.settings = payload.settings;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    setLoadCount: (state, { payload }) => {
      state.loadCount = payload;
    },
    incrementLoadedSection: (state) => {
      state.loadedSections = state.loadedSections + 1;
    },
    incrementLoadCount: (state) => {
      state.loadCount = state.loadCount + 1;
    },
    incrementLoadCountBy: (state, { payload }) => {
      state.loadCount = state.loadCount + payload;
    },
    setLoadedSections: (state, { payload }) => {
      state.loadedSections = payload;
    },
    setLSandLC: (state, { payload }) => {
      state.loadedSections = payload.ls;
      state.loadCount = payload.lc;
    },
    setShowLogin:(state, {payload})=>{
      state.showLogin = payload;
    },
    incrementFetchLoadCount: (state) => {
      state.fetchLoadCount = state.fetchLoadCount + 1;
    },
    setCorporateLogin: (state) => {
      state.corporateLogin = true;
    },
  },
});

// Action creators are generated for each case reducer function
export const {
  getBanner,
  setBanner,
  setError,
  setLoadCount,
  incrementLoadedSection,
  setLoadedSections,
  setLSandLC,
  incrementLoadCount,
  incrementLoadCountBy,
  setShowLogin,
  incrementFetchLoadCount,
  setCorporateLogin
} = globalSlice.actions;

export const globalSelector = (state) => state.global;

export default globalSlice.reducer;

export const fetchBanner = (url) => {
  return async (dispatch) => {
    dispatch(getBanner());
    try {
      const response = await fetch(
        `${process.env.NEXT_APP_URL}/event/${url}/banner`
      );
      const res = await response.json();
      dispatch(setBanner(res.data));
      dispatch(incrementLoadedSection());
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError());
    }
  };
};
export const postCorporateLogin = () => {
  return async (dispatch) => {
    dispatch(setCorporateLogin());
  };
};
