import { SagaIterator } from '@redux-saga/core'

import { call, put, takeEvery } from 'redux-saga/effects'

import { GeneralResponse } from '@src/models/GeneralResponse'

import { getPollsModulesApi, getQuestionsModulesApi, getProgramsModulesApi, getCreatePollsApi, getPollstatusApi, getCreateQuestionApi, getUpdateQuestionApi } from '@src/store/api/Polls.Api';

import { PollsActions, PollsPayload, QuestionListingPayload, CreatePollsPayload, PollstatusPayload, CreateQuestionPayload, UpdateQuestionPayload } from '@src/store/slices/Polls.Slice'

import { LoadingActions } from '@src/store/slices/Loading.Slice'

import { select } from 'redux-saga/effects';


function* OnGetPollsModules({
  payload,
}: {
    type: typeof PollsActions.loadModules
    payload: PollsPayload
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getPollsModulesApi,payload ,env)
  if (response.success) {
    yield put(PollsActions.success(response.data)); 
    yield put(LoadingActions.set(false))
  }
}
function* onGetQuestionsModules({
  payload,
}: {
    type: typeof PollsActions.loadQuestionListing
    payload: QuestionListingPayload
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getQuestionsModulesApi,payload ,env)
  if (response.success) {
    yield put(PollsActions.questionssuccess(response.data)); 
    yield put(LoadingActions.set(false))
  }
}
function* onGetPollstatusModules({
  payload,
}: {
    type: typeof PollsActions.pollstatus
    payload: PollstatusPayload
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getPollstatusApi,payload ,env)
  if (response.success) {
    yield put(PollsActions.pollsloading(response.data)); 
    yield put(LoadingActions.set(false))
  }
}

function* onGetProgramsModules({
  payload,
}: {
    type: typeof PollsActions.loadprograms
}): SagaIterator {
  yield put(LoadingActions.set(true))
  const env = yield select(state => state);
  const response: any = yield call(getProgramsModulesApi,env)
  if (response.success) {
    yield put(PollsActions.programsuccess(response.data.programs)); 
    yield put(LoadingActions.set(false))
  }
}
function* OnCreatePolls({
  payload,
}: {
    type: typeof PollsActions.createpoll
    payload: CreatePollsPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getCreatePollsApi, payload, state);
    if (response.success) {
      yield put(PollsActions.createsuccess(response));
    } else {
      yield put(PollsActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(PollsActions.failed(error.message));
  }
}
function* OnCreateQuestion({
  payload,
}: {
    type: typeof PollsActions.createquestion
    payload: CreateQuestionPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getCreateQuestionApi, payload, state);
    if (response.success) {
      yield put(PollsActions.createquestionsuccess(response));
    } else {
      yield put(PollsActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(PollsActions.failed(error.message));
  }
}
function* OnUpdateQuestion({
  payload,
}: {
    type: typeof PollsActions.updatequestion
    payload: UpdateQuestionPayload
}): SagaIterator {
  try {
    const state = yield select(state => state);
    const response: GeneralResponse = yield call(getUpdateQuestionApi, payload, state);
    if (response.success) {
      yield put(PollsActions.updatequestionsuccess(response));
    } else {
      yield put(PollsActions.failed(response.message!));
    }
  } catch (error: any) {
    yield put(PollsActions.failed(error.message));
  }
}

// Watcher Saga
export function* PollsWatcherSaga(): SagaIterator {
  yield takeEvery(PollsActions.loadModules.type, OnGetPollsModules)
  yield takeEvery(PollsActions.loadQuestionListing.type, onGetQuestionsModules)
  yield takeEvery(PollsActions.loadprograms.type, onGetProgramsModules)
  yield takeEvery(PollsActions.createpoll.type, OnCreatePolls)
  yield takeEvery(PollsActions.createquestion.type, OnCreateQuestion)
  yield takeEvery(PollsActions.updatequestion.type, OnUpdateQuestion)
  yield takeEvery(PollsActions.pollstatus.type, onGetPollstatusModules)
}

export default PollsWatcherSaga