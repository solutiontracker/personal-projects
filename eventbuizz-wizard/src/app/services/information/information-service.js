import { authHeader, handleResponse } from 'helpers';

export const InformationService = {
    create, update, destroy, listing, updateOrder, updateOrderInfoPages, destroyInfoPage
};

function create(request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        form.append(item, request_data[item]);
    });
    const type = (request_data.type === 'folder' ? 'menu' : request_data.type);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/${type}/store`,
        requestOptions
    ).then(handleResponse);
}

function update(id, request_data) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        form.append(item, request_data[item]);
    });
    const type = (request_data.type === 'folder' ? 'menu' : request_data.type);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/${type}/update/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroy(request_data, id, type) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE')
    };
    type = (type === 'folder' ? 'menu' : type);
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/${type}/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroyInfoPage(request_data, id, type, mainSection) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE'),
        body: JSON.stringify({ "mainSection": mainSection})
    };
    type = (type === 'folder' ? 'menu' : type);
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/${type}/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}

function listing(request_data) {
    const form = new FormData();
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/listing`,
        requestOptions
    ).then(handleResponse);
}

function updateOrder(request_data, items) {
    var list = [];
    items.forEach(function (item, index) {
        list.push({ id: item.id, type: item.type })
    });
    const requestOptions = {
        method: "PUT",
        headers: authHeader("PUT"),
        body: JSON.stringify({ "list": list})
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/update/order`,
        requestOptions
    ).then(handleResponse);
}

function updateOrderInfoPages(request_data, items) {
    var list = [];
    items.forEach(function (item, index) {
        list.push({ id: item.id, type: item.type, mainSection:item.section_id === undefined ? true : false})
    });
    const requestOptions = {
        method: "PUT",
        headers: authHeader("PUT"),
        body: JSON.stringify({ "list": list})
    };
    return fetch(
        `${process.env.REACT_APP_URL}/event-info/${request_data.cms}/update/order`,
        requestOptions
    ).then(handleResponse);
}