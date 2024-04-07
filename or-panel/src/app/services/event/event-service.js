import { authHeader, handleResponse } from 'helpers';

export const EventService = {
    createEvent, listing, destroy, fetchEvent, updateEvent
};

function listing(request_data, page) {
    const form = new FormData();
    form.append('limit', request_data.limit);
    form.append('query', request_data.query);
    form.append('order_by', request_data.order_by);
    form.append('sort_by', request_data.sort_by);
    form.append('action', request_data.action);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event/listing/${page}`,
        requestOptions
    ).then(handleResponse);
}

function createEvent(request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (page) {
        if (page !== "errors") {
            Object.keys(request_data[page]).forEach(function (item) {
                if (request_data[page][item] !== undefined && request_data[page][item]) {
                    if (Array.isArray(request_data[page][item])) {
                        request_data[page][item].forEach(function (value, index) {
                            form.append(item + '[' + index + ']', value);
                        });
                    } else {
                        form.append(item, request_data[page][item]);
                    }
                }
            });
        }
    });

    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event/store`,
        requestOptions
    ).then(handleResponse);
}

function updateEvent(request_data, id) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (page) {
        if (page !== "errors") {
            Object.keys(request_data[page]).forEach(function (item) {
                if (request_data[page][item] !== undefined && request_data[page][item]) {
                    if (Array.isArray(request_data[page][item])) {
                        request_data[page][item].forEach(function (value, index) {
                            form.append(item + '[' + index + ']', value);
                        });
                    } else {
                        form.append(item, request_data[page][item]);
                    }
                }
            });
        }
    });

    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };

    return fetch(
        `${process.env.REACT_APP_URL}/event/update/${id}`,
        requestOptions
    ).then(handleResponse);
}

function fetchEvent(id) {
    const requestOptions = {
        method: "GET",
        headers: authHeader('GET')
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event/fetch/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroy(id) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE')
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}