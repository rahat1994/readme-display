import controller from './controllers';

export default class Controller {
	static init() {
		return new Proxy(new this, {
			get: (target, property, receiver) => {
				const method = Controller.method(target, property);
				
				return Controller[method](target, property, receiver);
			}
		});
	}

	static method(target, property) {
		return (property in target)
			? 'resolveFromController'
			: 'resolveFromRest';
	}

	static resolveFromController(target, property, receiver) {
		if (typeof target[property] === 'function') {
			return (...args) => {
				return target[property].bind(receiver)(...args);
			};
		}

		return target[property];	
	}

	static resolveFromRest(target, property, receiver) {
		let ns = (target.namespace || '').replace(/\\/g, '.') + '.';
		
		let name = `${ns}${target.constructor.name}`.replace(/^\./, '');
		
		const instance =  controller(name, target);

		return instance[property];
	}
}
