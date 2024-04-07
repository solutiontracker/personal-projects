import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  speakers: null,
  labels:null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
  currentPage: 1,
};

export const speakerSlice = createSlice({
  name: "speaker",
  initialState,
  reducers: {
    getSpeakers: (state, {payload}) => {
      state.loading = true;
      state.currentPage = payload.page;
      if(payload.mount){
       state.speakers = null;
      }
    },
    setSpeakers: (state, { payload }) => {
      state.speakers = state.currentPage > 1 ? [...state.speakers, ...payload.data]: payload.data;
      state.labels = state.labels;
      state.total = payload.meta.total;
      state.totalPages = Math.ceil(payload.meta.total / payload.meta.per_page);
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getSpeakers, setSpeakers, setError } = speakerSlice.actions;

export const speakerSelector = (state) => state.speaker;

export default speakerSlice.reducer;

export const fetchSpeakers = (url, page, limit, search, mount, home) => {
  return async (dispatch) => {
    dispatch(getSpeakers({page, mount}));
    let endPoint = `/event/${url}/speakers?page=${page}&limit=${limit}`;
    if (search !== "") {
      endPoint = `/event/${url}/speakers?query=${search}&page=${page}&limit=${limit}`;
    }
    if (home === true) {
      endPoint = `/event/${url}/speakers?page=${page}&limit=${limit}&home=true`;
    }
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setSpeakers(res));
      if (mount) {
        dispatch(incrementLoadedSection());
        dispatch(incrementFetchLoadCount());
      }
    } catch (error) {
      dispatch(setError());
    }
  };
};
