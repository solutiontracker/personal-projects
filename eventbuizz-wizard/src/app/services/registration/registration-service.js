import { authHeader, handleResponse } from "helpers";

export const RegistrationService = {
    listing, update
};

function listing(alias = '') {
    const requestOptions = {
        method: "GET",
        headers: authHeader(),
    };
    return fetch(
        `${process.env.REACT_APP_URL}/registration/listing/${alias}`,
        requestOptions
    ).then(handleResponse);
}

function update(request_data, alias) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/registration/update/${alias}`,
        requestOptions
    ).then(handleResponse);
}