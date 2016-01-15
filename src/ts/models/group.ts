import {Dialog} from '../ui/dialog';
import {Confirmation} from '../ui/confirmation';

export class Group {
	private baseUrl: string = '/boomcms/group';

	constructor(private id: number) {
		this.id = id;
	}

	add(): JQueryDeferred<any> {
		var group = this,
			deferred: JQueryDeferred<any> = $.Deferred(),
			dialog: Dialog;

		dialog = new Dialog({
			url: this.baseUrl + 'add',
			title: 'Add group',
			closeButton: false,
			saveButton: true
		});

		dialog.open().done(function() {
			group.addWithName(dialog.contents.find('input[type=text]').val())
				.done(function(response) {
					deferred.resolve(response);
				});
		});

		return deferred;
	}

	addRole(roleId: number, allowed: boolean, pageId: number) {
		var deferred: JQueryDeferred<any> = $.Deferred(),
			group = this;

		group.removeRole(roleId, pageId)
			.done(function() {
				$.post(group.baseUrl + 'add_role/' + group.id, {
					role_id: roleId,
					allowed: allowed,
					page_id: pageId
				})
				.done(function(response) {
					deferred.resolve(response);
				});
			});

		return deferred;
	}

	addWithName(name: string) {
		return $.post(this.baseUrl + 'add', {name: name});
	}

	getRoles(page_id: number) {
		return $.get(this.baseUrl + 'list_roles/' + this.id + '?page_id=' + page_id);
	}

	remove(): JQueryDeferred<any> {
		var group = this,
			deferred: JQueryDeferred<any> = $.Deferred(),
			confirmation = new Confirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

		confirmation.open()
			.done(function() {
				$.post(group.baseUrl + 'delete/' + group.id)
					.done(function(response) {
						deferred.resolve(response);
					});
			});

		return deferred;
	}

	removeRole(roleId: number, pageId: number) {
		return $.post(this.baseUrl + 'remove_role/' + this.id, {
			role_id : roleId,
			page_id : pageId
		});
	}

	save(data: Object) {
		return $.post(this.baseUrl + 'save/' + this.id, data);
	}
};