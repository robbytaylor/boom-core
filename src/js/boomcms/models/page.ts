export class Page() {
	this.baseUrl = '/cms/page/';
	
	constructor(private id: number) {}

	add() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post(this.baseUrl + 'add/' + page_id, function(response) {
			(typeof response.url !== 'undefined')? promise.resolve(response) : promise.reject(response);
		});

		return promise;
	};

	addRelatedPage(pageId: number) {
		return $.post(this.baseUrl + 'relations/add/' + this.id, {
			related_page_id: pageId
		});
	};

	addTag(group: string, tag: string) {
		return $.post(this.baseUrl + 'tags/add/' + this.id, {
			group : group,
			tag : tag
		});
	};

	delete(options: object) {
		return $.post(this.baseUrl + 'settings/delete/' + this.id, options);
	};

	embargo() {
		var page = this,
			url = this.baseUrl + 'version/embargo/' + this.id,
			promise = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: url,
			title: 'Page embargo',
			width: 440
		}).done(function() {
			$.post(url, dialog.contents.find('form').serialize())
			.done(function(response) {
				new boomNotification("Page embargo saved.");
				promise.resolve(response);
			});
		});

		return promise;
	};

	publish() {
		var promise = new $.Deferred();

		$.post(this.baseUrl + 'version/embargo/' + this.id)
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	};

	requestApproval() {
		var url = this.baseUrl + 'version/request_approval/' + this.id;

		return $.post(url);
	};

	removeRelatedPage(pageId: number) {
		return $.post(this.baseUrl + 'relations/remove/' + this.id, {
			related_page_id: page_id
		});
	};

	removeTag(tagId: number) {
		return $.post(this.baseUrl + 'tags/remove/' + this.id, {
			tag : tagId
		});
	};

	revertToPublished() {
		var	promise = new $.Deferred(),
			page = this;

		new boomConfirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
			.done(function() {
				$.post(page.baseUrl + 'discard/' + page.id)
					.done(function() {
						promise.resolve();
					});
			});

		return promise;
	};

	saveSettings(section: string, data: object) {
		return $.post(this.baseUrl + 'settings/' + section + '/' + this.id, data);
	};

	setFeatureImage(asset) {
		return $.post(this.baseUrl + 'settings/feature/' + this.id, {
			feature_image_id : asset.getId()
		});
	};

	setTitle = function(title: string) {
		return $.post(this.baseUrl + 'version/title/' + this.id, {
			title : title
		});
	};

	setTemplate(templateId: number) {
		return $.post(this.baseUrl + 'version/template/' + this.id, {
			template_id: templateId
		});
	};
};