import ResponseProxyItr from "./ResponseProxyItr";

let instance = null;

const addQueryParam = (url, key, value) => {
    return wp.url.addQueryArgs(url, { [key]: value });
};

const makeResponse = async (response) => {
    return {
        original: response,
        status: response.status,
        statusText: response.statusText,
        responseJSON: await response.json(),
    };
};

const request = async (method, route, data = {}, headers = {}) => {
    const config = instance.config.globalProperties.appVars;
    const { namespace, version } = config.rest;
    let url = `${namespace}/${version}/${route.replace(/^\/+/, "")}`;

    headers["X-WP-Nonce"] = config.rest.nonce;

    // If method is GET data is given, add them to the URL
    if (method === "GET" && Object.keys(data).length) {
        for (const [key, value] of Object.entries(data)) {
            url = addQueryParam(url, key, value);
        }
    }

    const options = {
        method,
        parse: false,
        headers: {
            ...headers,
            "Content-Type": "application/json",
        },
        path: addQueryParam(url, "query_timestamp", Date.now()),
        body: method === "GET" ? undefined : JSON.stringify(data),
    };

    try {
        const response = await wp.apiFetch(options);

        return Promise.resolve(
            new ResponseProxyItr(await makeResponse(response))
        );
    } catch (response) {
        return Promise.reject(
            new ResponseProxyItr(await makeResponse(response))
        );
    }
};

export default {
    setInstance(app) {
        instance = app;
    },
    get(route, data = {}, headers = {}) {
        console.log(route, data, headers);
        return request("GET", route, data, headers);
    },
    post(route, data = {}, headers = {}) {
        return request("POST", route, data, headers);
    },
    delete(route, data = {}, headers = {}) {
        return request("DELETE", route, data, headers);
    },
    put(route, data = {}, headers = {}) {
        return request("PUT", route, data, headers);
    },
    patch(route, data = {}, headers = {}) {
        return request("PATCH", route, data, headers);
    },
};
