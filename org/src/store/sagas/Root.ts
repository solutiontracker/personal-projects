import { all, fork } from 'redux-saga/effects'

import { EventWatcherSaga } from '@src/store/sagas/Event.Saga';
import { AuthWatcherSaga } from '@src/store/sagas/Auth.Saga';
import { PollsWatcherSaga } from '@src/store/sagas/Polls.Saga';
import { QaWatcherSaga } from '@src/store/sagas/Qa.Saga';

export function* RootSaga() {
  yield all([fork(EventWatcherSaga), fork(AuthWatcherSaga), fork(PollsWatcherSaga), fork(QaWatcherSaga)])
}

export default RootSaga