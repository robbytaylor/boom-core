/// <reference path="../../../typings/jquery/jquery.d.ts" />
/// <reference path="../../../typings/jqueryui/jqueryui.d.ts" />

export class Dialog {
	public contents: JQuery;

	private deferred: JQueryDeferred<any> = $.Deferred().always(function() {
		$(top.window).trigger('boom:dialog:close');
	});

	private defaultOptions = {
		width: 'auto',
		cancelButton : true,
		closeButton : true,
		autoOpen: true,
		modal: true,
		resizable: false,
		draggable: true,
		closeOnEscape: true,
		buttons : [],
		dialogClass : 'b-dialog',
		boomDialog: this
	};

	constructor(private options: any) {
		this.options = $.extend(this.defaultOptions, options);
	}

	always(callback: Function) {
		this.deferred.always(callback);

		return this;
	}

 	private cancelButton = {
		text : 'Cancel',
		icons : { primary : 'b-button-icon-cancel b-button-icon' },
		class : 'b-button',
		click : function() {
			var dialog = $(this).dialog('option', 'boomDialog');
			dialog.cancel();
		}
	}

	cancel() {
		this.deferred.reject();

		this.contents.remove();
		this.contents = null;
	}

	private closeButton = {
		text : 'Okay',
		class : 'b-button',
		icons : { primary : 'b-button-icon-accept b-button-icon' },
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.close();
		}
	}

	close() {
		this.deferred.resolve();

		this.contents.remove();
		this.contents = null;
	}

	done(callback: Function): Dialog {
		this.deferred.done(callback);

		return this;
	}

	fail(callback: Function): Dialog {
		this.deferred.fail(callback);

		return this;
	}

	init = function() {
		$(top.window).trigger('boom:dialog:open');

		this
			.contents
			.dialog(this.options)
			.ui();
	}

	open(): JQueryDeferred<any> {
		var self = this,
			$div = $('<div></div>');

		if (this.options.id) {
			$div.attr('id', this.options.id);
		}

		this.contents = $div.appendTo($(document).contents().find('body'));

		this.options.cancelButton && this.options.buttons.push(this.cancelButton);
		this.options.closeButton && this.options.buttons.push(this.closeButton);
		this.options.saveButton && this.options.buttons.push(this.saveButton);

		if (this.options.url && this.options.url.length) {
			if (this.contents.hasClass('ui-dialog-content')) {
				this.contents.dialog('open');
			} else {
				setTimeout(function() {
					self.contents.load(self.options.url, function(response, status, xhr) {
						if (xhr.status === 200) {
							self.init();

							if ($.isFunction(self.options.onLoad)) {
								self.options.onLoad.apply(self.dialog);
							}
						} else {
							self.deferred.reject(response, xhr.status);
						}
					})
				}, 100);
			}

		} else if (this.options.msg.length) {
			setTimeout(function() {
				self.contents.html(self.options.msg);
				self.init();

				if ($.isFunction(self.options.onLoad)) {
					self.options.onLoad(self);
				}
			}, 100);
		}

		return this.deferred;
	}

	private saveButton = {
		text: 'Save',
		class: 'b-button',
		icons: { primary : 'b-button-icon-save b-button-icon' },
		click: function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.close();
		}
	}
};