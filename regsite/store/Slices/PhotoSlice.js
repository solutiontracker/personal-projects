import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection , incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  photos: null,
  labels: null,
  loading: false,
  error: null,
  total: null,
  totalPages: null,
  currentPage: 1,
};

export const photoSlice = createSlice({
  name: "photo",
  initialState,
  reducers: {
    getPhotos: (state, {payload}) => {
      state.loading = true;
      state.currentPage = payload.page;
      if(payload.mount){
       state.photos = null;
      }
    },
    setPhotos: (state, { payload }) => {
      state.photos = state.currentPage > 1 ? [...state.photos, ...payload.data]: payload.data;
      state.total = payload.meta.total;
      state.labels = payload.labels
      state.totalPages = Math.ceil(payload.meta.total / payload.meta.per_page);
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
        state.photos = null;
        state.total = null;
        state.labels = null
        state.totalPages = null;
        state.loading = false;
        state.error = null;
      },
  },
});

// Action creators are generated for each case reducer function
export const { getPhotos, setPhotos, setError, clearAll } = photoSlice.actions;

export const photoSelector = (state) => state.photo;

export default photoSlice.reducer;

export const fetchPhotos = (url, page, limit, home) => {
    return async (dispatch) => {
    dispatch(getPhotos({page}));
    let endPoint = `/event/${url}/photos?page=${page}&limit=${limit}`;
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}${endPoint}`);
      const res = await response.json();
      dispatch(setPhotos(res));
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