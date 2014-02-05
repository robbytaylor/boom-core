/**
Editable text slots
@class
@name chunkText
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkText', $.ui.chunk,

	/**
	@lends $.ui.chunkText
	*/
	{

	title : '',

	content : '',

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Text chunk slot edit');

		var $content = this.element.find( '.slot-content');

		var edit_content = function( $element ) {

			$element[0].id = $element[0].id || $.boom.util.dom.uniqueId('boom-dom-wysiwyg-');

			var old_html = self.element.html();

			if ( $element.text() == 'Default text.' ) {
				$element.html( '' );
			}
			self._bring_forward();

			$element.unbind('keydown');

			$('body').editor('edit', $element)
				.fail( function(){
					self.element.html( old_html ).show();
					self.destroy();
				})
				.done(function(html) {
					if ($element.text() == '') {
						self.remove();
					} else {
						self.insert(html);
					}
				})
				.always(function() {
					self._send_back();
				});

		};

		if ( $content.length ) {

			edit_content( $content );
			this.element
				.find( '.slot-title' )
				.attr( 'contenteditable', 'true' );

		} else {

			edit_content( this.element );
		}

	},

	/**
	Get the chunk HTML, escaped and cleaned.
	*/
	getData : function(){
		var $content = this.element.find( '.slot-content');

		if ( $content.length ) {
			this.content = $content.html();
			this.title = this.element.find( '.slot-title').text();
		} else {
			this.title = null;
			this.content = this.element.html();
		}

		return { title : this.title, text : this.content, is_block : this.element.attr('data-boom-is-block') };
	},

	/**
	Update the page with edited HTML from the editor, then remove TinyMCE.
	@param {String} replacedata HTML to insert into the page.
	*/
	insert : function(replacedata) {
		this.element.html(replacedata);

		return this._save();
	}
});