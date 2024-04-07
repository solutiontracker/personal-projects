import { createSlice } from '@reduxjs/toolkit'

import { header } from 'helpers/header'

import axios from 'axios'

const initialState = {
  subRegistration: null,
  loading: false,
  updating:false,
  error: null,
  alert: null,
  skip: false,
}

export const eventSlice = createSlice({

  name: 'subRegistration',

  initialState,

  reducers: {
    getSubRegistrationData: (state) => {
      state.loading = true,
      state.subRegistration = null,
      state.updating = false,
      state.error = null,
      state.alert = null
    },
    setSubRegistrationData: (state, { payload }) => {
      state.subRegistration = payload,
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
    setSkip: (state) => {
      state.skip = true
    },
  },

})

// Action creators are generated for each case reducer function
export const { getSubRegistrationData, setSubRegistrationData, setError, setAlert, setSkip, setUpdating } = eventSlice.actions

export const subRegistrationSelector = state => state.subRegistration

export default eventSlice.reducer

export const fetchSubRegistrationData = (id, url) => {

  return async dispatch => {
    dispatch(getSubRegistrationData())
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/sub-registration-after-login`, { headers: header("GET", id) })
      const res = await response.json()
      console.log(res);
      if (res.data.questions == undefined || res.data.questions.question.length <= 0 || res.data.settings['show_sub_registration_on_web_app'] === 0) {
        localStorage.setItem(`${url}_sub_reg_skip`, 'true');
        dispatch(setSkip());
      }
      dispatch(setSubRegistrationData(res.data))
    } catch (error) {
      dispatch(setError("Couldn't fetch Subregistration"));
    }
  }
}

export const updateSubRegistrationData = (id, url, data) => {

  return async dispatch => {
    dispatch(setUpdating(true));
    dispatch(setAlert(null))
    try {
      const response = await axios.post(`${process.env.NEXT_APP_URL}/event/${url}/save-sub-registration`, data, { headers: header("POST", id) })
      if (response.data.data.status || response.data.data.message == "Change answer date ended") {
        localStorage.setItem(`${url}_sub_reg_skip`, 'true');
        dispatch(setSkip());
      }
      dispatch(setUpdating(false));
      dispatch(setAlert(response.data.data.message))
    } catch (error) {
      dispatch(setUpdating(false));
      dispatch(setError("Couldn't update Subregistration"));
    }
  }

}
