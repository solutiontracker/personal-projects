import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  videos: null,
  labels: null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
  currentPage: 1,
};

export const videoSlice = createSlice({
  name: "video",
  initialState,
  reducers: {
    getVideos: (state, {payload}) => {
      state.loading = true;
      state.currentPage = payload.page;
      if(payload.mount){
       state.videos = null;
      }
    },
    setVideos: (state, { payload }) => {
      state.videos = state.currentPage > 1 ? [...state.videos, ...payload.data]: payload.data;
      state.total = payload.meta.total;
      state.labels = payload.labels
      state.totalPages = Math.ceil(payload.meta.total / payload.meta.per_page);
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
        state.videos = null;
        state.total = null;
        state.labels = null
        state.totalPages = null;
        state.loading = false;
        state.error = null;
      },
  },
});

// Action creators are generated for each case reducer function
export const { getVideos, setVideos, setError, clearAll } = videoSlice.actions;

export const videoSelector = (state) => state.video;

export default videoSlice.reducer;

export const fetchVideos = (url, page, limit, home) => {
    return async (dispatch) => {
    dispatch(getVideos({page}));
    let endPoint = `/event/${url}/videos?page=${page}&limit=${limit}`;
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setVideos(res));
      if(home){
          dispatch(incrementLoadedSection());
      }
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