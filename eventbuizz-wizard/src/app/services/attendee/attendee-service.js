import { authHeader, handleResponse } from 'helpers';

export const AttendeeService = {
    create, update, destroy, listing, programs
};

function create(request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        form.append(item, request_data[item]);
    });
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/attendee/store`,
        requestOptions
    ).then(handleResponse);
}

function update(id, request_data) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/attendee/update/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroy(id) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE')
    };
    return fetch(
        `${process.env.REACT_APP_URL}/attendee/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}

function listing(activePage, request_data) {
    const form = new FormData();
    form.append('limit', request_data.limit);
    form.append('query', request_data.query);
    form.append('sort_by', request_data.sort_by);
    form.append('order_by', request_data.order_by);
    if (request_data.speaker !== undefined) form.append('speaker', request_data.speaker);
    if (request_data.attendee_type !== 0) form.append('attendee_type', request_data.attendee_type);
    if (request_data.created_at !== undefined) form.append('created_at', request_data.created_at);
    if (request_data.action !== undefined) form.append('action', request_data.action);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/attendee/listing/${activePage}`,
        requestOptions
    ).then(handleResponse);
}

function programs() {
    const requestOptions = {
        method: "GET",
        headers: authHeader(),
    };
    return fetch(
        `${process.env.REACT_APP_URL}/program/all`,
        requestOptions
    ).then(handleResponse);
}