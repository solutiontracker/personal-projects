import axios, { AxiosRequestConfig } from 'axios'
import { Platform } from 'react-native';
import AsyncStorageClass from '@src/utils/AsyncStorageClass';

export default function makeApi(baseURL: string) {

  const api = axios.create({
    baseURL,
  })

  api.defaults.headers.post['Content-Type'] = 'application/json';

  api.defaults.headers.put['Content-Type'] = 'application/json';

  api.defaults.headers.delete['Content-Type'] = 'application/json';

  api.interceptors.request.use(
    async (config: any) => {
      if (Platform.OS === 'web' && localStorage.getItem('access_token')) {
        config.headers = { ...config.headers, Authorization: `Bearer ${localStorage.getItem('access_token')}`, 'Event-Id': '3790' }
      } else if (Platform.OS === 'android' || Platform.OS === 'ios') {
        const token = await AsyncStorageClass.getItem('access_token');
        if (token) {
          config.headers = { ...config.headers, Authorization: `Bearer ${token}`, 'Event-Id': '3790' }
        }
      }
      return config;
    },
    (error) => Promise.reject(error)
  )

  api.interceptors.response.use(
    (response) => response.data,
    (error) => Promise.reject(error)
  )

  return api
}