import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { Module } from '@src/models/Module'
import { QuestionModule } from '@src/models/QuestionModule'
import type { RootState } from '@src/store/Index'
import { GeneralResponse } from '@src/models/GeneralResponse';

export interface PollsPayload {
  limit: number;
  page: number;
  sort_by: string;
  order_by: string;
  query: string
}
export interface CreatePollsPayload {
  question_type: string,
  question: string,
  result_chart_type: string,
  agenda_id: string,
  required_question: string,
  enable_comments: string,
  is_anonymous: string,
  max_options: string,
  min_options: string,
  poll_status: string,
  answer: Array<any>,
  column: Array<any>
}
export interface CreateQuestionPayload {
  page: number,
  question_type: string,
  question: string,
  result_chart_type: string,
  required_question: string,
  enable_comments: string,
  is_anonymous: string,
  max_options: string,
  min_options: string,
  question_status: string,
  answer: Array<any>,
  column: Array<any>
}
export interface UpdateQuestionPayload {
  question_id: number,
  page: number,
  question_type: string,
  question: string,
  result_chart_type: string,
  required_question: string,
  enable_comments: string,
  is_anonymous: string,
  max_options: string,
  min_options: string,
  question_status: string,
  answer: Array<any>,
  column: Array<any>
}
export interface PollstatusPayload {
  page: number;
  poll_status: number;
}
export interface QuestionListingPayload {
  page: number;
}
export interface ProgramidPayload {
  id: number;
}
export interface PollsState {
    modules: Array<Module>,
    questions: Array<QuestionModule>,
    programs: Array<QuestionModule>,
    processing: boolean,
    questionprocessing: boolean,
    programprocessing: boolean,
    createprocessing: boolean,
    pollsloading: boolean,
    program_id: number,
    pollstatus: Array<any>,
    response: GeneralResponse,
    error: string;
    hasError: boolean
    createquestionprocessing: boolean,
    updatequestionprocessing: boolean,
}

const initialState: PollsState = {
  modules: [],
  processing: true,
  questions: [],
  questionprocessing: true,
  programs: [],
  programprocessing: true,
  createprocessing: false,
  pollsloading: false,
  program_id: 0,
  response: {},
  error: '',
  hasError: false,
  pollstatus: [],
  createquestionprocessing: false,
  updatequestionprocessing: false

}

// Slice
export const PollsSlice = createSlice({
  name: 'polls',
  initialState,
  reducers: {
    loadModules(state, action: PayloadAction<PollsPayload>) {
      state.processing = true
    },
    success(state, action: PayloadAction<Module>) {
      state.processing = false;
      state.modules = action.payload;
      state.program_id = state.program_id === 0 ?  action.payload.data[0].id : state.program_id;
    },
    programID(state, action: PayloadAction<ProgramidPayload>) {
      state.program_id = action.payload.id
    },
    pollstatus(state) {
      state.pollsloading = true;
    },
    pollsloading(state, action: PayloadAction<PollstatusPayload>) {
      state.pollsloading = false;
      state.pollstatus = action.payload
    },
    loadQuestionListing(state, action: PayloadAction<QuestionListingPayload>) {
      state.questionprocessing = true
    },
    questionssuccess(state, action: PayloadAction<QuestionModule>) {
      state.questionprocessing = false;
      state.questions = action.payload;
    },
    loadprograms(state) {
      state.programprocessing = true
    },
    programsuccess(state, action: PayloadAction<any>) {
      state.processing = false;
      state.programs = action.payload
    },
    createpoll(state, action: PayloadAction<CreatePollsPayload>) {
      state.createprocessing = true;
      state.hasError = false
    },
    createsuccess(state, action: PayloadAction<GeneralResponse>) {
      state.createprocessing = false;
      state.response = action.payload;
      state.error = '';
    },
    createquestion(state) {
      state.createquestionprocessing = true;
    },
    createquestionsuccess(state, action: PayloadAction<CreateQuestionPayload>) {
      state.createquestionprocessing = false;
      state.response = action.payload;
    },
    updatequestion(state) {
      state.updatequestionprocessing = true;
    },
    updatequestionsuccess(state, action: PayloadAction<UpdateQuestionPayload>) {
      state.updatequestionprocessing = false;
      state.response = action.payload;
    },
    failed(state, action: PayloadAction<string>) {
      state.createprocessing = false;
      state.error = action.payload;
      state.hasError = true
    },
  },
})

// Actions
export const PollsActions = {
  loadModules: PollsSlice.actions.loadModules,
  loadQuestionListing: PollsSlice.actions.loadQuestionListing,
  success: PollsSlice.actions.success,
  questionssuccess: PollsSlice.actions.questionssuccess,
  loadprograms: PollsSlice.actions.loadprograms,
  programsuccess: PollsSlice.actions.programsuccess,
  programID: PollsSlice.actions.programID,
  createpoll: PollsSlice.actions.createpoll,
  createsuccess: PollsSlice.actions.createsuccess,
  createquestion: PollsSlice.actions.createquestion,
  createquestionsuccess: PollsSlice.actions.createquestionsuccess,
  updatequestion: PollsSlice.actions.updatequestion,
  updatequestionsuccess: PollsSlice.actions.updatequestionsuccess,
  failed: PollsSlice.actions.failed,
  pollstatus: PollsSlice.actions.pollstatus,
  pollsloading: PollsSlice.actions.pollsloading,
  
}

// Selectors
export const Modules = (state: RootState) => state.polls.modules;
export const QuestionsModules = (state: RootState) => state.polls.questions;
export const ProgramssModules = (state: RootState) => state.polls.programs;
export const isProcessing = (state: RootState) => state.polls.processing;
export const isQuestionProcessing = (state: RootState) => state.polls.questionprocessing;
export const isProgramsProcessing = (state: RootState) => state.polls.programprocessing;
export const isCreatePollProcessing = (state: RootState) => state.polls.createprocessing;
export const isCreateQuestionProcessing = (state: RootState) => state.polls.createquestionprocessing;
export const isUpdateQuestionProcessing = (state: RootState) => state.polls.updatequestionprocessing;
export const programId = (state: RootState) => state.polls.program_id;
export const ispollstatus = (state: RootState) => state.polls.pollsloading;
export const response = (state: RootState) => state.polls.response;
export const error = (state: RootState) => state.polls.error;
export const hasError = (state: RootState) => state.polls.hasError;

// Reducer
export default PollsSlice.reducer