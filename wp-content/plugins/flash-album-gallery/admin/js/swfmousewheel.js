// RM: this is my own version of the swfmacmousewheel.js
// works with out registration, or the requirement of swfobject
// only applies scroll events to swf the mouse is currently over
// prevents browser scrolling on all OS's

// Good portion of code kindly borrowed from: SWFMacMouseWheel v2.0: Mac Mouse Wheel functionality in flash - http://blog.pixelbreaker.com/

var swfmousewheel = function () {
	
	var u = navigator.userAgent.toLowerCase();
	var p = navigator.platform.toLowerCase();
	var isMac = p ? /mac/.test(p) : /mac/.test(u);
	
	var handleWheelScroll = function(event) {
		
		var evt = event || window.event;
		var tgt = evt.target || evt.srcElement;
		if (tgt.nodeType == 3) { tgt = tgt.parentNode; } // safari edge case
		
		var typeAttr = tgt.getAttribute('type') || '';
		var classAttr = tgt.getAttribute('classid') || '';
		var isSWF = typeAttr.toLowerCase() == 'application/x-shockwave-flash' || classAttr.toLowerCase() == 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000';
		
		if (!isSWF) { return; } // not over a swf, let the browser deal with it
		
		var delta = 0;
		if (evt.wheelDelta) { // ie/opera
			delta = evt.wheelDelta / 120;
			if (window.opera) { delta = -delta; }

		} else if (evt.detail) { // mozilla
			delta = -evt.detail;
		}
				
		if (delta && isMac) {
			// we've scrolled.. pass info into swf if it can handle it
			if( typeof( tgt.externalMouseEvent ) == 'function' ) { 
				tgt.externalMouseEvent( delta );
			}
		}
		// RM: always prevent scroll events from reach the browser if we are over a swf
		if (evt.preventDefault) { evt.preventDefault(); }
		evt.returnValue = false;
				
	}
	
	// init
	if (window.addEventListener) { window.addEventListener('DOMMouseScroll', handleWheelScroll, false); } // mozilla
	window.onmousewheel = document.onmousewheel = handleWheelScroll; // ie/opera
	
}();