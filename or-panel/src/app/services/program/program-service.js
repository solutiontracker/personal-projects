import { authHeader, handleResponse } from "helpers";

export const ProgramService = {
    create,
    update,
    listing,
    destroy

};
function destroy(id) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader()
    };
    return fetch(
        `${process.env.REACT_APP_URL}/program/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}
function listing(activePage, request_data) {
    const form = new FormData();
    form.append('limit', request_data.limit);
    form.append('query', request_data.query);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/program/listing/${activePage}`,
        requestOptions
    ).then(handleResponse);
}
function update(request_data, id) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/program/update/${id}`,
        requestOptions
    ).then(handleResponse);
}

function create(request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (e) {
        form.append(e, request_data[e])
    });

    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };

    return fetch(
        `${process.env.REACT_APP_URL}/program/store`,
        requestOptions
    ).then(handleResponse);
}