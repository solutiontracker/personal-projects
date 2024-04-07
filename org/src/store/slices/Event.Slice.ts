import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { Module } from '@src/models/Module'
import type { RootState } from '@src/store/Index'

export interface EventPayload {
  limit: number;
  page: number;
  action: string;
  sort_by: string;
  order_by: string;
  query: string
}
export interface EventState {
    modules: Array<Module>,
    processing: boolean
}

const initialState: EventState = {
  modules: [],
  processing: true
}

// Slice
export const EventSlice = createSlice({
  name: 'event',
  initialState,
  reducers: {
    loadModules(state, action: PayloadAction<EventPayload>) {
      state.processing = true
    },
    success(state, action: PayloadAction<Module>) {
      state.processing = false;
      state.modules = action.payload;
    },
    updateModules(state, action: PayloadAction<Array<Module>>) {
      state.modules = action.payload
    },
  },
})

// Actions
export const EventActions = {
  loadModules: EventSlice.actions.loadModules,
  updateModules: EventSlice.actions.updateModules,
  success: EventSlice.actions.success,
  
}

// Selectors
export const Modules = (state: RootState) => state.event.modules;
export const isProcessing = (state: RootState) => state.event.processing;

// Reducer
export default EventSlice.reducer