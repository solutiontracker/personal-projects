export function header(method = 'POST') {
    // return authorization header with jwt token
    let user = JSON.parse(localStorage.getItem('eventBuizz'));
    if (user && user.data.access_token) {
        if (method === 'PUT' || method === 'DELETE')
            return { 'Authorization': 'Bearer ' + user.data.access_token, 'Accept': 'application/json', 'Content-Type': 'application/json' };
        else
            return { 'Authorization': 'Bearer ' + user.data.access_token, 'Accept': 'application/json' };

    } else {
        if (method === 'PUT' || method === 'DELETE')
            return { 'Accept': 'application/json', 'Content-Type': 'application/json' };
        else
            return { 'Accept': 'application/json' };
    }
}