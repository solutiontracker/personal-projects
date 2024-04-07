import { SagaIterator } from '@redux-saga/core'

import { call, put, takeEvery } from 'redux-saga/effects'

import { getLoginApi, getUserApi, getPasswordResetApi, getChooseProviderApi, getLoadProviderApi, getVerificationApi, getResetApi } from '@src/store/api/Auth.Api';

import { LoginPayload, AuthActions, ChooseProviderPayload, PasswordResetPayload, LoadProviderPayload, VerificationPayload, ResetPayload } from '@src/store/slices/Auth.Slice';

import { GeneralResponse } from '@src/models/GeneralResponse'

import { Platform } from 'react-native';

import AsyncStorageClass from '@src/utils/AsyncStorageClass';

import { select } from 'redux-saga/effects';

// Worker Sagas handlers
function* OnLogin({
  payload,
}: {
    type: typeof AuthActions.login
    payload: LoginPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getLoginApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
      if (Platform.OS === 'web') {
        localStorage.setItem('access_token', response.data.access_token);
      } else {
        AsyncStorageClass.setItem('access_token', response.data.access_token);
      }
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
    if (Platform.OS === 'web') {
      localStorage.removeItem('access_token');
    } else {
      AsyncStorageClass.removeItem('access_token');
    }
  }
}

function* OnPasswordReset({
  payload,
}: {
    type: typeof AuthActions.login
    payload: PasswordResetPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getPasswordResetApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
  }
}

function* OnChooseProvider({
  payload,
}: {
    type: typeof AuthActions.login
    payload: ChooseProviderPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getChooseProviderApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
  }
}

function* OnReset({
  payload,
}: {
    type: typeof AuthActions.login
    payload: ResetPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getResetApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
  }
}

function* OnVerification({
  payload,
}: {
    type: typeof AuthActions.login
    payload: VerificationPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getVerificationApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
  }
}

function* OnLoadProvider({
  payload,
}: {
    type: typeof AuthActions.login
    payload: LoadProviderPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getLoadProviderApi, payload, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(AuthActions.failed(error.message));
  }
}

function* OnGetUser({ }: {
    type: typeof AuthActions.getUser
    payload: LoginPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getUserApi, state);
    if (response.success) {
      yield put(AuthActions.success(response));
    } else {
      yield put(AuthActions.logout());
    }
  } catch (error: any) {
    yield put(AuthActions.logout());
  }
}

function* OnLogout({
}: {
    type: typeof AuthActions.logout
    payload: Event
}): SagaIterator {
  if (Platform.OS === 'web') {
    localStorage.removeItem('access_token');
  } else {
    AsyncStorageClass.removeItem('access_token');
  }
}

// Watcher Saga
export function* AuthWatcherSaga(): SagaIterator {
  yield takeEvery(AuthActions.login.type, OnLogin)
  yield takeEvery(AuthActions.passwordReset.type, OnPasswordReset)
  yield takeEvery(AuthActions.chooseProvider.type, OnChooseProvider)
  yield takeEvery(AuthActions.reset.type, OnReset)
  yield takeEvery(AuthActions.verification.type, OnVerification)
  yield takeEvery(AuthActions.loadProvider.type, OnLoadProvider)
  yield takeEvery(AuthActions.getUser.type, OnGetUser)
  yield takeEvery(AuthActions.logout.type, OnLogout)
}

export default AuthWatcherSaga