jQuery(document).ready(function() {
	jQuery('div.flashalbum').bind("mouseenter",function(){
	  var obj_id = jQuery('object, embed',this).attr('id');
	  var flash = flagFind(obj_id);
	  if(flash && jQuery.isFunction(flash[obj_id])) {
		  flash[obj_id]("false");
		  console.log(flash[obj_id]);
	  }
    }).bind("mouseleave",function(){
	  var obj_id = jQuery('object, embed',this).attr('id');
	  var flash = flagFind(obj_id);
	  if(flash && jQuery.isFunction(flash[obj_id])) {
		  flash[obj_id]("true");
		  console.log(flash[obj_id]);
	  }
    });
});

function flagFind(flagName){
	if (window.document[flagName]){
		return window.document[flagName];
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1){
		if (document.embeds && document.embeds[flagName])
			return document.embeds[flagName];
	}
	if (document.getElementById(flagName)){
		return document.getElementById(flagName);
	}
	else {
		return false;
	}
}
