import { createSlice } from '@reduxjs/toolkit'
import { header } from 'helpers/header'
import axios from 'axios'
const initialState = {
  keywords: null,
  loading:false,
  updating:false,
  error:null,
  alert:null,
}

export const eventSlice = createSlice({
  name: 'networkInterest',
  initialState,
  reducers: {
    getInterestKeywordsData : (state) => {
      state.loading = true,
      state.keywords= null,
      state.updating=false,
      state.error=null,
      state.alert=null
    },
    setInterestKeywordsData: (state, { payload}) => {
        state.keywords= payload,
        state.loading = false
    },
    setError: (state, { payload }) => {
      state.error = payload
    },
    setUpdating: (state, { payload }) => {
      state.updating = payload
    },
    setAlert: (state, { payload }) => {
      state.alert = payload
    },
  },
})

// Action creators are generated for each case reducer function
export const { getInterestKeywordsData, setInterestKeywordsData, setError, setAlert, setUpdating } = eventSlice.actions

export const interestSelector = state => state.networkInterest

export default eventSlice.reducer

export const fetchKeywordsData = (id, url) => {
    return async dispatch => {
      dispatch(getInterestKeywordsData())
      try {
        const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/network-interest`, { headers:header("GET", id)})
        const res = await response.json()
        dispatch(setInterestKeywordsData(res.data))
      } catch (error) {
        dispatch(setError(error))
      }
    }
  }
export const updateKeywordData = (id, url, data) => {
    return async dispatch => {
      dispatch(setUpdating(true));
      try {
        const response = await axios.put(`${process.env.NEXT_APP_URL}/event/${url}/update-network-interest`, {keywords:data}, { headers:header("POST", id)})
        dispatch(setAlert(response.data))
        dispatch(setUpdating(false));
      } catch (error) {
        dispatch(setError(error))
        dispatch(setUpdating(false));
      }
    }
  }