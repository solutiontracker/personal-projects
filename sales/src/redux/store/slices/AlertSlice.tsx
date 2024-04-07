import { createSlice } from '@reduxjs/toolkit'
import type { PayloadAction } from '@reduxjs/toolkit'
import type { RootState } from '@/redux/store/store'

// Define a type for the slice state
type alert = {
  class: string,
  message: string,
  title: string,
  redirect: string,
  logged: boolean,
  success: boolean
}

interface AlertState {
  alert: alert | null ,
}




// Define the initial state using that type
const initialState: AlertState = {
  alert: null,
}

export const alertSlice = createSlice({
  name: 'alertUser',
  // `createSlice` will infer the state type from the `initialState` argument
  initialState,
  reducers: {
    setAlert: (state, action: PayloadAction<any>) => {
      state.alert = action.payload;
    },
  },
})


export const { setAlert } = alertSlice.actions

// Other code such as selectors can use the imported `RootState` type
export const selectUser = (state: RootState) => state.alert

export default alertSlice.reducer