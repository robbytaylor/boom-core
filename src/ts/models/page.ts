import {Asset} from './asset';
import {Confirmation} from '../feedback/confirmation';
import {Dialog} from '../feedback/dialog';
import {Notification} from '../feedback/notification';

export class Page {
	private baseUrl: string = '/cms/page/';

	constructor(private id: number) {
		this.id = id;
	}

	add() {
		var promise = $.Deferred(),
			pageId = this.id;

		$.post(this.baseUrl + 'add/' + pageId, function(response) {
			(typeof response.url !== 'undefined')? promise.resolve(response) : promise.reject(response);
		});

		return promise;
	}

	addRelatedPage(pageId: number) {
		return $.post(this.baseUrl + 'relations/add/' + this.id, {
			related_page_id: pageId
		});
	}

	addTag(group: string, tag: string) {
		return $.post(this.baseUrl + 'tags/add/' + this.id, {
			group : group,
			tag : tag
		});
	}

	delete(options: Object) {
		return $.post(this.baseUrl + 'settings/delete/' + this.id, options);
	}

	embargo(): JQueryDeferred<any> {
		var page = this,
			url = this.baseUrl + 'version/embargo/' + this.id,
			promise: JQueryDeferred<any> = $.Deferred(),
			dialog;

		dialog = new Dialog({
			url: url,
			title: 'Page embargo',
			width: 440
		})

		dialog.open().done(function() {
			$.post(url, dialog.contents.find('form').serialize())
			.done(function(response) {
				new Notification("Page embargo saved.");
				promise.resolve(response);
			});
		});

		return promise;
	}

	publish(): JQueryDeferred<any> {
		var promise: JQueryDeferred<any> = $.Deferred();

		$.post(this.baseUrl + 'version/embargo/' + this.id)
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	}

	requestApproval() {
		var url = this.baseUrl + 'version/request_approval/' + this.id;

		return $.post(url);
	}

	removeRelatedPage(pageId: number) {
		return $.post(this.baseUrl + 'relations/remove/' + this.id, {
			related_page_id: pageId
		});
	}

	removeTag(tagId: number) {
		return $.post(this.baseUrl + 'tags/remove/' + this.id, {
			tag : tagId
		});
	}

	revertToPublished(): JQueryDeferred<any> {
		var	promise: JQueryDeferred<any> = $.Deferred(),
			page = this;

		new Confirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
			.open()
			.done(function() {
				$.post(page.baseUrl + 'discard/' + page.id)
					.done(function() {
						promise.resolve();
					});
			});

		return promise;
	}

	saveSettings(section: string, data: Object) {
		return $.post(this.baseUrl + 'settings/' + section + '/' + this.id, data);
	}

	setFeatureImage(asset: Asset) {
		return $.post(this.baseUrl + 'settings/feature/' + this.id, {
			feature_image_id : asset.getId()
		});
	}

	setTitle(title: string) {
		return $.post(this.baseUrl + 'version/title/' + this.id, {
			title : title
		});
	}

	setTemplate(templateId: number) {
		return $.post(this.baseUrl + 'version/template/' + this.id, {
			template_id: templateId
		});
	}
};
