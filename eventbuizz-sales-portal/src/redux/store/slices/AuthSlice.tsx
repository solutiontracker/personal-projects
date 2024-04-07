import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'
import type { PayloadAction } from '@reduxjs/toolkit'
import type { RootState } from '@/redux/store/store'
import axios from 'axios'
import { authHeader, guestHeader, handleErrorResponse } from '@/helpers'
import { LOGIN_ENDPOINT, LOGOUT_ENDPOINT, PASSWORD_REQUEST_ENDPOINT, PASSWORD_RESET_ENDPOINT, PASSWORD_VERIFY_ENDPOINT } from '@/constants/endpoints'

// Slice Thunks
export const loginUser = createAsyncThunk(
  'users/login',
  async (data:any , { signal }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    const response = await axios.post(LOGIN_ENDPOINT, data,{
      cancelToken: source.token,
      headers: guestHeader(),
    })
    return response.data
  }
)

// Slice Thunks
export const logOutUser = createAsyncThunk(
  'users/logOut',
  async (data:any , { signal, dispatch, rejectWithValue }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    try {
      const response = await axios.post(LOGOUT_ENDPOINT, data,{
        cancelToken: source.token,
        headers: authHeader('GET'),
      })
      return response.data
      
    } catch (err:any) {
      if (!err.response) {
        throw err
      }
      if(err.response.status !== 200){
        handleErrorResponse(err.response.status, dispatch);
      }
        // Return the known error for future handling
      return rejectWithValue(err.response.status);
    }
  }
)

export const forgotPasswordRequest = createAsyncThunk(
  'forgotPassword/request',
  async (data:any , { signal }) => {
    
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    const response = await axios.post(PASSWORD_REQUEST_ENDPOINT, data,{
      cancelToken: source.token,
      headers: guestHeader(),
    })
    return response.data
  }
)

export const forgotPasswordVerify = createAsyncThunk(
  'forgotPassword/verify',
  async (data:any , { signal }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    const response = await axios.post(PASSWORD_VERIFY_ENDPOINT, data,{
      cancelToken: source.token,
      headers: guestHeader(),
    })
    return response.data
  }
)

export const forgotPasswordReset = createAsyncThunk(
  'forgotPassword/reset',
  async (data:any , { signal }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    const response = await axios.post(PASSWORD_RESET_ENDPOINT, data,{
      cancelToken: source.token,
      headers: guestHeader(),
    })
    return response.data
  }
)

// Define a type for the slice state
interface AuthState {
  user: any,
  loading:boolean,
  redirect:null|string,
  error:any,
  successMessage:any,
  errors:any,
  forgetPasswordEmail:null|string,
  forgetPasswordToken:null|string,
  forgetPasswordTokenSuccess:boolean,
}

let authInfo =
    typeof window !== "undefined" && localStorage.getItem("agent");
const initialUser =
    authInfo && authInfo !== undefined ? JSON.parse(authInfo) : null;

// console.log(authInfo);

// Define the initial state using that type
const initialState: AuthState = {
  user: initialUser,
  loading:false,
  redirect:null,
  error:null,
  errors:null,
  forgetPasswordEmail:null,
  forgetPasswordToken:null,
  forgetPasswordTokenSuccess:false,
  successMessage:null,
}

export const authUserSlice = createSlice({
  name: 'authUser',
  // `createSlice` will infer the state type from the `initialState` argument
  initialState,
  reducers: {
    setAuthUser: (state, action: PayloadAction<any>) => {
      localStorage.setItem('agent', JSON.stringify(action.payload));
      state.user = action.payload;
    },
    removeAuthUser: (state, action: PayloadAction<any>) => {
      localStorage.removeItem('agent');
      localStorage.removeItem('eventsRequestData');
      localStorage.removeItem('ordersRequestData');
      state.user = null;
    },
    setRedirect: (state, action: PayloadAction<any>) => {
      state.redirect = action.payload;
    },
    setForgetPasswordEmail: (state, action: PayloadAction<any>) => {
      state.forgetPasswordEmail = action.payload;
    },
    setForgetPasswordToken: (state, action: PayloadAction<any>) => {
      state.forgetPasswordToken = action.payload;
      state.forgetPasswordTokenSuccess = true;
    },
    setLoading: (state, action: PayloadAction<any>) => {
      state.loading = action.payload;
    },
    clearErrors: (state) => {
      state.error = null;
      state.errors = null;
    },
  },
  extraReducers: (builder) => {
    // Login thuckCases
    builder.addCase(loginUser.pending, (state, action) => {
      state.loading = true;
      state.redirect = null;
      state.forgetPasswordEmail = null;
      state.forgetPasswordToken = null;
      state.forgetPasswordTokenSuccess = false;
      state.user = null;
      state.error = null;
      state.successMessage = null;
    }),
    builder.addCase(loginUser.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        localStorage.setItem('agent', JSON.stringify(res.data.agent));
        state.user = res.data.agent;
      }else{
        if(Array.isArray(res.message)){
          state.errors = res.message;
        }else{
          state.error = res.message;
        }
      }
      state.loading = false;
    }),
    builder.addCase(loginUser.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.error = "Network Error";
      state.loading = false;
    }),
    // LogOut thuckCases
    builder.addCase(logOutUser.pending, (state, action) => {
      state.loading = true;
      state.redirect = null;
      state.forgetPasswordEmail = null;
      state.forgetPasswordToken = null;
      state.forgetPasswordTokenSuccess = false;
      state.user = null;
      state.error = null;
      state.successMessage = null;
    }),
    builder.addCase(logOutUser.fulfilled, (state, action) => {
      state.loading = false;
      localStorage.removeItem('agent');
      localStorage.removeItem('eventsRequestData');
      localStorage.removeItem('ordersRequestData');
    }),
    builder.addCase(logOutUser.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.error = "Network Error";
      state.loading = false;
    }),
    // 
    // 
    // forgotPassworRequest thuckCases
    builder.addCase(forgotPasswordRequest.pending, (state, action) => {
      state.loading = true;
      state.redirect = null;
      state.user = null;
      state.error = null;
      state.successMessage = null;
    }),
    builder.addCase(forgotPasswordRequest.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        state.redirect = "/auth/forgot-password/verify";
      }else{
        if(Array.isArray(res.message)){
          state.errors = res.message;
        }else{
          state.error = res.message;
        }
        state.loading = false;
      }
    }),
    builder.addCase(forgotPasswordRequest.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.error = "Network Error";
      state.loading = false;
    }),
    // 
    // 
    // forgotPassworVerify thuckCases
    builder.addCase(forgotPasswordVerify.pending, (state, action) => {
      state.loading = true;
      state.redirect = null;
      state.user = null;
      state.error = null;
      state.successMessage = null;
    }),
    builder.addCase(forgotPasswordVerify.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        state.redirect = '/auth/forgot-password/reset';
        state.forgetPasswordToken = action.payload.data.resetCode;
        state.forgetPasswordEmail = action.payload.data.email;
        state.forgetPasswordTokenSuccess = true;
      }else{
        if(Array.isArray(res.message)){
          state.errors = res.message;
        }else{
          state.error = res.message;
        }
        state.loading = false;
      }
      // state.loading = false;
    }),
    builder.addCase(forgotPasswordVerify.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.error = "Network Error";
      state.loading = false;
    }),
    // forgotPassworReset thuckCases
    builder.addCase(forgotPasswordReset.pending, (state, action) => {
      state.loading = true;
      state.redirect = null;
      state.user = null;
      state.error = null;
      state.successMessage = null;
    }),
    builder.addCase(forgotPasswordReset.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        state.redirect = '/auth/login';
        state.forgetPasswordEmail = null;
        state.forgetPasswordToken = null;
        state.forgetPasswordTokenSuccess = false;
        state.user = null;
        state.error = null;
        state.successMessage = res.message;
      }else{
        state.user = null;
        state.errors = res.message ? res.message : ['Something went wrong'];
      }
      // state.loading = false;
    }),
    builder.addCase(forgotPasswordReset.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.error = "Network Error";
      state.loading = false;
    })
  },
})


export const { setAuthUser, setRedirect, setForgetPasswordEmail, setForgetPasswordToken, setLoading, removeAuthUser, clearErrors } = authUserSlice.actions

// Other code such as selectors can use the imported `RootState` type
export const selectUser = (state: RootState) => state.authUser.user

export default authUserSlice.reducer