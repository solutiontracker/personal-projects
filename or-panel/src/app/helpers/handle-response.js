import { store } from 'helpers';

export function handleResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        if (!response.ok && response.status === 401) {
            const error = (data && data.message) || response.statusText;
            localStorage.removeItem('eventBuizz');
            store.dispatch({ type: "success", "message": response.message });
            return Promise.reject(error);
        } else if (!response.ok && response.status === 503) {
            const error = (data && data.message) || response.statusText;
            localStorage.removeItem('eventInfo');
            store.dispatch({ type: "success", "message": error });
            return Promise.reject(error);
        } else if (!response.ok && response.status !== 422) {
            store.dispatch({ type: "success", "message": response.message, "redirect": '/error' });
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