import { authHeader, handleResponse } from "helpers";

export const HotelService = {
    create, update, listing, destroy, updateHotelPriceSetting, getHotelPriceSetting
};

function listing(activePage, request_data) {
    const form = new FormData();
    form.append('limit', request_data.limit);
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };
    return fetch(
        `${process.env.REACT_APP_URL}/hotel/listing/${activePage}`,
        requestOptions
    ).then(handleResponse);
}

function create(request_data) {
    const form = new FormData();
    const room_range = [];
    form.append('name', request_data['roomName']);
    form.append('price', request_data['priceNight']);
    form.append('description', request_data['roomDescription']);
    form.append('from_date', request_data['from_date']);
    form.append('to_date', request_data['to_date']);
    
    if(request_data['dates'] !== undefined) {
        request_data['dates'].map((date, k) => {
            return room_range.push({'room_date' : date, 'no_of_rooms': request_data['roomsDates'][k]});
        });
    }
    form.append('room_range', JSON.stringify(room_range));
    const requestOptions = {
        method: "POST",
        headers: authHeader(),
        body: form
    };

    return fetch(
        `${process.env.REACT_APP_URL}/hotel/store`,
        requestOptions
    ).then(handleResponse);
}

function update(request_data, id) {
    const form = {};
    const room_range = [];
    form.name = request_data['roomName'];
    form.price = request_data['priceNight'];
    form.description = request_data['roomDescription'];
    form.from_date = request_data['from_date'];
    form.to_date = request_data['to_date'];

    if(request_data['dates'] !== undefined) {
        request_data['dates'].map((date, k) => {
            return room_range.push({'room_date' : date, 'no_of_rooms': request_data['roomsDates'][k] ? request_data['roomsDates'][k] : 0});
        });
    }
    
    form.room_range = JSON.stringify(room_range);
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(form)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/hotel/update/${id}`,
        requestOptions
    ).then(handleResponse);
}

function destroy(id) {
    const requestOptions = {
        method: "DELETE",
        headers: authHeader()
    };
    return fetch(
        `${process.env.REACT_APP_URL}/hotel/destroy/${id}`,
        requestOptions
    ).then(handleResponse);
}

function updateHotelPriceSetting(request_data) {
    const requestOptions = {
        method: "PUT",
        headers: authHeader('PUT'),
        body: JSON.stringify(request_data)
    };
    return fetch(
        `${process.env.REACT_APP_URL}/hotel/updateHotelPriceSetting`,
        requestOptions
    ).then(handleResponse);
}

function getHotelPriceSetting() {
    const requestOptions = {
        method: "GET",
        headers: authHeader(),
    };
    return fetch(
        `${process.env.REACT_APP_URL}/hotel/getHotelPriceSetting`,
        requestOptions
    ).then(handleResponse);
}