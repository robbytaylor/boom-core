export class Asset {
	constructor(private id: number) {
		this.id = id;
	}

	getId() {
		return this.id;
	}

	getEmbedCode() {
		return $.get(this.getUrl('embed'));
	}

	getUrl(action: string, width: number, height: number): string {
		var url = '/asset/' + this.getId();

		if ((!action || action === 'view') && !(width || height)) {
			return url;
		}

		if (!action && (width || height)) {
			action = 'view';
		}

		url = url + '/' + action;

		if (width || height) {
			url = url + '/' + width + '/' + height;
		}

		return url;
	}
};
