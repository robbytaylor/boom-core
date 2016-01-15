import {Confirmation} from '../ui/confirmation';
import {Dialog} from '../ui/dialog';

export class Url {
	constructor(private id: number, private pageId: number) {
		this.id = id;
		this.pageId = pageId;
	}

	add(): JQueryDeferred<any> {
		var url = this,
			deferred = $.Deferred(),
			dialog;

		dialog = new Dialog({
			url : '/boomcms/page/' + this.pageId + '/urls/add',
			title : 'Add URL',
			closeButton: false,
			saveButton: true,
			width : 700
		}).done(function() {
			var location = dialog.contents.find('input[name=url]').val();

			url.addWithLocation(location)
				.done(function() {
					deferred.resolve();
				});
		});

		return deferred;
	};

	addWithLocation(location: string): JQueryDeferred<any> {
		var deferred = $.Deferred(),
			pageId = this.pageId;

		$.post('/boomcms/page/' + pageId + '/urls/add', {location : location})
			.done(function(response) {
				if (response) {
					if (typeof response.existing_url_id !== 'undefined') {
						var url = new boomPageUrl(response.existing_url_id, pageId);
						url.move()
							.done(function() {
								deferred.resolve();
							});
					}
				} else {
					deferred.resolve();
				}
			});

		return deferred;
	};

	delete(): JQueryDeferred<any> {
		var url = this,
			deferred = n$.Deferred(),
			confirmation = new Confirmation('Please confirm', 'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!');

			confirmation
			.open()
			.done(function() {
				$.post('/boomcms/page/' + url.pageId + '/urls/' + url.id + '/delete')
				.done(function() {
					deferred.resolve();
				});
			});

		return deferred;
	};

	makePrimary(is_primary: boolean): JQueryDeferred<any> {
		return $.post('/boomcms/page/' + this.pageId + '/urls/' + this.id + '/make_primary');
	};

	move(): JQueryDeferred<any> {
		var deferred = new $.Deferred(),
			move_dialog,
			form_url = '/boomcms/page/' + this.pageId + '/urls/' + this.id + '/move',
			dialog;

		dialog = new Dialog({
			url : form_url,
			title : 'Move url',
			deferred: deferred,
			width : '500px'
		});
		dialog.open().done(function() {
			$.post(form_url)
				.done(function(response) {
					deferred.resolve(response);
				});
		});

		return deferred;
	};
}