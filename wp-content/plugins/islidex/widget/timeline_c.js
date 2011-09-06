jQuery.noConflict(); if (typeof(window.$) === 'undefined') { window.$ = jQuery; }
jQuery(document).ready(function(){
	/* This code is executed after the DOM has been completely loaded */
	
	var totWidth=0;
	var positions = new Array();
	
	jQuery('#timesc .timec').each(function(i){
		
		/* Traverse through all the times and store their accumulative widths in totWidth */
		
		positions[i]= totWidth;
		totWidth += jQuery(this).width();
		
		/* The positions array contains each timec's commulutative offset from the left part of the container */
		
		if(!jQuery(this).width())
		{
			alert("Please, fill in width & height for all your images!");
			return false;
		}
		
	});
	
	jQuery('#timesc').width(totWidth);

	/* Change the cotnainer div's width to the exact width of all the times combined */

	jQuery('#times_menuc ul li a').click(function(e,keepScroll){

			/* On a thumbnail click */

			jQuery('li.timeItemc').removeClass('act').addClass('inact');
			jQuery(this).parent().addClass('act');
			
			var pos = jQuery(this).parent().prevAll('.timeItemc').length;
			
			jQuery('#timesc').stop().animate({marginLeft:-positions[pos]+'px'},450);
			/* Start the sliding animation */
			
			e.preventDefault();
			/* Prevent the default action of the link */
			
			
			// Stopping the auto-advance if an icon has been clicked:
			if(!keepScroll) clearInterval(itvl);
	});
	
	jQuery('#times_menuc ul li.timeItemc:first').addClass('act').siblings().addClass('inact');
	/* On page load, mark the first thumbnail as active */
	
	
	
	/*****
	 *
	 *	Enabling auto-advance.
	 *
	 ****/
	 
	var current=1;
	function autoAdvance()
	{
		if(current==-1) return false;
		
		jQuery('#times_menuc ul li a').eq(current%jQuery('#times_menuc ul li a').length).trigger('click',[true]);	// [true] will be passed as the keepScroll parameter of the click function on line 28
		current++;
	}

	// The number of seconds that the slider will auto-advance in:
	
	var changeEvery = 10;

	var itvl = setInterval(function(){autoAdvance()},changeEvery*1000);

	/* End of customizations */
});