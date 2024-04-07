import { authHeader, handleResponse } from 'helpers';

export const SurveyService = {
    create, update, destroy, listing, updateQuestionOrder,getLeaderBoard
};

function create(request_data, type, survey_id) {
    const form = new FormData();
    Object.keys(request_data).forEach(function (item) {
        if (item === "answer" || item === 'column' || item === "charts") {
            form.append(item, JSON.stringify(request_data[item]));
        } else {
            form.append(item, request_data[item]);
        }
    });
    if (type === "survey") {
        const requestOptions = {
            method: "POST",
            headers: authHeader(),
            body: form
        };
        return fetch(
            `${process.env.REACT_APP_URL}/survey/store`,
            requestOptions
        ).then(handleResponse);
    } else {
        const requestOptions = {
            method: "POST",
            headers: authHeader(),
            body: form
        };
        return fetch(
            `${process.env.REACT_APP_URL}/survey/question/store/${survey_id}`,
            requestOptions
        ).then(handleResponse);
    }
}

function update(request_data, type, id, survey_id = 0) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    if (type === "survey") {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/update/${id}`,
            requestOptions
        ).then(handleResponse);
    } else {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/question/update/${survey_id}/${id}`,
            requestOptions
        ).then(handleResponse);
    }
}

function destroy(request_data, id, type) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader('DELETE')
    };
    if (type === "question") {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/question/destroy/${request_data.survey_id}/${id}`,
            requestOptions
        ).then(handleResponse);
    } else if (type === "survey") {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/destroy/${id}`,
            requestOptions
        ).then(handleResponse);
    } else if(type === "option_matrix"){
        return fetch(
            `${process.env.REACT_APP_URL}/survey/question/option/matrix/destroy/${id}`,
            requestOptions
        ).then(handleResponse);
    } else{
        return fetch(
            `${process.env.REACT_APP_URL}/survey/question/option/destroy/${id}`,
            requestOptions
        ).then(handleResponse);
    }
}

function listing(request_data, type, activePage = 1) {
    const form = new FormData();
    form.append('limit', request_data.limit);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    if (type === "survey") {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/listing/${activePage}`,
            requestOptions
        ).then(handleResponse);
    } else {
        return fetch(
            `${process.env.REACT_APP_URL}/survey/questions/${request_data.survey_id}`,
            requestOptions
        ).then(handleResponse);
    }
}
function getLeaderBoard(request_data) {
    const requestOptions = {
        method: "GET",
        headers: authHeader()
    };
    return fetch(
        `${process.env.REACT_APP_URL}/survey/get_leaderboard/${request_data.survey_id}`,
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
        body: JSON.stringify({ "list": list })
    };
    return fetch(
        `${process.env.REACT_APP_URL}/survey/question/update/order`,
        requestOptions
    ).then(handleResponse);
}