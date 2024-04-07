import { authHeader, handleResponse, handleThirdPartyResponse } from 'helpers';

export const service = {
    get, put, post, _import, destroy, download, eventCenterAutoLogin, downloadWithPost
};

function post(url, request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        if (request_data[item] !== undefined) {
            if (Array.isArray(request_data[item])) {
                request_data[item].forEach(function (value, index) {
                    form.append(item + '[' + index + ']', value);
                });
            } else {
                form.append(item, request_data[item]);
            }
        }
    });
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        url,
        requestOptions
    ).then(handleResponse);
}

function put(url, request_data) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        url,
        requestOptions
    ).then(handleResponse);
}

function get(url) {
    const requestOptions = {
        method: "GET",
        headers: authHeader('GET'),
    };
    return fetch(
        url,
        requestOptions
    ).then(handleResponse);
}

function destroy(url, request_data = null) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE'),
        body: JSON.stringify(request_data ? request_data : [])
    };
    return fetch(
        url,
        requestOptions
    ).then(handleResponse);
}

function _import(url, request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        if (item === "column") {
            form.append(item, JSON.stringify(request_data[item]));
        } else {
            form.append(item, request_data[item]);
        }
    });
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        url,
        requestOptions
    ).then(handleResponse);
}

function download(url) {
    const requestOptions = {
        method: "GET",
        headers: authHeader('GET'),
    };
    return fetch(
        url,
        requestOptions
    );
}

function downloadWithPost(url, request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        if (request_data[item] !== undefined) {
            if (Array.isArray(request_data[item])) {
                request_data[item].forEach(function (value, index) {
                    form.append(item + '[' + index + ']', value);
                });
            } else {
                form.append(item, request_data[item]);
            }
        }
    });
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        url,
        requestOptions
    );
}

function eventCenterAutoLogin(url) {
    const requestOptions = {
        method: "GET",
    };
    return fetch(
        url,
        requestOptions
    ).then(handleThirdPartyResponse);
}