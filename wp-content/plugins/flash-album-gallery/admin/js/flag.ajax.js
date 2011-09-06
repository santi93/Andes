/*
 * Ajax Plugin for Flash Album Gallery
 */ 
(function($) {
flagAjax = {
		settings: {
			url: flagAjaxSetup.url, 
			type: "POST",
			action: flagAjaxSetup.action,
			operation : flagAjaxSetup.operation,
			nonce: flagAjaxSetup.nonce,
			ids: flagAjaxSetup.ids,
			permission: flagAjaxSetup.permission,
			error: flagAjaxSetup.error,
			failure: flagAjaxSetup.failure,
			timeout: 10000
		},
	
		run: function( index ) {
			s = this.settings;
			var req = $.ajax({
				type: "POST",
			   	url: s.url,
			   	data:"action=" + s.action + "&operation=" + s.operation + "&_wpnonce=" + s.nonce + "&image=" + s.ids[index],
			   	cache: false,
			   	timeout: 10000,
			   	success: function(msg){
			   		switch ( parseInt(msg) ) {
			   			case -1:
					   		flagProgressBar.addNote( flagAjax.settings.permission );
						break;
			   			case 0:
					   		flagProgressBar.addNote( flagAjax.settings.error );
						break;
			   			case 1:
					   		// show nothing, its better
						break;
						default:
							// Return the message
							flagProgressBar.addNote( "<strong>ID " + flagAjax.settings.ids[index] + ":</strong> " + flagAjax.settings.failure, msg );
						break; 			   			
			   		}

			    },
			    error: function (msg) {
					flagProgressBar.addNote( "<strong>ID " + flagAjax.settings.ids[index] + ":</strong> " + flagAjax.settings.failure, msg.responseText );
				},
				complete: function () {
					index++;
					flagProgressBar.increase( index );
					// parse the whole array
					if (index < flagAjax.settings.ids.length)
						flagAjax.run( index );
					else 
						flagProgressBar.finished();
				} 
			});
		},

		readIDs: function( index ) {
			s = this.settings;
			var req = $.ajax({
				type: "POST",
			   	url: s.url,
			   	data:"action=" + s.action + "&operation=" + s.operation + "&_wpnonce=" + s.nonce + "&image=" + s.ids[index],
			   	dataType: "json",
	   			cache: false,
			   	timeout: 10000,
			   	success: function(msg){
  					// join the array
			 		imageIDS = imageIDS.concat(msg);
				},
			    error: function (msg) {
					flagProgressBar.addNote( "<strong>ID " + flagAjax.settings.ids[index] + ":</strong> " + flagAjax.settings.failure, msg.responseText );
				},
				complete: function () {
					index++;
					flagProgressBar.increase( index );
					// parse the whole array
					if (index < flagAjax.settings.ids.length)
						flagAjax.readIDs( index );
					else {
						// and now run the image operation
						index  = 0;
						flagAjax.settings.ids = imageIDS;
						flagAjax.settings.operation = nextOperation;
						flagAjax.settings.maxStep = imageIDS.length;
						flagProgressBar.init( flagAjax.settings );
						flagAjax.run( index );
					}
				} 
			});
		},
	
		init: function( s ) {

			var index  = 0;
								
			// get the settings
			this.settings = $.extend( {}, this.settings, {}, s || {} );
			
			// a gallery operation need first all image ids via ajax
			if ( this.settings.operation.substring(0, 8) == 'gallery_' ) {
				nextOperation = this.settings.operation.substring(8);
				//first run, get all the ids
				this.settings.operation = 'get_image_ids';
				imageIDS = new Array();
				this.readIDs( index );
			} else {
				// start the ajax process
				this.run( index );				
			}
		}
	}

}(jQuery));