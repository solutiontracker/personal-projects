export const BASE_URL = process.env.serverHost;
export const LOGIN_ENDPOINT = `${BASE_URL}/api/v1/sales/auth/login`;
export const LOGOUT_ENDPOINT = `${BASE_URL}/api/v1/sales/agent/logout`;
export const PASSWORD_REQUEST_ENDPOINT = `${BASE_URL}/api/v1/sales/auth/password/reset-request`;
export const AGENT_EVENTS_ENDPOINT = `${BASE_URL}/api/v1/sales/agent/events`;
export const AGENT_ENDPOINT = `${BASE_URL}/api/v1/sales/agent`;
export const PASSWORD_VERIFY_ENDPOINT = `${process.env.serverHost}/api/v1/sales/auth/password/reset-code/verify`;
export const PASSWORD_RESET_ENDPOINT = `${process.env.serverHost}/api/v1/sales/auth/password/reset`;
