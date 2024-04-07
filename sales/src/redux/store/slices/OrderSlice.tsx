import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import type { PayloadAction } from '@reduxjs/toolkit'
import type { RootState } from '@/redux/store/store'
import axios from 'axios'
import { AGENT_ENDPOINT, AGENT_EVENTS_ENDPOINT } from '@/constants/endpoints'
import { authHeader, handleErrorResponse } from '@/helpers'


// Slice Thunks
export const userEventOrderInvoice = createAsyncThunk(
  'users/EventOrderInvoice',
  async (data:any , { signal, dispatch, rejectWithValue }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    try {
      const response = await axios.post(`${AGENT_EVENTS_ENDPOINT}/${data.event_id}/orders/${data.order_id}/invoice`,data, {
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
// change payment status
export const userEventOrderChangePymentStatus = createAsyncThunk(
  'users/EventOrderChangePymentStatus',
  async (data:any , { signal, dispatch, rejectWithValue }) => {
    const source = axios.CancelToken.source()
    signal.addEventListener('abort', () => {
      source.cancel()
    })
    try {
      const response = await axios.post(`${AGENT_ENDPOINT}/billing/change-payment-status/${data.order_id}`,data, {
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

// Define a type for the slice state
interface EventState {
  order:any,
  invoice:any,
  loading:boolean,
  error:any,
}


// Define the initial state using that type
const initialState: EventState = {
  order: null,
  invoice: null,
  loading:false,
  error:null,
}

export const orderSlice = createSlice({
  name: 'order',
  // `createSlice` will infer the state type from the `initialState` argument
  initialState,
  reducers: {
    setLoading: (state, action: PayloadAction<boolean>) => {
      state.loading = action.payload;
    },
  },
  extraReducers: (builder) => {
    // Login thuckCases
    builder.addCase(userEventOrderInvoice.pending, (state, action) => {
      state.loading = true;
      state.invoice = null;
    }),
    builder.addCase(userEventOrderInvoice.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        state.invoice = action.payload.data.invoice;
      }else{
          state.error = res.message;
      }
      state.loading = false;
    }),
    builder.addCase(userEventOrderInvoice.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.loading = false;
    }),
    // Login thuckCases
    builder.addCase(userEventOrderChangePymentStatus.pending, (state, action) => {
      state.loading = true;
      state.invoice = null;
    }),
    builder.addCase(userEventOrderChangePymentStatus.fulfilled, (state, action) => {
      let res = action.payload;
      if(res.success){
        state.invoice = action.payload.data.invoice;
      }else{
          state.error = res.message;
      }
      state.loading = false;
    }),
    builder.addCase(userEventOrderChangePymentStatus.rejected, (state, action) => {
      console.log("rejected", action.payload);
      state.loading = false;
    })
  },
})


export const { setLoading } = orderSlice.actions

// Other code such as selectors can use the imported `RootState` type
export const selectOrder = (state: RootState) => state.order

export default orderSlice.reducer