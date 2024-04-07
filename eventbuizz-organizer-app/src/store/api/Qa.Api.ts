
import makeApi from '@src/utils/ConfigureAxios';
import { GetACtiveProgram } from '@srcstore/slices/Qa.Slice';


export const getQaModulesApi = (): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post('https://apidev.eventbuizz.com/organizer/qa/listing');
}
export const getQaProgramModulesApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').get(`https://apidev.eventbuizz.com/organizer/qa/moderator-view-data/${payload.id}`, payload);
}
export const OnPutLivetoRejectApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/qa/add-incoming-to-reject/${payload.id}`, payload);
}
export const OnPutrejectToLiveApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/qa/add-reject-to-incoming/${payload.id}`, payload);
}
export const OnPutcomingtoLiveApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/qa/add-incoming-to-live/${payload.id}`, payload);
}
export const onGetQuestionDataApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').get(`https://apidev.eventbuizz.com/organizer/qa/get-question-data/${payload.id}`, payload);
}
export const onCloneQuestionApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/qa/clone-question/${payload.id}`, payload);
}
export const OnPutLivetoaAchieveApi = (payload: GetACtiveProgram,state): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/qa/add-live-to-archive/${payload.id}`, payload);
}
export const OnPutactivemoderatorApi = (payload: GetACtiveProgram,state): Promise<any> => {
  if (payload.id) {
    return makeApi('https://apidev.eventbuizz.com').get('https://apidev.eventbuizz.com/organizer/qa/active-moderator');
  } else {
    return makeApi('https://apidev.eventbuizz.com').get('https://apidev.eventbuizz.com/organizer/qa/inactive-moderator');
  }
}
