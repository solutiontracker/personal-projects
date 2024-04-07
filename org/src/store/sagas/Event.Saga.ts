import { SagaIterator } from '@redux-saga/core'

import { call, put, takeEvery } from 'redux-saga/effects'

import { getModulesApi } from '@src/store/api/Event.Api';

import { EventActions, EventPayload } from '@src/store/slices/Event.Slice'

import { LoadingActions } from '@src/store/slices/Loading.Slice'
import { select } from 'redux-saga/effects';

function* OnGetModules({
  payload,
}: {
    type: typeof EventActions.FetchEvent
    payload: EventPayload
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getModulesApi,payload ,env)
  if (response.success) {
    yield put(EventActions.success(response.data)); 
    yield put(LoadingActions.set(false))
  }
}

// Watcher Saga
export function* EventWatcherSaga(): SagaIterator {
  yield takeEvery(EventActions.loadModules.type, OnGetModules)
}

export default EventWatcherSaga