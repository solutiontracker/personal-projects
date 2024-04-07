import { useCallback } from 'react'

import {Modules, QaActions, isProcessing, error, activeId, isinnerProcessing, GetACtiveProgram, ProgramssModules, isStatusprocessing, isOpenPopup, isPopupProcessing, CloneQuestionData } from '@src/store/slices/Qa.Slice'

import { QaModule } from '@src/models/QaModule'

import { useAppDispatch, useAppSelector } from '@src/store/Hooks'
import { PayloadAction } from '@reduxjs/toolkit'

export type QaServiceOperators = {
    modules: Array<QaModule>,
    programs: Array<QaModule>,
    clonequesiondata: Array<QaModule>,
    processing?: boolean,
    openpopup?: {id: number, status: boolean},
    statusprocessing?: boolean,
    popupprocessing?: boolean,
    error: string,
    loadModules: () => void,
    activeId: number,
    innerProcessing?: boolean,
    getactiveprogram: (action: PayloadAction<GetACtiveProgram>) => void;
    loadPrograms: (action: PayloadAction<GetACtiveProgram>) => void;
    incomingtoreject: (action: PayloadAction<GetACtiveProgram>) => void;
    addlivetoacrhieve: (action: PayloadAction<GetACtiveProgram>) => void;
    rejecttoincoming: (action: PayloadAction<GetACtiveProgram>) => void;
    comingtolive: (action: PayloadAction<GetACtiveProgram>) => void;
    activemoderator: (action: PayloadAction<GetACtiveProgram>) => void;
    getquestiondata: (action: PayloadAction<GetACtiveProgram>) => void;
    clonequestion: (action: PayloadAction<GetACtiveProgram>) => void;
}

/**
 * EventService custom-hooks
 * @see https://reactjs.org/docs/hooks-custom.html
 */
export const UseQaService = (): Readonly<QaServiceOperators> => {

  const dispatch = useAppDispatch()

  return {
    modules: useAppSelector(Modules),
    processing: useAppSelector(isProcessing),
    clonequesiondata: useAppSelector(CloneQuestionData),
    error: useAppSelector(error),
    openpopup: useAppSelector(isOpenPopup),
    activeId: useAppSelector(activeId),
    programs: useAppSelector(ProgramssModules),
    innerProcessing: useAppSelector(isinnerProcessing),
    statusprocessing: useAppSelector(isStatusprocessing),
    popupprocessing: useAppSelector(isPopupProcessing),
    loadModules: useCallback(
      () => {
        dispatch(QaActions.loadModules())
      },
      [dispatch],
    ),
    getactiveprogram: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.getactiveprogram(payload))
      },
      [dispatch],
    ),
    loadPrograms: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.loadPrograms(payload))
      },
      [dispatch],
    ),
    incomingtoreject: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.incomingtoreject(payload))
      },
      [dispatch],
    ),
    addlivetoacrhieve: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.addlivetoacrhieve(payload))
      },
      [dispatch],
    ),
    rejecttoincoming: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.rejecttoincoming(payload))
      },
      [dispatch],
    ),
    comingtolive: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.comingtolive(payload))
      },
      [dispatch],
    ),
    activemoderator: useCallback(
      (payload: GetACtiveProgram) => {
        dispatch(QaActions.activemoderator(payload))
      },
      [dispatch],
    ),
    getquestiondata: useCallback(
      (payload: {id: number}) => {
        dispatch(QaActions.getquestiondata(payload))
      },
      [dispatch],
    ),
    clonequestion: useCallback(
      (payload: {id: number}) => {
        dispatch(QaActions.clonequestion(payload))
      },
      [dispatch],
    ),
    
  }
}

export default UseQaService