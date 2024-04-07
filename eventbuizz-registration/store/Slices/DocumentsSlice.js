import { createSlice } from "@reduxjs/toolkit";
import { incrementFetchLoadCount } from "./GlobalSlice";
const initialState = {
  documents: null,
  labels: null,
  loading: false,
  error: null,
};

export const documentsSlice = createSlice({
  name: "documents",
  initialState,
  reducers: {
    getDocuments: (state, {payload}) => {
      state.loading = true;
    },
    setDocuments: (state, { payload }) => {
      state.documents = payload.data.documents;
      state.labels = payload.labels;
      state.loading = false;
    },
    setError: (state, { payload }) => {
      state.error = payload;
    },
  },
});

// Action creators are generated for each case reducer function
export const { getDocuments, setDocuments, setError } = documentsSlice.actions;

export const documentsSelector = (state) => state.documents;

export default documentsSlice.reducer;

export const fetchDocuments = (url, event_id) => {
  return async (dispatch) => {
    dispatch(getDocuments());    
    try {
      const user_data = JSON.parse(localStorage.getItem(`event${event_id}User`)); ;
      const attendee_id = user_data ? user_data.user.id : 0;
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/documents?attendee=${attendee_id}`);
      const res = await response.json();
      dispatch(setDocuments(res));
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError(error.message));
    }
  };
};
