import { createSlice } from "@reduxjs/toolkit";
import { header } from "helpers/header";
import axios from "axios";
const initialState = {
  userData: null,
  loading: false,
  authenticationId:null,
  redirect:null,
  email:null,
  provider:"email",
  attendee:null,
  ms:null,
  error: null,
  loggedout:false,
  enabel_cancel:false,
};

export const userSlice = createSlice({
  name: "user",
  initialState,
  reducers: {
    setLoading: (state) => {
      state.loading = true;
      state.error = false;
    },
    setEmail: (state, {payload}) => {
      state.email = payload;
    },
    setLoginData: (state, { payload }) => {
      state.userData = payload.data;
      state.redirect = payload.redirect;
      state.loading = false;
      state.error = false;
    },
    setForgotPasswordCode: (state, { payload }) => {
      state.authenticationId = payload.data.authentication_id;
      state.redirect = payload.redirect;
      state.loading = false;
      state.error = false;
    },
    setAttendeeData: (state, { payload }) => {
      state.authenticationId = payload.data.authentication_id;
      state.attendee = payload.data;
      state.error = false;
    },
    setAuthId: (state, { payload }) => {
      state.authenticationId = payload.data.authentication_id;
      state.redirect = payload.redirect;
      state.ms = payload.data.ms;
      state.error = false;
      state.loading= false;
    },
    setRedirect: (state, { payload }) => {
      state.redirect = payload.redirect;
      state.loading = false;
    },
    setProvider: (state, { payload }) => {
      state.provider = payload;
    },
    setMs: (state) => {
      state.ms = null;
    },
    setError: (state, { payload }) => {
      state.error = payload;
      state.loading = false;
    },
    setLoggedOut: (state, { payload }) => {
      state.loggedout = payload;
    },
    setEnableCancel: (state, { payload }) => {
      state.enabel_cancel = payload;
    },
    reset: () => initialState
  },
});

// Action creators are generated for each case reducer function
export const { setLoading, setLoginData, setError, setForgotPasswordCode, setEnableCancel, setAttendeeData, setAuthId, setEmail, setRedirect, setProvider, setMs, reset, setLoggedOut } = userSlice.actions;

export const userSelector = (state) => state.user;

export default userSlice.reducer;

export const logUserIn = (id, url, data) => {
    return async (dispatch) => {
    dispatch(setLoading());
    dispatch(setEmail(data.email));
    try {
      const response = await axios.post(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/login`, data);
      if(response.data.success){
        if(response.data.redirect === "choose-provider"){
          dispatch(setForgotPasswordCode(response.data));
        }else if(response.data.redirect === "verification"){
          dispatch(setAuthId(response.data));
        }
        else if(response.data.redirect === "dashboard"){
          localStorage.setItem(`event${id}User`, JSON.stringify(response.data.data));
          localStorage.setItem(`event${id}UserLogged`, true);

          dispatch(setLoginData(response.data));
        }else{
          dispatch(setError("Something went wrong...."));
        }
      }
      else{
        dispatch(setError(response.data.message));
      }
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};
export const resetPasswordRequest = (id, url, data) => {
    return async (dispatch) => {
    dispatch(setLoading());
    dispatch(setEmail(data));
    try {
      const response = await axios.post(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/password/email`, {email:data, url});
      if(response.data.success){
        if(response.data.redirect === "choose-provider"){
        dispatch(setForgotPasswordCode(response.data));
      }else if(response.data.redirect === "verification"){
        dispatch(setAuthId(response.data));
      }else{
        dispatch(setError("Something went wrong...."));
      }
    }
    else{
      dispatch(setError(response.data.message));
    }
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};
export const getAttendeeData = (id, url, data) => {
  return async (dispatch) => {
    try {
      const response = await axios.get(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/verification/${data}`);
      if(response.data.success){
        dispatch(setAttendeeData(response.data));
      }else{
      dispatch(setError(response.data.message));
      }
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};
export const verify = (id, screen, provider, code, url, authentication_id) => {
  return async (dispatch) => {
    dispatch(setLoading());
    dispatch(setMs());
    try {
      const response = await axios.post(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/verification/${authentication_id}`, { screen, provider, code, url, authentication_id});
      if(response.data.success){
        if(screen === "choose-provider"){
        dispatch(setProvider(provider));
        dispatch(setAuthId(response.data));
      }
      else if(screen === "verification"){
        dispatch(setLoginData(response.data));
        localStorage.setItem(`event${id}User`, JSON.stringify(response.data.data));
        localStorage.setItem(`event${id}UserLogged`, true);
      }
    }else{
      dispatch(setError(response.data.message));
      }
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};
export const resetPassword = (id, url, data) => {
  return async (dispatch) => {
    dispatch(setLoading());
    try {
      const response = await axios.post(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/password/reset`, {...data, url});
      if(response.data.success){
        dispatch(setRedirect(response.data));
      }else{
        dispatch(setError(response.data.message));
      }
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};

export const logOut = (id, url, success) => {
  return async (dispatch) => {

    try {
      const response = await axios.post(`${process.env.NEXT_APP_AUTH_URL}event/${url}/auth/logout`, null ,{ headers:header("POST", id)});
        localStorage.removeItem(`event${id}User`);
        localStorage.removeItem(`event${id}UserLogged`);
        localStorage.removeItem(`${url}_sub_reg_skip`);
        localStorage.removeItem(`EI${url}EC`);
        localStorage.removeItem(`EI${url}EC_COUNT`);
        dispatch(setLoggedOut(true));
        dispatch(reset(true));
        success();
    } catch (error) {
      if(error.response.data.message){
        dispatch(setError(error.response.data.message));
      }else{
        dispatch(setError(error.message));
      }
    }
  };
};
