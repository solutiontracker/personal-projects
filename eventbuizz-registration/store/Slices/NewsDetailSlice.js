import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  news: null,
  loading: false,
  labels:null,
  error: null,
};

export const newsDetailSlice = createSlice({
  name: "newsDetail",
  initialState,
  reducers: {
    getNews: (state, {payload}) => {
      state.loading = true;
      state.news = null;
    },
    setNews: (state, { payload }) => {
      state.news = payload.data;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.news = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getNews, setNews, setError, clearAll } = newsDetailSlice.actions;

export const newsDetailSelector = (state) => state.newsDetail;

export default newsDetailSlice.reducer;

export const fetchNewsDetail = (url,  id) => {
    return async (dispatch) => {
    dispatch(getNews());
    let endPoint = `/event/${url}/news/${id}/detail`; 
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setNews(res));
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