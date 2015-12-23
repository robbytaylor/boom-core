import {Dialog} from '../feedback/dialog';
import {Confirmation} from '../feedback/confirmation';

export class Person {
	private baseUrl: string = '/cms/person';

	constructor(private id: number) {
		this.id = id;
	}

	add() {
		var deferred = $.Deferred(),
			person = this,
			dialog: Dialog;

		dialog = new Dialog({
			url : this.baseUrl + 'add',
			width: '600px',
			title : 'Create new person',
			closeButton: false,
			saveButton: true
		});

		dialog.open().done(function() {
			var data = dialog.contents.find('form').serialize();

			person.addWithData(data)
				.done(function(response) {
					deferred.resolve();
				})
				.fail(function() {
					deferred.reject();
				});
		});

		return deferred;
	}

	addGroups(): JQueryDeferred<any> {
		var url = this.baseUrl + 'add_group/' + this.id,
			deferred: JQueryDeferred<any> = $.Deferred(),
			dialog: Dialog;

		dialog = new Dialog({
			url: url,
			title: 'Add group',
			closeButton: false,
			saveButton: true
		});

		dialog.open().done(function() {
			var groups = {};

			dialog.contents.find('form select option:selected').each(function(i, el) {
				var $el = $(el);
				groups[$el.val()] = $el.text();
			});

			var groupIds = Object.keys(groups);
			if (groupIds.length) {
				$.post(url, {'groups[]' : groupIds})
					.done(function() {
						deferred.resolve(groups);
					});
			} else {
				deferred.resolve([]);
			}
		});

		return deferred;
	}

	addWithData(data: Object) {
		return $.post(this.baseUrl + 'add', data);
	}

	delete() {
		var deferred = $.Deferred(),
			person = this,
			confirmation = new Confirmation('Please confirm', 'Are you sure you want to delete this person?');

			confirmation.open()
				.done(function() {
					$.post(person.baseUrl + 'delete', {
						people : [person.id]
					})
					.done(function() {
						deferred.resolve();
					});
				});

		return deferred;
	}

	deleteMultiple(ids: Array<number>) {
		return $.post(this.baseUrl + 'delete', {'people[]': ids});
	}

	removeGroup(groupId: number) {
		return $.post(this.baseUrl + 'remove_group/' + this.id, {group_id: groupId});
	}

	save(data: Object) {
		return $.post(this.baseUrl + 'save/' + this.id, data);
	}
}
