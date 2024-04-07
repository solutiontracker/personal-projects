import { Polls, QuestionListing } from '@src/models/Polls';

import makeApi from '@src/utils/ConfigureAxios';

import { PollsPayload, QuestionListingPayload, CreatePollsPayload, PollstatusPayload, CreateQuestionPayload, UpdateQuestionPayload } from '@src/store/slices/Polls.Slice';

export const getPollsModulesApi = (payload: PollsPayload, state: any): Promise<Polls> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/poll/listing/${payload.page}`, payload);
}
export const getQuestionsModulesApi = (payload: QuestionListingPayload, state: any): Promise<QuestionListing> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/poll/questions/${payload.page}`, payload);
}
export const getProgramsModulesApi = (): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').get('https://apidev.eventbuizz.com/organizer/poll/get-programs',);
}
export const getCreatePollsApi = (payload: CreatePollsPayload, state: any): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post('https://apidev.eventbuizz.com/organizer/poll/store',payload);
}
export const getPollstatusApi = (payload: PollstatusPayload, state: any): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').put(`https://apidev.eventbuizz.com/organizer/poll/update-status/${payload.page}`,payload);
}
export const getCreateQuestionApi = (payload: CreateQuestionPayload, state: any): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/poll/question/store/${payload.page}`,payload);
}
export const getUpdateQuestionApi = (payload: UpdateQuestionPayload, state: any): Promise<any> => {
  return makeApi('https://apidev.eventbuizz.com').put(`https://apidev.eventbuizz.com/organizer/poll/question/update/${payload.page}/${payload.question_id}`,payload);
}