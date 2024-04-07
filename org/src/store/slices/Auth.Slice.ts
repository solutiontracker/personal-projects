import { GeneralResponse } from '@src/models/GeneralResponse';
import { RootState } from '@src/store/Index'
import { PayloadAction } from '@reduxjs/toolkit';
import { createSlice, createAction } from '@reduxjs/toolkit';

export interface LoginPayload {
    email: string;
    password: string;
    logged: boolean;
    app_type: string;

}

export interface PasswordResetPayload {
    email: string;
}

export interface LoadProviderPayload {
    id: number;
    screen: string;
}

export interface ChooseProviderPayload {
    id: number;
    provider: string;
    screen: string;
}

export interface ResetPayload {
    password: string;
    password_confirmation: string;
    token: string;
}

export interface VerificationPayload {
    id: number;
    authentication_id: number;
    code?: string;
    screen?: string;
    provider?: string;
}

export interface AuthState {
    isLoggedIn: boolean;
    processing?: boolean;
    response: GeneralResponse;
    error: string;
}

const initialState: AuthState = {
  isLoggedIn: false,
  processing: false,
  response: {},
  error: '',
};

const AuthSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    login(state, action: PayloadAction<LoginPayload>) {
      state.processing = true;
    },
    passwordReset(state, action: PayloadAction<PasswordResetPayload>) {
      state.processing = true;
    },
    chooseProvider(state, action: PayloadAction<ChooseProviderPayload>) {
      state.processing = true;
    },
    reset(state, action: PayloadAction<ResetPayload>) {
      state.processing = true;
    },
    verification(state, action: PayloadAction<VerificationPayload>) {
      state.processing = true;
    },
    loadProvider(state, action: PayloadAction<LoadProviderPayload>) {
      state.processing = true;
    },
    getUser(state) {
      state.processing = true;
    },
    success(state, action: PayloadAction<GeneralResponse>) {
      state.isLoggedIn = true;
      state.processing = false;
      state.response = action.payload;
      state.error = '';
    },
    failed(state, action: PayloadAction<string>) {
      state.processing = false;
      state.error = action.payload;
    },
    logout(state) {
      state.isLoggedIn = false;
      state.processing = false;
      state.response = {};
    },
  },
});

// Actions
export const AuthActions = {
  login: AuthSlice.actions.login,
  passwordReset: AuthSlice.actions.passwordReset,
  chooseProvider: AuthSlice.actions.chooseProvider,
  reset: AuthSlice.actions.reset,
  verification: AuthSlice.actions.verification,
  loadProvider: AuthSlice.actions.loadProvider,
  getUser: AuthSlice.actions.getUser,
  success: AuthSlice.actions.success,
  failed: AuthSlice.actions.failed,
  logout: AuthSlice.actions.logout,
}

// Selectors
export const selectIsLoggedIn = (state: RootState) => state.auth.isLoggedIn;

export const isProcessing = (state: RootState) => state.auth.processing;

export const response = (state: RootState) => state.auth.response;

export const error = (state: RootState) => state.auth.error;

// Reducer
const authReducer = AuthSlice.reducer;

export default authReducer;