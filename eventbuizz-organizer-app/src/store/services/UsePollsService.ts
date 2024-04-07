import { useCallback } from 'react'

import { PollsActions, PollsPayload, Modules, isProcessing, programId,  ProgramidPayload, QuestionListingPayload , QuestionsModules, isQuestionProcessing, isProgramsProcessing, ProgramssModules, CreatePollsPayload, isCreatePollProcessing, error, hasError, PollstatusPayload, ispollstatus, CreateQuestionPayload, isCreateQuestionProcessing, UpdateQuestionPayload, isUpdateQuestionProcessing} from '@src/store/slices/Polls.Slice'

import { Module } from '@src/models/Module'
import { QuestionModule } from '@src/models/QuestionModule'

import { useAppDispatch, useAppSelector } from '@src/store/Hooks'

export type PollsServiceOperators = {
    modules: Array<Module>,
    questions: Array<QuestionModule>,
    programs: Array<any>,
    loadModules: (payload: PollsPayload) => void,
    loadQuestionListing: (payload: QuestionListingPayload) => void,
    createpoll: (payload: CreatePollsPayload) => void,
    createquestion: (payload: CreateQuestionPayload) => void,
    updatequestion: (payload: UpdateQuestionPayload) => void,
    loadprograms: () => void,
    processing?: boolean,
    questionprocessing?: boolean,
    programprocessing?: boolean,
    pollsloading?: boolean,
    createprocessing?: boolean,
    hasError?: boolean,
    program_id: number,
    error: string,
    programID: (payload: ProgramidPayload) => void,
    pollstatus: (payload: PollstatusPayload) => void,
    createquestionprocessing: boolean,
    updatequestionprocessing: boolean
    
}

/**
 * EventService custom-hooks
 * @see https://reactjs.org/docs/hooks-custom.html
 */
export const UsePollsService = (): Readonly<PollsServiceOperators> => {

  const dispatch = useAppDispatch()

  return {
    modules: useAppSelector(Modules),
    questions: useAppSelector(QuestionsModules),
    programs: useAppSelector(ProgramssModules),
    error: useAppSelector(error),
    hasError: useAppSelector(hasError),
    processing: useAppSelector(isProcessing),
    questionprocessing: useAppSelector(isQuestionProcessing),
    programprocessing: useAppSelector(isProgramsProcessing),
    createprocessing: useAppSelector(isCreatePollProcessing),
    createquestionprocessing: useAppSelector(isCreateQuestionProcessing),
    updatequestionprocessing: useAppSelector(isUpdateQuestionProcessing),
    pollsloading: useAppSelector(ispollstatus),
    program_id: useAppSelector(programId),
    loadModules: useCallback(
      (payload: PollsPayload) => {
        dispatch(PollsActions.loadModules(payload))
      },
      [dispatch],
    ),
    loadQuestionListing: useCallback(
      (payload: QuestionListingPayload) => {
        dispatch(PollsActions.loadQuestionListing(payload))
      },
      [dispatch],
    ),
    loadprograms: useCallback(
      () => {
        dispatch(PollsActions.loadprograms())
      },
      [dispatch],
    ),
    programID: useCallback(
      (payload: ProgramidPayload) => {
        dispatch(PollsActions.programID(payload))
      },
      [dispatch],
    ),
    pollstatus: useCallback(
      (payload: PollstatusPayload) => {
        dispatch(PollsActions.pollstatus(payload))
      },
      [dispatch],
    ),
    createpoll: useCallback(
      (payload: CreatePollsPayload) => {
        dispatch(PollsActions.createpoll(payload))
      },
      [dispatch],
    ),
    createquestion: useCallback(
      (payload: CreateQuestionPayload) => {
        dispatch(PollsActions.createquestion(payload))
      },
      [dispatch],
    ),
    updatequestion: useCallback(
      (payload: UpdateQuestionPayload) => {
        dispatch(PollsActions.updatequestion(payload))
      },
      [dispatch],
    ),
  }
}

export default UsePollsService