import { createSlice, PayloadAction } from '@reduxjs/toolkit'

import type { RootState } from '@src/store/Index'

export interface EnvState {
    enviroment: string,
    api_base_url: string,
    msw_enabled: string
}

const initialState: EnvState = {
  enviroment: '',
  api_base_url: '',
  msw_enabled: ''
}

// Slice
export const EnvSlice = createSlice({
  name: 'env',
  initialState,
  reducers: {
    update(state, action: PayloadAction<EnvState>) {
      state.enviroment = action.payload.enviroment
      state.api_base_url = action.payload.api_base_url
      state.msw_enabled = action.payload.msw_enabled
    },
  },
})

// Actions
export const EnvActions = {
  update: EnvSlice.actions.update,
}

// Selectors
export const Env = (state: RootState) => state.env

// Reducer
export default EnvSlice.reducer