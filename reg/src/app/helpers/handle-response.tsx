import { ltrim } from '@/src/app/helpers';
import in_array from "in_array";

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
export async function handleResponse(response: { text: () => Promise<any>; ok: any; status: number; statusText: any; }) {
    const text = await response?.text();
    const data = text && JSON.parse(text);
    if (!response.ok && response.status === 401) {
        const error = (data && data.message) || response.statusText;
        return Promise.reject(error);
    } else if (in_array(data?.redirect, ['no-order-found', 'waiting-link-expired'])) {
        const path = ltrim(window.location.pathname, "/");
        const params = path.split("/");
        if(params.length > 0 && data?.redirect === 'no-order-found') {
            window.location.href = process.env.REACT_APP_BASE_URL+'/'+params[0]+'/'+params[1]+'/no-order-found';
        } else if(params.length > 0 && data?.redirect === 'waiting-link-expired') {
            window.location.href = process.env.REACT_APP_BASE_URL+'/'+params[0]+'/'+params[1]+'/waiting-link-expired';
        } 
    } else if (!response.ok && response.status === 503) {
        const error_1 = (data && data.message) || response.statusText;
        return Promise.reject(error_1);
    }
    return data;
}