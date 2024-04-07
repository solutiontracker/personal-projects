import { response, Response } from '@src/store/slices/Response.slice'
import { useAppSelector } from '@src/store/Hooks'

export type ResponseServiceOperators = {
    response: Response
}

/**
 * ResponseService custom-hooks
 * @see https://reactjs.org/docs/hooks-custom.html
 */
export const UseResponseService = (): Readonly<ResponseServiceOperators> => {
  return {
    response: useAppSelector(response),
  }
}

export default UseResponseService