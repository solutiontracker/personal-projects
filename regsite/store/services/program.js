import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'
import { objectToArray } from '../../app/helpers/helper'

// Define a service using a base URL and expected endpoints
export const programApi = createApi({
  reducerPath: 'programApi',
  baseQuery: fetchBaseQuery({ baseUrl: process.env.NEXT_APP_URL}),
  endpoints: (builder) => ({
    getPrograms: builder.query({
      query: (path) => {
        return `/event/${path.eventUrl}/programs?page=${path.page}${path.search !== "" ? `&query=${path.search}`: ""}`
      },
      transformResponse: (response) => {
        const data = objectToArray(response.data)
        const meta =  response.meta ? response.meta : {}
        const links = response.links ? response.links : {}
        return  {data,meta,links};
      }
    }),
  }),
})

// Export hooks for usage in functional components, which are
// auto-generated based on the defined endpoints
export const { useGetProgramsQuery } = programApi