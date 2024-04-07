import { createSlice } from '@reduxjs/toolkit'
import { header } from 'helpers/header'
import axios from 'axios'
const initialState = {
  surveyDetail: null,
  surveyResult: null,
  survey: null,
  loading:false,
  updating:false,
  error:null,
  alert:null,
}

export const eventSlice = createSlice({
  name: 'survey',
  initialState,
  reducers: {
    getSurveyData : (state) => {
      state.loading = true
      state.updating=false,
      state.surveyDetail= null,
      state.surveyResult= null,
      state.survey= null,
      state.error=null,
      state.alert=null
    },
    setSurveyData: (state, { payload}) => {
        state.surveyDetail = payload.survey_details,
        state.surveyResult = payload.survey_result,
        state.survey = payload.survey,
        state.loading = false
    },
    setUpdating:(state, { payload})=>{
      state.updating = payload
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
export const { getSurveyData, setSurveyData, setError, setAlert, setUpdating } = eventSlice.actions

export const surveySelector = state => state.survey

export default eventSlice.reducer

export const fetchSurveyData = (id, url,survey_id) => {
    return async dispatch => {
      dispatch(getSurveyData())
      try {
        const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/survey-detail/${survey_id}`, { headers:header("GET", id)})
        const res = await response.json()
        dispatch(setSurveyData(res.data))
      } catch (error) {
        dispatch(setError(error))
      }
    }
  }
export const updateSurveyData = (id, url, survey_id, data, success) => {
    return async dispatch => {
      console.log(data);
      dispatch(setUpdating(true))
      try {
        const response =  await axios.post(`${process.env.NEXT_APP_API_GATEWAY_URL}/v2/save-surveys`, data, { headers:header("POST", id)})
        dispatch(setAlert("Answers Successfully Updated"));
        success();
        dispatch(setUpdating(false));
      } catch (error) {
        dispatch(setError("Couldn't update Subregistration"));
        dispatch(setUpdating(false));
      }
    }
  }