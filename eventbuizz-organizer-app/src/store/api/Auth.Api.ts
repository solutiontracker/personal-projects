import { LoginPayload, PasswordResetPayload, ChooseProviderPayload, LoadProviderPayload, VerificationPayload, ResetPayload } from '@src/store/slices/Auth.Slice';

import { GeneralResponse } from '@src/models/GeneralResponse';

import makeApi from '@src/utils/ConfigureAxios';


const EventBaseUrl = '/event'

export const getLoginApi = (payload: LoginPayload, state: any): Promise<GeneralResponse> => {
  return makeApi('https://apidev.eventbuizz.com').post('https://apidev.eventbuizz.com/auth/login', payload);
}

export const getUserApi = (state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).get(`${EventBaseUrl}/${state?.event?.event.url}/attendee/profile`);
}

export const getPasswordResetApi = (payload: PasswordResetPayload, state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).post(`${EventBaseUrl}/${state?.event?.event.url}/auth/password/email`, payload);
}

export const getChooseProviderApi = (payload: ChooseProviderPayload, state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).post(`${EventBaseUrl}/${state?.event?.event.url}/auth/verification/${payload.id}`, payload);
}

export const getResetApi = (payload: ResetPayload, state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).post(`${EventBaseUrl}/${state?.event?.event.url}/auth/password/reset/${payload.token}`, payload);
}

export const getVerificationApi = (payload: VerificationPayload, state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).post(`${EventBaseUrl}/${state?.event?.event.url}/auth/verification/${payload.id}`, payload);
}

export const getLoadProviderApi = (payload: LoadProviderPayload, state: any): Promise<GeneralResponse> => {
  return makeApi(`${state?.env?.api_base_url}`).get(`${EventBaseUrl}/${state?.event?.event.url}/auth/verification/${payload.id}?screen=${payload.screen}`);
}