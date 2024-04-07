import UAParser from 'ua-parser-js'

const parser = new UAParser()

const userAgentInfo = parser.getResult()

function formatString(fmt, ...args) {
    if (!fmt.match(/^(?:(?:(?:[^{}]|(?:\{\{)|(?:\}\}))+)|(?:\{[0-9]+\}))+$/)) {
        throw new Error('invalid format string.');
    }
    return fmt.replace(/((?:[^{}]|(?:\{\{)|(?:\}\}))+)|(?:\{([0-9]+)\})/g, (m, str, index) => {
        if (str) {
            return str.replace(/(?:{{)|(?:}})/g, m => m[0]);
        } else {
            if (index >= args.length) {
                throw new Error('argument index is out of range in format');
            }
            return args[index];
        }
    });
}

function ltrim(str, chr) {
    var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
    return str.replace(rgxtrim, '');
}

const isSafari = () => {
    return (
        userAgentInfo.browser.name === 'Safari' ||
        userAgentInfo.browser.name === 'Mobile Safari'
    )
}

const isCompatibleChrome = () => {
    if (userAgentInfo.browser.name === 'Chrome') {
        const major = +userAgentInfo.browser.major
        if (major >= 72) return true
    }
    return false
}

const isFirefox = () => {
    return userAgentInfo.browser.name === 'Firefox'
}


export {
    formatString,
    ltrim,
    isSafari,
    isCompatibleChrome,
    isFirefox
};