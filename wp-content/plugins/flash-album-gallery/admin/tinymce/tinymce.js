function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertFLAGLink() {
	
	var tagtext;
	
	var galleryname = document.getElementById('galleryname').value;
	var gallerywidth = document.getElementById('gallerywidth').value;
	var galleryheight = document.getElementById('galleryheight').value;
	var galorderby = document.getElementById('galorderby').value;
	var galorder = document.getElementById('galorder').value;
	var galexclude = document.getElementById('galexclude').value;
	var skinname = document.getElementById('skinname').value;
	var playlist = document.getElementById('playlist').value;
	var gallery = document.getElementById('galleries');
	var album = jQuery('#album').val();
	var len = gallery.length;
	var galleryid="";
	if(!album){
		for(i=0;i<len;i++)
		{
			if(gallery.options[i].selected) {
				if(galleryid=="") {
					galleryid = " gid=" + galleryid + gallery.options[i].value;
				} else {
					galleryid = galleryid + "," + gallery.options[i].value;
				}
			}
		}
	} else {
		galleryname = jQuery('#album option:selected').text();
		album = ' album='+album;
	}
	if (gallerywidth && galleryheight)
		var gallerysize = " w=" + gallerywidth + " h=" + galleryheight;
	else
		var gallerysize="";

	if (galleryid == 'all') {
		if (galorderby) {
			var galorderby = " orderby=" + galorderby;
		} 
		if (galorder) {
			var galorder = " order=" + galorder;
		}
		if (galexclude) {
			var galexclude = " exclude=" + galexclude;
		} 
	} else {
		var galorderby = '';
		var galorder = '';
		var galexclude = '';
	}
	if (skinname) {
		var skinname = " skin=" + skinname;
	} else var skinname = '';
	if (playlist) {
		var playlist = " play=" + playlist;
	} else var playlist = '';

	if (galleryid || album )
		tagtext = '[flagallery' + galleryid + album + ' name="' + galleryname + '"' + gallerysize + galorderby + galorder + galexclude + skinname + playlist + ']';
	else
		tinyMCEPopup.close();
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
