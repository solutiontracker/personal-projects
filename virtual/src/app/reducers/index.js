import { combineReducers } from 'redux';
import { alert } from './alert.reducer';
import { event } from './event.reducer';
import { redirect } from './redirect.reducer';
import { update } from './update.reducer';
import { auth } from './auth.reducer';
import { stream } from './stream.reducer';
import { agora } from './agora.reducer';
import { video } from './video.reducer';
import { gdpr } from './gdpr.reducer';
import { vonage } from './vonage.reducer';
const rootReducer = combineReducers({
  alert, event, redirect, update, auth, stream, video, agora, gdpr, vonage
});

export default rootReducer;