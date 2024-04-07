import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { QaModule } from '@src/models/QaModule'
import type { RootState } from '@src/store/Index'

export interface QaState {
    modules: Array<QaModule>,
    programs: Array<QaModule>
    clonequesiondata: Array<QaModule>
    processing: boolean,
    openpopup: {id: number, status: boolean},
    error: string;
    activeId: number,
    innerProcessing: boolean,
    statusprocessing: boolean,
    popupprocessing: boolean,
}
export interface GetACtiveProgram {
  id? : number;
}

const initialState: QaState = {
  modules: [],
  programs: [],
  clonequesiondata: [],
  processing: true,
  error: '',
  activeId: 0,
  innerProcessing: false,
  statusprocessing: false,
  popupprocessing: false,
  openpopup: {
    id: 0,
    status: false
  },
}

// Slice
export const QaSlice = createSlice({
  name: 'qa',
  initialState,
  reducers: {
    loadModules(state) {
      state.processing = true
    },
    success(state, action: PayloadAction<QaModule>) {
      state.processing = false;
      state.modules = action.payload;
      state.activeId = state.activeId === 0 ?  action.payload.qa_session_programs[0].id : state.activeId;
    },
    failed(state, action: PayloadAction<QaModule>) {
      state.processing = false;
      state.error = action.payload;
    },
    getactiveprogram(state, action: PayloadAction<GetACtiveProgram>) {
      state.activeId = action.payload.id;
    },
    loadPrograms(state) {
      state.programs = [];
      state.innerProcessing = true;
    },
    loadProgramsucssess(state, action: PayloadAction<QaModule>) {
      state.innerProcessing = false;
      state.programs = action.payload;
    },
    incomingtoreject(state) {
      state.statusprocessing = true;
    },
    incomingtorejectstatus(state) {
      state.statusprocessing = false;
    },
    rejecttoincoming(state) {
      state.statusprocessing = true;
    },
    comingtolive(state) {
      state.statusprocessing = true;
    },
    activemoderator(state) {
      state.statusprocessing = true;
    },
    openpopupAction(state, action: PayloadAction<{id: number, status: boolean}>) {
      state.openpopup = {
        id: action.payload.id,
        status: action.payload.status,
      };
    },
    getquestiondata(state) {
      state.popupprocessing = true;
    },
    getquestiondatasuccess(state, action: PayloadAction<QaModule>) {
      state.popupprocessing = false;
      state.clonequesiondata = action.payload
    },
    clonequestion(state) {
      state.popupprocessing = true;
    },
    clonsequestionsuccess(state) {
      state.popupprocessing = false;
      state.openpopup.status = false;
      state.statusprocessing = true;
    },
    addlivetoacrhieve(state) {
      state.statusprocessing = true;
      
    },
    addlivetoacrhievesuccess(state) {
      state.statusprocessing = false;
      
    },
  },
})

// Actions
export const QaActions = {
  loadModules: QaSlice.actions.loadModules,
  success: QaSlice.actions.success,
  failed: QaSlice.actions.failed,
  getactiveprogram: QaSlice.actions.getactiveprogram,
  loadPrograms: QaSlice.actions.loadPrograms,
  loadProgramsucssess: QaSlice.actions.loadProgramsucssess,
  incomingtoreject: QaSlice.actions.incomingtoreject,
  incomingtorejectstatus: QaSlice.actions.incomingtorejectstatus,
  rejecttoincoming: QaSlice.actions.rejecttoincoming,
  comingtolive: QaSlice.actions.comingtolive,
  activemoderator: QaSlice.actions.activemoderator,
  openpopupAction: QaSlice.actions.openpopupAction,
  getquestiondata: QaSlice.actions.getquestiondata,
  getquestiondatasuccess: QaSlice.actions.getquestiondatasuccess,
  clonequestion: QaSlice.actions.clonequestion,
  clonsequestionsuccess: QaSlice.actions.clonsequestionsuccess,
  addlivetoacrhieve: QaSlice.actions.addlivetoacrhieve,
  addlivetoacrhievesuccess: QaSlice.actions.addlivetoacrhievesuccess,

}

// Selectors
export const Modules = (state: RootState) => state.qa.modules;
export const isProcessing = (state: RootState) => state.qa.processing;
export const isOpenPopup = (state: RootState) => state.qa.openpopup;
export const isPopupProcessing = (state: RootState) => state.qa.popupprocessing;
export const error = (state: RootState) => state.qa.error;
export const activeId = (state: RootState) => state.qa.activeId;
export const getactiveprogram = (state: RootState) => state.qa.activeId;
export const isinnerProcessing = (state: RootState) => state.qa.innerProcessing;
export const isStatusprocessing = (state: RootState) => state.qa.statusprocessing;
export const ProgramssModules = (state: RootState) => state.qa.programs;
export const CloneQuestionData = (state: RootState) => state.qa.clonequesiondata;

// Reducer
export default QaSlice.reducer