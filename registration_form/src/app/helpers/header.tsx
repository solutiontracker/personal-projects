// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
export function header(method = 'POST'): any {
    if (method === 'PUT' || method === 'DELETE')
        return { 'Accept': 'application/json', 'Content-Type': 'application/json' };
    else
        return { 'Accept': 'application/json' };
}