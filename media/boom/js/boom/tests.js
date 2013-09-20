/**
@fileOverview qUnit tests.
*/
module('Text helpers');
test('cleanup() : fix broken HTML', function(){
	
	var test_string = '<p lang="vo" dir="ltr" xml:lang="vo">\
	Dagetön degans neflenis lä nek, <span title="hello">fe suemön tidäbis fut</span>, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,<br />\
	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, \
	\
	degolön degtelis is sin.\
	  </p>';
	var result = '<p lang="vo" dir="ltr" xml:lang="vo">\
	Dagetön degans neflenis lä nek, <span title="hello">fe suemön tidäbis fut</span>, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,<br />\
	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, \
	\
	degolön degtelis is sin.\
	  </p>';
	
	equal( test_string.cleanup(), result);
	
});
test('text() : strip tags', function(){
	
	var test_string = '<p lang="vo" dir="ltr" xml:lang="vo">\
	Dagetön degans neflenis lä nek, <span title="hello">fe suemön tidäbis fut</span>, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,<br />\
	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, \
	\
	degolön degtelis is sin.\
	  </p>';
	var result = 'Dagetön degans neflenis lä nek, fe suemön tidäbis fut, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, 		degolön degtelis is sin.';
	
	equal( test_string.text(), result);
	
});
test('encodeHTML() : encode HTML as entities.', function(){
	
	var test_string = '<p lang="vo" dir="ltr" xml:lang="vo">\
	Dagetön degans neflenis lä nek, <span title="hello">fe suemön tidäbis fut</span>, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,<br />\
	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, \
	\
	degolön degtelis is sin.\
	  </p>';
	var result = '&lt;p lang="vo" dir="ltr" xml:lang="vo"&gt;	Dagetön degans neflenis lä nek, &lt;span title="hello"&gt;fe suemön tidäbis fut&lt;/span&gt;, ekälols sinanas mö ome. Mö jep logonsös ninälo. Gug gold-li lomioköm vi. Hipul vönaoloveikod nog iv,&lt;br&gt;	ata me isio laka maita. Kusadön oglidols spearükon of vat, jol bethphage kultans olenükobs-li de, pos me badani futabami osämikebobs. Jigok temakäd fid zü, 		degolön degtelis is sin.	  &lt;/p&gt;';
	
	equal( test_string.encodeHTML(), result);
	
});

module('Util');
test('url.addQueryStringParams() : should add params to the current page URL.', function(){
	
	var test_params = (window.location.search == '') ? '?' : window.location.search + '&';
	var base_url = window.location.protocol + '//' + window.location.host + window.location.pathname + test_params;
	var params = {
		'number' : 1,
		'text' : 'hello world'
	};
	var result = base_url + 'number=1&text=hello+world';
	
	equal(
		$.boom.util.url.addQueryStringParams( params, true ),
		result
	);
});
test('obj.search() : should find a single value in an object.', function(){
	
	var t = 'hello world';
	var o = {};
	o.a = 1;
	o.b = {};
	o.c = 'helloworld';
	o.b.a = 'hello world';
	o.b.b = 'hello';
	o.b.c = 42;
	
	equal(
		$.boom.util.obj.search(o, t, true),
		'b.a'
	);
});
test('obj.search() : should find multiple values in an object.', function(){
	
	var t = 'hello world';
	var o = {};
	o.a = 1;
	o.b = {};
	o.c = 'hello world';
	o.b.a = 'hello world';
	o.b.b = 'hello';
	o.b.c = 42;
	
	var r = [];
	r.push("b.a");
	r.push("c");
	
	deepEqual(
		$.boom.util.obj.search(o, t, true),
		r
	);
});
test('obj.search() : should find partial value in an object.', function(){
	
	var t = 'hello';
	var o = {};
	o.a = 1;
	o.b = {};
	o.c = 'world';
	o.b.a = 'hello world';
	o.b.b = 'world';
	o.b.c = 42;
	
	equal(
		$.boom.util.obj.search(o, t, false),
		'b.a'
	);
});
test('obj.search() : should find multiple partial values in an object.', function(){
	
	var t = 'world';
	var o = {};
	o.a = 1;
	o.b = {};
	o.c = 'world';
	o.b.a = 'hello world';
	o.b.b = 'world';
	o.b.c = 42;
	
	deepEqual(
		$.boom.util.obj.search(o, t, false),
		['b.a', 'b.b', 'c']
	);
});

module("History");  
test("getHash() : should return the current location hash", function(){
	
	var hash = window.location.hash;

	window.location.hash = '#1';

	equal(
		$.boom.history.getHash(),  
		'1'
	);
	
	window.location.hash = hash;
});  
test("route() : should fire hashChange callback on hash change", function(){
	
	var thehash = '';
	var cur_hash = window.location.hash;

	window.location.hash = '';

	function test(hash){
		thehash = hash;
	}

	$.boom.history.init();
	$.boom.history.route(test, function(){});

	window.location.hash = '#1';
	
	stop();

	setTimeout(function(){

		start();

		equal(
			thehash,
			'1'
		);

		window.location.hash = cur_hash;
	}, 300);

});
test("route() : should fire the noHash callback when no hash is present.", function(){
	
	var thehash = '';
	var cur_hash = window.location.hash;

	window.location.hash = '';

	function test(){
		thehash = 'pass';
	}
	
	function fail(hash){
		thehash = 'fail';
	}

	$.boom.history.init();
	$.boom.history.route( fail, test);

	
	stop();

	setTimeout(function(){

		start();

		equal(
			thehash,
			'pass'
		);

		window.location.hash = cur_hash;
	}, 300);

});
test('load() : should change location hash and fire callback function', function(){

	var thehash;
	var cur_hash = window.location.hash;

	window.location.hash = '';

	function test(hash){
		thehash = hash;
	}

	$.boom.history.init(test);
	$.boom.history.load('1');

	stop();

	setTimeout(function(){

		start();

		equal(
			window.location.hash,
			'#1'
		);	

		window.location.hash = cur_hash;
	}, 300);
});

module("Tree", {
	/** @ignore */
	setup: function(){
		var treeConfig = {
			/** @ignore */
			onClick: function(){
				return $(this);
			},
			/** @ignore */
			onToggle: function( page_id ) {
				var list_ready = new $.Deferred();
				
				var $children = $('<ul><li><a rel="5" href="#">Item 5</a></li><li><a rel="6" href="#">Item 6</a></li></ul>');
					$children
						.data( 'children', 2);
				
				var $children2 = $('<ul><li><a rel="7" href="#">Item 7</a></li><li><a rel="8" href="#">Item 8</a></li></ul>');

				if (page_id == 3) {
					list_ready.resolve( { childList: $children } );
				}
				
				if (page_id == 5) {
					setTimeout( function(){
						list_ready.resolve( { childList: $children2 } );
					}, 100);
				} 
				
				return list_ready;
			}
		};
		
		treeConfig = $.extend({}, $.boom.config.tree, treeConfig);
		this.tree = $('<ul><li><a rel="1" href="#">Item 1</a><ul><li><a rel="3" href="#">Item 3</a></li><li><a rel="4" href="#">Item 4</a></li></ul></li><li><a rel="2" href="#">Item 2</a></li></ul>').prependTo($('body')).tree( treeConfig );
	},
	/** @ignore */
	teardown: function() {
		this.tree.tree( 'destroy' ).remove();
	}
});
test('onClick callback', function(){
	
	this.tree.find('li > a').each( function(){
		var i = $(this).click().attr('rel');
		
		equal(
			$(this).attr('rel'),
			i
		);
	});
});
test('add_item()', function(){
	
	var new_item = $( '<li><a rel="5" href="#">WOO</a></li>');
	this.tree.tree( 'add_item', new_item );
	var i = $( new_item ).find( '> a' ).click();
	
	equal(
		i.attr( 'rel' ),
		'5'
	);
	equal(
		i.text(),
		'WOO'
	);
	equal(
		this.tree.find( '> li' ).length,
		3
	);
});
test('toggle( $item )', function(){
	
	var $item = this.tree.find( 'a[rel=1]' );
	var child_list = $item.siblings( 'ul' );
	var visibility = $( child_list ).is( ':visible' );
	
	$item.siblings( 'span.boom-tree-hitarea' ).click();
	
	equal(
		$( child_list ).is( ':visible' ),
		!visibility
	);
	$item.siblings( 'span.boom-tree-hitarea' ).click();
	equal(
		$( child_list ).is( ':visible' ),
		visibility
	);
});
asyncTest('onToggle callback', function(){
	
	var self = this;
	
	this.tree.find( 'a[rel=3]' ).siblings( 'span.boom-tree-hitarea' ).click();
	this.tree.find( 'a[rel=5]' ).siblings( 'span.boom-tree-hitarea' ).click();

	
	setTimeout( function(){
		var $link = self.tree.find( 'ul a[rel=7]' );
		equal(
			$link.length,
			1
		);
		equal(
			$link.attr( 'rel' ),
			7
		);
		equal(
			self.tree.find( 'a[rel=5]' ).siblings( 'ul' ).is( ':visible' ),
			true
		);
		start();
	}, 500);
	
});

module("Dialog");  
asyncTest("open a simple dialog box", function(){

	$.boom.dialog.open({
		msg: 'hi there'
	});
	
	equal(
		$( 'div[role=dialog]').length,
		1
	);
	equal(
		$( '.ui-dialog-content' ).html(),
		'hi there'
	);
	
	$.boom.dialog.destroy();
	start();
	
});

asyncTest("check default dialog buttons", function(){

	$.boom.dialog.open({
		msg: 'hi there'
	});
	
	var labels = [];
	
	$( '.ui-dialog-buttonpane button' ).each( function(){
		labels.push( $(this).text() );
	});

	equal(
		labels[0],
		'Cancel'
	);
	equal(
		labels[1],
		'Okay'
	);
	
	$.boom.dialog.destroy();
	
	start();
});
asyncTest( "close the dialog when 'close' is clicked", function(){
	
	$.boom.dialog.open({
		msg: 'hi there'
	});
	
	$( '.ui-dialog-titlebar-close' ).click();
	
	equal(
		$( 'div[role=dialog]').length,
		0
	);
	
	start();
	
});
asyncTest( "fire a callback and close the dialog when 'ok' is clicked", function(){
	
	var test = 0;
	
	var dialog = $.boom.dialog.open({
		msg: 'hi there',
		callback: function(){
			test = 1;
			start();
		}
	});
	
	$( '.ui-dialog-buttonpane button' )[1].click();
	
	equal(
		test,
		1
	);
	equal(
		$( 'div[role=dialog]').length,
		0
	);
	
});
asyncTest( "chain done callbacks using a deferred object", function(){
	
	var test = 0;
	var promise = new $.Deferred();
	
	promise
	.done( function(){
		ok( true, 'OK button was clicked');
		start();
	});
	
	var dialog = $.boom.dialog.open({
		msg: 'hi there',
		deferred: promise
	});
	
	$( '.ui-dialog-buttonpane button' )[1].click();
	equal(
		$( 'div[role=dialog]').length,
		0,
		'Dialog closed.'
	);
	
	
	
});
asyncTest( "chain fail callbacks using a deferred object", function(){
	
	var test = 0;
	var promise = new $.Deferred();
	
	promise
	.fail( function(){
		ok( true, 'Cancel button was clicked');
		start();
	});
	
	var dialog = $.boom.dialog.open({
		msg: 'hi there',
		deferred: promise
	});
	
	$( '.ui-dialog-buttonpane button' )[0].click();
	equal(
		$( 'div[role=dialog]').length,
		0,
		'Dialog closed.'
	);
	
	
	
});
asyncTest( "cancel the action and close the dialog when 'cancel' is clicked", function(){
	
	var test = 0;
	
	var dialog = $.boom.dialog.open({
		msg: 'hi there',
		callback: function(){
			test = 1;
		}
	});
	
	$( '.ui-dialog-buttonpane button' )[0].click();
	
	equal(
		test,
		0
	);
	equal(
		$( 'div[role=dialog]').length,
		0,
		'Dialog closed.'
	);
	
	start();
	
});

module("Asset manager", {
	/** @ignore */
	setup: function(){
		
		$( 'body' ).append( '<div class="b-items-rightpane"><div class="b-items-content">asset manager</div></div>');
		
		$.boom.init('assets', {
		});
		
		$( 'body' ).browser_asset();
	},
	/** @ignore */
	teardown: function(){
		$( 'body' ).browser_asset( 'destroy' );
		$( '.b-items-rightpane' ).remove();
	}
});
test('$.boom.type == "assets"', function(){
	
	equal(
		$.boom.type,
		'assets'
	);
	
});
asyncTest('Changing the hash changes the asset item', function(){
	
	$.boom.history
		.load('asset/7759')
		.done( function(){
			equal(
				$( 'body' ).data( 'boom-browser_asset' ).url_map.asset.rid,
				7759
			);
			window.location.hash = '';
			start();
		});
	
	
});
test('Editing an asset changes the selected asset ID', function(){
	
	$( 'body' ).browser_asset( 'edit', 7759 );
	
	equal(
		$( 'body' ).browser_asset( 'get_asset' ),
		7759
	);
	
});
asyncTest( 'Clicking an asset returns the asset ID', function(){
	
	var $link;
	var asset_id;
	
	$.boom.history
		.load('tag/0')
		.done( function(){
			$link = $( '#b-items-view-thumbs a:first');
			asset_id = $link.attr( 'href' ).split( '/' )[1];
			$link.click();
		});
		
	$.when( $( 'body' ).browser_asset( 'browse' ) )
	.progress( function( rid ){
		equal(
			rid,
			parseInt( asset_id, 10 ),
			'asset ' + rid + ' selected.'
		);
		start();
	});

});
module("People manager", {
	/** @ignore */
	setup: function(){
		
		$( 'body' ).append( '<div class="b-items-sidebar"></div><div class="b-items-rightpane"><div class="b-items-content">people manager</div></div>');
		
		$.boom.init('people', {
		});
		
		$( 'body' ).browser_people();
	},
	/** @ignore */
	teardown: function(){
		$( 'body' ).browser_people( 'destroy' );
		$( '.b-items-rightpane' ).remove();
	}
});
test('$.boom.type == "people"', function(){
	
	equal(
		$.boom.type,
		'people'
	);
	
});
asyncTest('Changing the hash changes the person item', function(){
	
	$.boom.history
		.load('person/3')
		.always( function(){
			equal(
				$( 'body' ).data( 'boom-browser_people' ).url_map.person.rid,
				3
			);
			window.location.hash = '';
			start();
		});
	
});
asyncTest('edit a group opens the "Edit group" panel', function(){
	var browser = $( 'body' ).data( 'boom-browser_people' );
	console.log( browser );
	
	// FIXME: group edit has to be triggered from a link inside an li, otherwise the code fails.
	$('<a href="#tag/13" rel="13">Group</a>')
		.click(function(e){
			browser.sidebar.group_editor( 'edit', e )
				.done( function(){
					equal(
						$( 'div#b-group-roles-general').length,
						1,
						'editor loaded'
					);
					equal(
						$.trim( $('#b-people-group-name').val() ),
						'Blog Editors',
						'group name verified'
					);
					start();
				});
		})
		.appendTo(
			$('<li></li>')
			.appendTo(
				$('<ul></ul>')
			)
		)
		.trigger('click');

});