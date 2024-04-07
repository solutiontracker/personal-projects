import { combineReducers } from 'redux';
import { alert } from './alert.reducer';
import { event } from './event.reducer';
import { redirect } from './redirect.reducer';
import { update } from './update.reducer';

const rootReducer = combineReducers({
  alert, event, redirect, update
});

export default rootReducer;