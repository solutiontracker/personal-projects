import { getCookieValue } from "./helper";

export function authHeader(method = 'POST') {
    let user =
        typeof window !== "undefined" && JSON.parse(localStorage.getItem('agent'));
    user = (user && true) ? user : {};

    let interface_language_id = getCookieValue('NEXT_LOCALE');

    if (user && user.access_token) {
        if (method === 'PUT' || method === 'DELETE')
            return { 'Authorization': 'Bearer ' + user.access_token, 'Accept': 'application/json', 'Content-Type': 'application/json', 'Interface-Language-Id': interface_language_id === 'da' ? 2 : 1 };
        else
            return { 'Authorization': 'Bearer ' + user.access_token, 'Accept': 'application/json', 'Content-Type': 'application/json', 'Interface-Language-Id': interface_language_id === 'da' ? 2 : 1 };
    } else {
        return {};
    }
}