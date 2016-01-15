import {Asset} from './asset';
import {Confirmation} from '../ui/confirmation';
import {Dialog} from '../ui/dialog';
import {Message} from '../ui/message';

export class Page {
	private baseUrl: string = '/boomcms/page/';

	constructor(private id: number) {
		this.id = id;
	}

	add() {
		var promise = $.Deferred(),
			page_id = this.id;

		$.post(this.baseUrl + 'add/' + page_id, function(response) {
			if (response.prompt) {
				var dialog = new boomDialog({
					msg: response.prompt,
					cancelButton: false,
					closeButton: false,
					onLoad: function() {
						dialog.contents.on('click', 'button', function() {
							var parentId = $(this).attr('data-parent'),
								parent = parentId === this.id ? this : new boomPage(parentId);

							if (!parentId) {
								dialog.cancel();
							} else {
								parent.addWithoutPrompt()
									.done(function(response) {
										promise.resolve(response);
									});
										
							}
						});
					}
				});
			} else if (response.url) {
				promise.resolve(response);
			} else {
				promise.reject(response);
			}
		});

		return promise;
	};

	addWithoutPrompt() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post(this.baseUrl + 'add/' + page_id, {noprompt: 1}, function(response) {
			if (response.url) {
				promise.resolve(response);
			} else {
				promise.reject(response);
			}
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
				new Message("Page embargo saved.");
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
