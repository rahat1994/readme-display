import Rest from '@/utils/http/Rest.js';
import Caster from '@/utils/cast';

const controllers = (endpoints) => {
    
    const parseUrl = (target, value, prop, args) => {
        let index = 0;
        let uri = target[value].uri.replace(/{([^}?]*)(\?)?}/g, (match, p1, p2) => {
            if (index < args.length) {
                return args[index++];
            } else if (p2 === '?') {
                return '';
            } else {
                throw new Error(`Parameters mismatched in ${prop}.`);
            }
        });

        return uri.replace(/\/{2,}/g, '/').replace(/\/$/, '');
    };

    const normalize = (name) => {
        return name.endsWith(
            'Controller'
        ) ? name : `${name}Controller`
    }

    const findTarget = (name) => {
        let target;

        name = normalize(name);
            
        if (name in endpoints) {
            target = endpoints[name];
        }

        for (let key in endpoints) {
            let last = key.split('.').pop();

            if (last === name) {
                target = endpoints[key];
            }
        }

        if (!target) {
            throw new Error(`Unknown resource ${name}`);
        }

        return target;
    }

    const buildUrlWithQuery = (query, uri) => {
        // Use a base URL, the URI is relative
        const baseUrl = 'http://example.com';
        const url = new URL(uri, baseUrl);
        Object.entries(query).forEach(([key, value]) => {
            url.searchParams.append(key, value);
        });
        return url.pathname + url.search;
    };

    const handler = {
        __query: {},
        __params: {},
        __headers: {},
        get: function(target, prop, receiver) {
            if (prop === 'withParams') {
                return (...args) => {
                    this.__params = Object.assign(
                        this.__params, { ...args[0] }
                    );
                    return receiver;
                };
            }

            if (prop === 'withQuery') {
                return (...args) => {
                    this.__query = Object.assign(
                        this.__query, { ...args[0] }
                    );
                    return receiver;
                };
            }

            if (prop === 'withHeaders') {
                return (...args) => {
                    this.__headers = Object.assign(
                        this.__headers, { ...args[0] }
                    );
                    return receiver;
                };
            }

            // Handle the controller methods
            const value = `_${prop}`;
            
            if (value in target) {
                return (...args) => {
                    const query = { ...this.__query };
                    const params = { ...this.__params };
                    const headers = { ...defaultHeaders, ...this.__headers };
                    const method = target[value].methods[0].toLowerCase();

                    this.__query = {};
                    this.__params = {};
                    this.__headers = {};

                    if (method === 'get') {
                        for (let key in defaultQuery) {
                            query[key] = defaultQuery[key];
                        }
                    }

                    const uri = buildUrlWithQuery(
                        query, parseUrl(
                            target, value, prop, args
                        )
                    );

                    const result = (
                        async () => {
                            const r = await Rest[method](uri, params, headers);
                            return r;
                        }
                    )();

                    return result.then(result => Caster.cast(result, casts));
                };
            }

            throw new Error(`Undefined method ${prop}.`);
        }
    };

    let casts, defaultQuery, defaultHeaders;

    return (key, target) => {
        casts = target?.casts || {};
        defaultQuery = target?.query || {};
        defaultHeaders = target?.headers || {};
        return new Proxy(findTarget(key), handler);
    };
};

export default controllers(window.fluentFrameworkAdmin.endpoints);
