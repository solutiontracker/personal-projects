import { createSlice } from '@reduxjs/toolkit'
import { header } from 'helpers/header'
import axios from 'axios'
const initialState = {
  newsletter: null,
  loading:false,
  updating:false,
  error:null,
  alert:null,
}

export const eventSlice = createSlice({
  name: 'newsletter',
  initialState,
  reducers: {
    getNewsletterData : (state) => {
      state.loading = true,
      state.newsletter= null,
      state.updating=false,
      state.error=null,
      state.alert=null
    },
    setNewsletterData: (state, { payload}) => {
        state.newsletter = payload,
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
export const { getNewsletterData, setNewsletterData, setError, setAlert, setUpdating } = eventSlice.actions

export const newsLetterSelector = state => state.newsletter

export default eventSlice.reducer

export const fetchNewsletterData = (id, url) => {
    return async dispatch => {
      dispatch(getNewsletterData())
      try {
        const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/newsletter-subscription`, { headers:header("GET", id)})
        const res = await response.json()
        dispatch(setNewsletterData(res.data))
      } catch (error) {
        dispatch(setError(error))
      }
    }
  }
export const updateNewsLetterData = (id, url, data) => {
    return async dispatch => {
      dispatch(setUpdating(true));
      try {
        const response = await axios.put(`${process.env.NEXT_APP_URL}/event/${url}/update-newsletter-subscription`, data, { headers:header("POST", id)})
        dispatch(setAlert(response.data))
        dispatch(setUpdating(false));
    } catch (error) {
      dispatch(setError(error))
      dispatch(setUpdating(false));
      }
    }
  }