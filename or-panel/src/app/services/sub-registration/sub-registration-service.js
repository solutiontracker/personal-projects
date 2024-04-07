import { authHeader, handleResponse } from 'helpers';

export const SubRegistrationService = {
    create, update, destroy, listing, updateQuestionOrder
};

function create(request_data, sub_registration_id) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        if (item === "answer" || item === 'column' || item === "charts") {
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
        sub_registration_id ? `${process.env.REACT_APP_URL}/sub-registration/question/store/${sub_registration_id}` : `${process.env.REACT_APP_URL}/sub-registration/question/store`,
        requestOptions
    ).then(handleResponse);
}

function update(id, request_data, sub_registration_id) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/sub-registration/question/update/${sub_registration_id}/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroy(request_data, id, type) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE')
    };
    if (type === "question") {
        return fetch(
            `${process.env.REACT_APP_URL}/sub-registration/question/destroy/${request_data.sub_registration_id}/${id}`,
            requestOptions
        ).then(handleResponse);
    } else if(type === "option_matrix"){
        return fetch(
            `${process.env.REACT_APP_URL}/sub-registration/question/option/matrix/destroy/${id}`,
            requestOptions
        ).then(handleResponse);
    }else {
        return fetch(
            `${process.env.REACT_APP_URL}/sub-registration/question/option/destroy/${id}`,
            requestOptions
        ).then(handleResponse);
    }
}

function listing(request_data) {
    const form = new FormData();
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/sub-registration/questions`,
        requestOptions
    ).then(handleResponse);
}

function updateQuestionOrder(items) {
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
        `${process.env.REACT_APP_URL}/sub-registration/question/update/order`,
        requestOptions
    ).then(handleResponse);
}