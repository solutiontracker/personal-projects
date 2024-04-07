import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  news: null,
  labels:null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
  currentPage: 1,
};

export const newsSlice = createSlice({
  name: "news",
  initialState,
  reducers: {
    getNews: (state, {payload}) => {
      state.loading = true;
      state.currentPage = payload.page;
      if(payload.mount){
       state.news = null;
      }
    },
    setNews: (state, { payload }) => {
      state.news = state.currentPage > 1 ? [...state.news, ...payload.data]: payload.data;
      state.total = payload.meta.total;
      state.totalPages = Math.ceil(payload.meta.total / payload.meta.per_page);
      state.loading = false;
      state.labels = payload.labels;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getNews, setNews, setError } = newsSlice.actions;

export const newsSelector = (state) => state.news;

export default newsSlice.reducer;

export const fetchNews = (url, page, limit, mount) => {
    return async (dispatch) => {
    dispatch(getNews({page, mount}));
    let endPoint = `/event/${url}/news?page=${page}&limit=${limit}`;
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setNews(res));
      if (mount) {
        dispatch(incrementFetchLoadCount());
      }
    } catch (error) {
      dispatch(setError());
    }
  };
};
