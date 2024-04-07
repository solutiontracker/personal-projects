import { errors, message, Error } from '@src/store/slices/Error.slice'
import { useAppSelector } from '@src/store/Hooks'

export type ErrorServiceOperators = {
    errors: Error,
    message: string | undefined
}

/**
 * ErrorService custom-hooks
 * @see https://reactjs.org/docs/hooks-custom.html
 */
export const UseErrorService = (): Readonly<ErrorServiceOperators> => {
  return {
    errors: useAppSelector(errors),
    message: useAppSelector(message)
  }
}

export default UseErrorService