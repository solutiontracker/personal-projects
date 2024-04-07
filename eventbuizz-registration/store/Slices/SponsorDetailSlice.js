import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  sponsor: null,
  documents: null,
  labels: null,
  loading: false,
  error: null,
};

export const sponsorDetailSlice = createSlice({
  name: "sponsorDetail",
  initialState,
  reducers: {
    getSponsor: (state, {payload}) => {
      state.loading = true;
    },
    setSponsor: (state, { payload }) => {
      state.sponsor = payload.data.sponsor;
      state.documents = payload.data.documents;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.sponsor = null;
      state.documents = null;
      state.labels = null;
      state.loading = false;
      state.error = null;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getSponsor, setSponsor, setError, clearAll } = sponsorDetailSlice.actions;

export const sponsorDetailSelector = (state) => state.sponsorDetail;

export default sponsorDetailSlice.reducer;

export const fetchSponsor = (url, sponsor_id) => {
  return async (dispatch) => {
    dispatch(getSponsor());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/sponsor-detail/${sponsor_id}`);
      const res = await response.json();
      dispatch(setSponsor(res));
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
