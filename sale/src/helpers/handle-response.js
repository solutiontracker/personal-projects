
import { removeAuthUser  } from '@/redux/store/slices/AuthSlice';

export function handleResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        if (!response.ok && response.status === 401) {
            const error = (data && data.message) || response.statusText;
            localStorage.removeItem('agent');
            store.dispatch({ type: "error", message: 'Unauthenticated, please login again' });
            return Promise.reject(error);
        } else if (!response.ok && response.status === 503) {
            const error = (data && data.message) || response.statusText;
            localStorage.removeItem('eventInfo');
            store.dispatch({ type: "success", message: error });
            return Promise.reject(error);
        } else if (!response.ok && response.status !== 422) {
            store.dispatch({ type: "success", message: response.message, redirect: '/auth/login' });
        }
        return data;
    });
}

export function handleThirdPartyResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        return data;
    });
}

export function handleErrorResponse(status, dispatch) {
    console.log('eero');
    if (status === 401) {
        dispatch(removeAuthUser());
    } else if (status === 503) {
        const error = (data && data.message) || response.statusText;
    } else if (status !== 422) {
    }
}