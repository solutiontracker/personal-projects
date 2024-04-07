import { createSlice } from '@reduxjs/toolkit'
// import { setLoadCount } from './GlobalSlice'
const initialState = {
  event: null,
  loading: false,
  error: null,
  cookie: "necessary",
  verification_id: null,
  validatettendee: null,
}

export const eventSlice = createSlice({
  name: 'event',
  initialState,
  reducers: {
    getEvent: (state) => {
      state.loading = true
    },
    setEvent: (state, { payload }) => {
      state.event = payload
      state.loading = false
    },
    setError: (state, { payload }) => {
      state.error = payload
    },
    setCookie: (state, { payload }) => {
      state.cookie = payload
    },
    setVerificationids: (state, { payload }) => {
      state.verification_id = payload.verification_id
      state.validateAttendee = payload.validateAttendee
    },
  },
})

// Action creators are generated for each case reducer function
export const { getEvent, setEvent, setError, setCookie, setVerificationids } = eventSlice.actions

export const eventSelector = state => state.event

export default eventSlice.reducer

export const fetchEvent = (url, layout=null) => {
  return async dispatch => {
    dispatch(getEvent())
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}?layout=${layout}`)
      const res = await response.json()
      dispatch(setEvent(res.data))
    } catch (error) {
      dispatch(setError())
    }
  }
}

export const updateCookie = (cookie, url) => {
  return async dispatch => {   
      dispatch(setCookie(cookie))
      localStorage.setItem(`cookie_${url}`, cookie);
  }
}



