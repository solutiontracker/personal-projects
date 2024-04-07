import { createSlice } from '@reduxjs/toolkit'
import { header } from 'helpers/header'
const initialState = {
  surveyListAnswered: null,
  surveyList: null,
  loading:false,
  error:null,
  alert:null,
}

export const eventSlice = createSlice({
  name: 'surveyList',
  initialState,
  reducers: {
    getSurveyListData : (state) => {
      state.loading = true
    },
    setSurveyListData: (state, { payload}) => {
        state.surveyList = payload.filter((survey)=>(!survey.complete_answered)),
        state.surveyListAnswered = payload.filter((survey)=>(survey.complete_answered)),
        state.loading = false
    },
    setError: (state, { payload }) => {
      state.error = payload
    },
    setAlert: (state, { payload }) => {
      state.alert = payload
    },
  },
})

// Action creators are generated for each case reducer function
export const { getSurveyListData, setSurveyListData, setError, setAlert } = eventSlice.actions

export const surveyListSelector = state => state.surveyList

export default eventSlice.reducer

export const fetchSurveyListData = (id, url) => {
    return async dispatch => {
      dispatch(getSurveyListData())
      try {
        const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/survey-listing?ALL_SURVEYS=1`, { headers:header("GET", id)})
        const res = await response.json()
        dispatch(setSurveyListData(res.data))
      } catch (error) {
        dispatch(setError(error))
      }
    }
  }