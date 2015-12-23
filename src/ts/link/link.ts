import {Asset} from '../models/asset';

class Link {
	constructor(private url: string, private pageId: number, private title: string) {
		this.url = url? url : "";
		this.pageId = pageId? pageId : 0;
		this.title = title? title : "";
	}

	isAsset(): boolean {
		return this.getUrl().indexOf('/asset/') === 0;
	}

	isExternal(): boolean {
		return this.getUrl() !== "" && this.getUrl().substring(0,1) !== '/';
	}

	isHttp(): boolean {
		return this.url.substring(0,7) === 'http://';
	};

	isHttps(): boolean {
		return this.url.substring(0,8) === 'https://';
	};

	isInternal(): boolean {
		return this.pageId > 0 || this.getUrl().substring(0,1) === '/';
	};

	isMailto(): boolean {
		return this.getUrl().substring(0,7) === 'mailto:';
	}

	isTel(): boolean {
		return this.url.substring(0,4) === 'tel:';
	};

	getAsset(): Asset {
		var assetId = this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)\/(view|download)/i, "$1");

		return new Asset(parseInt(assetId));
	}

	getAssetAction(): string {
		if (this.isAsset()) {
			return this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)\/(view|download)/i, "$3");
		}
	}

	getUrl(): string {
		return (this.url == 'http://') ? '' : this.makeUrlRelative();
	}

	getPageId(): number {
		return this.pageId;
	}

	getTitle(): string {
		return this.title;
	}

	makeUrlRelative(): string {
		return (this.url.indexOf(window.location.hostname) > -1) ?
			this.url.replace(/^https?:\/\//, '').replace(window.location.hostname, '') :
			this.url;
	}
};
