export class Group(group_id) {
	constructor(private id: number) {}

	boomGroup.prototype.base_url = '/cms/group/';

	add() {
		var group = this,
			deferred = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: this.base_url + 'add',
			title: 'Add group',
			closeButton: false,
			saveButton: true
		})
		.done(function() {
			group.addWithName(dialog.contents.find('input[type=text]').val())
				.done(function(response) {
					deferred.resolve(response);
				});
		});

		return deferred;
	};

	addRole(role_id: number, allowed: boolean, page_id: number) {
		var deferred = new $.Deferred(),
			group = this;

		group.removeRole(role_id, page_id)
			.done(function() {
				$.post(group.base_url + 'add_role/' + group.id, {
					role_id : role_id,
					allowed : allowed,
					page_id: page_id
				})
				.done(function(response) {
					deferred.resolve(response);
				});
			});

		return deferred;
	};

	addWithName(name: string) {
		return $.post(this.base_url + 'add', {name: name});
	};

	getRoles(page_id: number) {
		return $.getJSON(this.base_url + 'list_roles/' + this.id + '?page_id=' + page_id);
	};

	remove() {
		var group = this,
			deferred = new $.Deferred(),
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

		confirmation
			.done(function() {
				$.post(group.base_url + 'delete/' + group.id)
					.done(function(response) {
						deferred.resolve(response);
					});
			});

		return deferred;
	};

	removeRole(role_id: number, page_id: number) {
		return $.post(this.base_url + 'remove_role/' + this.id, {
			role_id : role_id,
			page_id : page_id
		});
	},

	save(data: object) {
		return $.post(this.base_url + 'save/' + this.id, data);
	};
};