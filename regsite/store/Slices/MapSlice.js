import { createSlice } from "@reduxjs/toolkit";
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  map: null,
  loading: false,
  error: null,
};

export const mapSlice = createSlice({
  name: "map",
  initialState,
  reducers: {
    getMap: (state) => {
      state.loading = true;
    },
    setMap: (state, { payload }) => {
      state.map = payload.length === 0 ? null : payload;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    
  },
});

// Action creators are generated for each case reducer function
export const {
  getMap,
  setMap,
  setError,
} = mapSlice.actions;

export const mapSelector = (state) => state.map;

export default mapSlice.reducer;

export const fetchMap = (url) => {
  return async (dispatch) => {
    dispatch(getMap());
    try {
      const response = await fetch(
        `${process.env.NEXT_APP_URL}/event/${url}/map`
      );
      const res = await response.json();
      dispatch(setMap(res.data));
      dispatch(incrementLoadedSection());
      dispatch(incrementFetchLoadCount());

    } catch (error) {
      dispatch(setError());
    }
  };
};
