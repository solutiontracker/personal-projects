import { authHeader, handleResponse } from 'helpers';

export const GeneralService = {
    metaData, languages
};

function languages() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };
    return fetch(`${process.env.REACT_APP_URL}/general/metadata/languages`,
        requestOptions
    ).then(handleResponse);
}

function metaData() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };
    return fetch(`${process.env.REACT_APP_URL}/general/metadata`,
      requestOptions
    ).then(handleResponse);
}