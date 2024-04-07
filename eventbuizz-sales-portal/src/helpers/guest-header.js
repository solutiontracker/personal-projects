import { getCookieValue } from "./helper";

export function guestHeader() {
    // return authorization header with jwt token
    let interface_language_id = getCookieValue('NEXT_LOCALE');
    if (interface_language_id && interface_language_id !== '') {
        return {'Content-Type': 'application/json', 'Accept': 'application/json', 'Interface-Language-Id': interface_language_id === 'da' ? 2 : 1};
    } else {
        return {'Content-Type': 'application/json', 'Accept': 'application/json'};
    }

}

