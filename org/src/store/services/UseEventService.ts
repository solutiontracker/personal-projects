import { useCallback } from 'react'

import { EventActions, EventPayload, Modules, isProcessing } from '@src/store/slices/Event.Slice'

import { Module } from '@src/models/Module'

import { useAppDispatch, useAppSelector } from '@src/store/Hooks'

export type EventServiceOperators = {
    modules: Array<Module>
    loadModules: (payload: EventPayload) => void,
    processing?: boolean,
    
}

/**
 * EventService custom-hooks
 * @see https://reactjs.org/docs/hooks-custom.html
 */
export const UseEventService = (): Readonly<EventServiceOperators> => {

  const dispatch = useAppDispatch()

  return {
    modules: useAppSelector(Modules),
    processing: useAppSelector(isProcessing),
    loadModules: useCallback(
      (payload: EventPayload) => {
        dispatch(EventActions.loadModules(payload))
      },
      [dispatch],
    )
  }
}

export default UseEventService