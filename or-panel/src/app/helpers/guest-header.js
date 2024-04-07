export function guestHeader() {
    // return authorization header with jwt token
    let interface_language_id = JSON.parse(localStorage.getItem('interface_language_id'));
    if (interface_language_id && interface_language_id !== '') {
        return {'Content-Type': 'application/json', 'Interface-Language-Id': interface_language_id};
    } else {
        return {'Content-Type': 'application/json'};
    }
}