import { Event } from '@src/models/Event';

import makeApi from '@src/utils/ConfigureAxios';
import { EventPayload } from '@src/store/slices/Event.Slice';

export const getModulesApi = (payload: EventPayload, state: any): Promise<Event> => {
  return makeApi('https://apidev.eventbuizz.com').post(`https://apidev.eventbuizz.com/organizer/event/listing/${payload.page}`, payload);
}