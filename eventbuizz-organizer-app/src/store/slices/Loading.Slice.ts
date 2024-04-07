import { createSlice, PayloadAction } from '@reduxjs/toolkit'

import type { RootState } from '@src/store/Index'

export interface EventState {
    loading: boolean;
}

const initialState: EventState = {
  loading: false,
}

// Slice
export const LoadingSlice = createSlice({
  name: 'loading',
  initialState,
  reducers: {
    set(state, action: PayloadAction<boolean>) {
      state.loading = action.payload
    },
  },
})

// Actions
export const LoadingActions = {
  set: LoadingSlice.actions.set,
}

// Selectors
export const isLoading = (state: RootState) => state.loading.loading;

// Reducer
export default LoadingSlice.reducer