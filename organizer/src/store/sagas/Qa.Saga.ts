import { SagaIterator } from '@redux-saga/core'

import { call, put, takeEvery, select } from 'redux-saga/effects'


import { GetACtiveProgram, QaActions } from '@src/store/slices/Qa.Slice'

import { LoadingActions } from '@src/store/slices/Loading.Slice'

import { getQaModulesApi, getQaProgramModulesApi, OnPutLivetoRejectApi, OnPutrejectToLiveApi, OnPutcomingtoLiveApi, OnPutactivemoderatorApi,onGetQuestionDataApi, onCloneQuestionApi, OnPutLivetoaAchieveApi  } from '@src/store/api/Qa.Api'


function* OnGetQaModules({
  payload,
}: {
    type: typeof QaActions.loadModules
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const response: any = yield call(getQaModulesApi)
  if (response.success) {
    yield put(QaActions.success(response.data)); 
    yield put(LoadingActions.set(false))
  } else {
    yield put(QaActions.failed(response.data)); 
    yield put(LoadingActions.set(false))
  }
}


function* OnGetQaProgramModules({
  payload,
}: {
  type: typeof QaActions.loadPrograms
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getQaProgramModulesApi, payload, env)
  if (response) {
    yield put(QaActions.loadProgramsucssess(response.data)); 
    yield put(LoadingActions.set(false))
  } else {
    yield put(QaActions.failed(response.data)); 
    yield put(LoadingActions.set(false))
  }
}

function* OnPutLivetoReject({
  payload,
}: {
  type: typeof QaActions.incomingtoreject
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(OnPutLivetoRejectApi, payload, env)
  if (response.success) {
    yield put(QaActions.incomingtorejectstatus(response)); 
    yield put(LoadingActions.set(false))
  } 
}

function* OnPutLivetoArchieve({
  payload,
}: {
  type: typeof QaActions.addlivetoacrhieve
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(OnPutLivetoaAchieveApi, payload, env)
  if (response.success) {
    yield put(QaActions.addlivetoacrhievesuccess()); 
    yield put(LoadingActions.set(false))
  } 
}

function* OnPutRejecttoLive({
  payload,
}: {
  type: typeof QaActions.rejecttoincoming
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(OnPutrejectToLiveApi, payload, env)
  if (response.success) {
    yield put(QaActions.incomingtorejectstatus(response)); 
    yield put(LoadingActions.set(false))
  } 
}
function* OnPutcomingtoLive({
  payload,
}: {
  type: typeof QaActions.rejecttoincoming
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(OnPutcomingtoLiveApi, payload, env)
  if (response.success) {
    yield put(QaActions.incomingtorejectstatus(response)); 
    yield put(LoadingActions.set(false))
  } 
}

function* OnPutactiveModerator({
  payload,
}: {
  type: typeof QaActions.rejecttoincoming
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(OnPutactivemoderatorApi, payload, env)
  if (response.success) {
    yield put(QaActions.incomingtorejectstatus(response)); 
    yield put(LoadingActions.set(false))
  } 
}
function* OngetQuestionData({
  payload,
}: {
  type: typeof QaActions.getquestiondata
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(onGetQuestionDataApi, payload, env)
  if (response.success) {
    yield put(QaActions.getquestiondatasuccess(response)); 
    yield put(LoadingActions.set(false))
  } 
}
function* onCloneQuestion({
  payload,
}: {
  type: typeof QaActions.clonequestion
  payload: GetACtiveProgram
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(onCloneQuestionApi, payload, env)
  if (response.success) {
    yield put(QaActions.clonsequestionsuccess()); 
    yield put(LoadingActions.set(false))
  } 
}


// Watcher Saga
export function* QaWatcherSaga(): SagaIterator {
  yield takeEvery(QaActions.loadModules.type, OnGetQaModules)
  yield takeEvery(QaActions.loadPrograms.type, OnGetQaProgramModules)
  yield takeEvery(QaActions.incomingtoreject.type, OnPutLivetoReject)
  yield takeEvery(QaActions.rejecttoincoming.type, OnPutRejecttoLive)
  yield takeEvery(QaActions.comingtolive.type, OnPutcomingtoLive)
  yield takeEvery(QaActions.activemoderator.type, OnPutactiveModerator)
  yield takeEvery(QaActions.getquestiondata.type, OngetQuestionData)
  yield takeEvery(QaActions.clonequestion.type, onCloneQuestion)
  yield takeEvery(QaActions.addlivetoacrhieve.type, OnPutLivetoArchieve)

}

export default QaWatcherSaga