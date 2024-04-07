import { createSlice } from '@reduxjs/toolkit'
import { incrementLoadedSection, incrementFetchLoadCount } from "./GlobalSlice";

// import { setLoadCount } from './GlobalSlice'
const initialState = {
  packages: null,
  package_currency: null,
  loading: false,
  error: null,
}

export const formPackageSlice = createSlice({
  name: 'formPackages',
  initialState,
  reducers: {
    getFormPackges: (state) => {
      state.loading = true
    },
    setFormPackages: (state, { payload }) => {
      state.packages = payload.data
      state.package_currency = payload.currency
      state.loading = false
    },
    setError: (state, { payload }) => {
      state.error = payload
    },
  },
})

// Action creators are generated for each case reducer function
export const { getFormPackges, setFormPackages, setError, } = formPackageSlice.actions

export const formPackageSelector = state => state.formPackages

export default formPackageSlice.reducer

export const fetchPackages = (url, layout=null) => {
  return async dispatch => {
    dispatch(getFormPackges())
    try {
      const response = await fetch(`${process.env.NEXT_APP_URL}/event/${url}/form-packages`)
      const res = await response.json()
      dispatch(setFormPackages(res))
      dispatch(incrementLoadedSection());
      dispatch(incrementFetchLoadCount());
    } catch (error) {
      dispatch(setError())
    }
  }
}





