import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  exhibitor: null,
  documents:null,
  labels: null,
  loading: false,
  error: null,
};

export const exhibitorDetailSlice = createSlice({
  name: "exhibitorDetail",
  initialState,
  reducers: {
    getExhibitor: (state, {payload}) => {
      state.loading = true;
    },
    setExhibitor: (state, { payload }) => {
      state.exhibitor = payload.data.exhibitor;
      state.documents = payload.data.documents;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
    clearAll: (state) => {
      state.exhibitor= null;
      state.documents=null;
      state.labels= null;
      state.loading= false;
      state.error= null;
    }
  },
});

// Action creators are generated for each case reducer function
export const { getExhibitor, setExhibitor, setError, clearAll } = exhibitorDetailSlice.actions;

export const exhibitorDetailSelector = (state) => state.exhibitorDetail;

export default exhibitorDetailSlice.reducer;

export const fetchExhibitor = (url, exhibitor_id) => {
  return async (dispatch) => {
    dispatch(getExhibitor());    
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/exhibitor-detail/${exhibitor_id}`);
      const res = await response.json();
      dispatch(setExhibitor(res));
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
