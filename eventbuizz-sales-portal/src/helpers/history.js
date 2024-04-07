import { createBrowserHistory, createMemoryHistory } from 'history';

let history;

if (typeof document !== 'undefined') {
    history = createBrowserHistory();
} else {
    // You can provide an alternative history object for server-side rendering
    history = createMemoryHistory();
}

export { history };

// export const history = createBrowserHistory();