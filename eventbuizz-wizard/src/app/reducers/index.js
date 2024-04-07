import { combineReducers } from 'redux';
import { alert } from './alert.reducer';
import { event } from './event.reducer';
import { eventState } from './event-state.reducer';
import { eventStep } from './event-step.reducer';
import { template } from './template.reducer';
import { invitation } from './invitation.reducer';
import { redirect } from './redirect.reducer';
import { update } from './update.reducer';
import { auth } from './auth-reducer'; 

const rootReducer = combineReducers({
  alert, event, eventState, eventStep, template, invitation, redirect, update, auth
});

export default rootReducer;