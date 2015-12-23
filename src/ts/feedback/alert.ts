import {Dialog} from 'dialog';

export class Alert {
	constructor(private message: string) {
		this.message = message;

		return this.open();
	}

	open() {
		var alert = this;

		return new Dialog({
			msg : alert.message,
			cancelButton : false
		});
	}
}