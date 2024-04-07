import createSagaMiddleware from '@redux-saga/core'
import logger from 'redux-logger';
import { configureStore } from '@reduxjs/toolkit';
import EventSlice from '@src/store/slices/Event.Slice'
import ResponseSlice from '@src/store/slices/Response.slice'
import ErrorSlice from '@src/store/slices/Error.slice'
import AuthSlice from '@src/store/slices/Auth.Slice'
import EnvSlice from '@src/store/slices/Env.Slice'
import PollsSlice  from '@src/store/slices/Polls.Slice';
import { RootSaga } from '@src/store/sagas/Root'
import QaSlice from '@src/store/slices/Qa.Slice';

const makeStore = () => {

  const sagaMiddleware = createSagaMiddleware()

  const store = configureStore({
    reducer: {
      auth: AuthSlice,
      event: EventSlice,
      env: EnvSlice,
      polls: PollsSlice,
      qa: QaSlice,
      response: ResponseSlice,
      error: ErrorSlice
    },
    devTools: true,
    middleware: getDefaultMiddleware =>
      getDefaultMiddleware({ thunk: false })
        .concat(sagaMiddleware)
        // .concat(logger),
  })

  sagaMiddleware.run(RootSaga)

  return store
}

export const store = makeStore()

export type AppDispatch = typeof store.dispatch

export type RootState = ReturnType<typeof store.getState>