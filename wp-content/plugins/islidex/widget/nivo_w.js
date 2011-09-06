/*
 * jQuery Nivo Slider v1.9
 * http://nivo.dev7studios.com
 *
 * Copyright 2010, Gilbert Pellegrom
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * April 2010 - controlNavThumbs option added by Jamie Thompson (http://jamiethompson.co.uk)
 * March 2010 - manualAdvance option added by HelloPablo (http://hellopablo.co.uk)
 */

(function($) {

	$.fn.nivoSliderw = function(options) {

		//Defaults are below
		var settings = $.extend({}, $.fn.nivoSliderw.defaults, options);

		return this.each(function() {
			//Useful variables. Play carefully.
			var vars = {
				currentSlide: 0,
				currentImage: '',
				totalSlides: 0,
				randAnim: '',
				running: false,
				paused: false,
				stop:false
			};
		
			//Get this sliderw
			var sliderw = $(this);
			sliderw.data('nivo:vars', vars);
			sliderw.css('position','relative');
			sliderw.width('1px');
			sliderw.height('1px');
			sliderw.addClass('nivoSliderw');
			
			//Find our sliderw children
			var kids = sliderw.children();
			kids.each(function() {
				var child = $(this);
				if(!child.is('img')){
					if(child.is('a')){
						child.addClass('nivo-imageLinkw');
					}
					child = child.find('img:first');
				}
				//Don't ask
				var childWidth = child.width();
				if(childWidth == 0) childWidth = child.attr('width');
				var childHeight = child.height();
				if(childHeight == 0) childHeight = child.attr('height');
				//Resize the sliderw
				if(childWidth > sliderw.width()){
					sliderw.width(childWidth);
				}
				if(childHeight > sliderw.height()){
					sliderw.height(childHeight);
				}
				child.css('display','none');
				vars.totalSlides++;
			});
			
			//Set startSlide
			if(settings.startSlide > 0){
				if(settings.startSlide >= vars.totalSlides) settings.startSlide = vars.totalSlides - 1;
				vars.currentSlide = settings.startSlide;
			}
			
			//Get initial image
			if($(kids[vars.currentSlide]).is('img')){
				vars.currentImage = $(kids[vars.currentSlide]);
			} else {
				vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
			}
			
			//Show initial link
			if($(kids[vars.currentSlide]).is('a')){
				$(kids[vars.currentSlide]).css('display','block');
			}
			
			//Set first background
			sliderw.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
			
			//Add initial slices
			for(var i = 0; i < settings.slices; i++){
				var sliceWidth = Math.round(sliderw.width()/settings.slices);
				if(i == settings.slices-1){
					sliderw.append(
						$('<div class="nivo-slicew"></div>').css({ left:(sliceWidth*i)+'px', width:(sliderw.width()-(sliceWidth*i))+'px' })
					);
				} else {
					sliderw.append(
						$('<div class="nivo-slicew"></div>').css({ left:(sliceWidth*i)+'px', width:sliceWidth+'px' })
					);
				}
			}
			
			//Create caption
			if(settings.caption){
			sliderw.append(
				$('<div class="nivo-captionw"><p></p></div>').css({ display:'none', opacity:settings.captionOpacity })
			);			
			
			//Process initial  caption
			if(vars.currentImage.attr('title') != ''){
				$('.nivo-captionw p', sliderw).html(vars.currentImage.attr('title'));					
				$('.nivo-captionw', sliderw).fadeIn(settings.animSpeed);
			}
			}
			//In the words of Super Mario "let's a go!"
			var timer = 0;
			if(!settings.manualAdvance){
				timer = setInterval(function(){ nivoRun(sliderw, kids, settings, false); }, settings.pauseTime);
			}

			//Add Direction nav
			if(settings.directionNav){
				sliderw.append('<div class="nivo-directionNavw"><a class="nivo-prevNavw">Prev</a><a class="nivo-nextNavw">Next</a></div>');
				
				//Hide Direction nav
				if(settings.directionNavHide){
					$('.nivo-directionNavw', sliderw).hide();
					sliderw.hover(function(){
						$('.nivo-directionNavw', sliderw).show();
					}, function(){
						$('.nivo-directionNavw', sliderw).hide();
					});
				}
				
				$('a.nivo-prevNavw', sliderw).live('click', function(){
					if(vars.running) return false;
					clearInterval(timer);
					timer = '';
					vars.currentSlide-=2;
					nivoRun(sliderw, kids, settings, 'prev');
				});
				
				$('a.nivo-nextNavw', sliderw).live('click', function(){
					if(vars.running) return false;
					clearInterval(timer);
					timer = '';
					nivoRun(sliderw, kids, settings, 'next');
				});
			}
			
			//Add Control nav
			if(settings.controlNav){
				var nivoControl = $('<div class="nivo-controlNavw"></div>');
				sliderw.append(nivoControl);
				for(var i = 0; i < kids.length; i++){
					if(settings.controlNavThumbs){
						var child = kids.eq(i);
						if(!child.is('img')){
							child = child.find('img:first');
						}
						nivoControl.append('<a class="nivo-controlw" rel="'+ i +'"><img src="'+ child.attr('src').replace(settings.controlNavThumbsSearch, settings.controlNavThumbsReplace) +'"></a>');
					} else {
						nivoControl.append('<a class="nivo-controlw" rel="'+ i +'">'+ i +'</a>');
					}
					
				}
				//Set initial active link
				$('.nivo-controlNavw a:eq('+ vars.currentSlide +')', sliderw).addClass('active');
				
				$('.nivo-controlNavw a', sliderw).live('click', function(){
					if(vars.running) return false;
					if($(this).hasClass('active')) return false;
					clearInterval(timer);
					timer = '';
					sliderw.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
					vars.currentSlide = $(this).attr('rel') - 1;
					nivoRun(sliderw, kids, settings, 'control');
				});
			}
			
			//Keyboard Navigation
			if(settings.keyboardNav){
				$(window).keypress(function(event){
					//Left
					if(event.keyCode == '37'){
						if(vars.running) return false;
						clearInterval(timer);
						timer = '';
						vars.currentSlide-=2;
						nivoRun(sliderw, kids, settings, 'prev');
					}
					//Right
					if(event.keyCode == '39'){
						if(vars.running) return false;
						clearInterval(timer);
						timer = '';
						nivoRun(sliderw, kids, settings, 'next');
					}
				});
			}
			
			//For pauseOnHover setting
			if(settings.pauseOnHover){
				sliderw.hover(function(){
					vars.paused = true;
					clearInterval(timer);
					timer = '';
				}, function(){
					vars.paused = false;
					//Restart the timer
					if(timer == '' && !settings.manualAdvance){
						timer = setInterval(function(){ nivoRun(sliderw, kids, settings, false); }, settings.pauseTime);
					}
				});
			}
			
			//Event when Animation finishes
			sliderw.bind('nivo:animFinished', function(){ 
				vars.running = false; 
				//Hide child links
				$(kids).each(function(){
					if($(this).is('a')){
						$(this).css('display','none');
					}
				});
				//Show current link
				if($(kids[vars.currentSlide]).is('a')){
					$(kids[vars.currentSlide]).css('display','block');
				}
				//Restart the timer
				if(timer == '' && !vars.paused && !settings.manualAdvance){
					timer = setInterval(function(){ nivoRun(sliderw, kids, settings, false); }, settings.pauseTime);
				}
				//Trigger the afterChange callback
				settings.afterChange.call(this);
			});
		});
		
		function nivoRun(sliderw, kids, settings, nudge){
			//Get our vars
			var vars = sliderw.data('nivo:vars');
			if((!vars || vars.stop) && !nudge) return false;
			
			//Trigger the beforeChange callback
			settings.beforeChange.call(this);
					
			//Set current background before change
			if(!nudge){
				sliderw.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
			} else {
				if(nudge == 'prev'){
					sliderw.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
				}
				if(nudge == 'next'){
					sliderw.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
				}
			}
			vars.currentSlide++;
			if(vars.currentSlide == vars.totalSlides){ 
				vars.currentSlide = 0;
				//Trigger the slideshowEnd callback
				settings.slideshowEnd.call(this);
			}
			if(vars.currentSlide < 0) vars.currentSlide = (vars.totalSlides - 1);
			//Set vars.currentImage
			if($(kids[vars.currentSlide]).is('img')){
				vars.currentImage = $(kids[vars.currentSlide]);
			} else {
				vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
			}
			
			//Set acitve links
			if(settings.controlNav){
				$('.nivo-controlNavw a', sliderw).removeClass('active');
				$('.nivo-controlNavw a:eq('+ vars.currentSlide +')', sliderw).addClass('active');
			}
			
			//Process caption
			if(settings.caption){
			if(vars.currentImage.attr('title') != ''){
				if($('.nivo-captionw', sliderw).css('display') == 'block'){
					$('.nivo-captionw p', sliderw).fadeOut(settings.animSpeed, function(){
						$(this).html(vars.currentImage.attr('title'));
						$(this).fadeIn(settings.animSpeed);
					});
				} else {
					$('.nivo-captionw p', sliderw).html(vars.currentImage.attr('title'));
				}					
				$('.nivo-captionw', sliderw).fadeIn(settings.animSpeed);
			} else {
				$('.nivo-captionw', sliderw).fadeOut(settings.animSpeed);
			}
			}
			//Set new slice backgrounds
			var  i = 0;
			$('.nivo-slicew', sliderw).each(function(){
				var sliceWidth = Math.round(sliderw.width()/settings.slices);
				$(this).css({ height:'0px', opacity:'0', 
					background: 'url('+ vars.currentImage.attr('src') +') no-repeat -'+ ((sliceWidth + (i * sliceWidth)) - sliceWidth) +'px 0%' });
				i++;
			});
			
			if(settings.effect == 'random'){
				var anims = new Array("sliceDownRight","sliceDownLeft","sliceUpRight","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","fade");
				vars.randAnim = anims[Math.floor(Math.random()*(anims.length + 1))];
				if(vars.randAnim == undefined) vars.randAnim = 'fade';
			}
		
			//Run effects
			vars.running = true;
			if(settings.effect == 'sliceDown' || settings.effect == 'sliceDownRight' || vars.randAnim == 'sliceDownRight' ||
				settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft'){
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slicew', sliderw);
				if(settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft') slices = $('.nivo-slicew', sliderw).reverse();
				slices.each(function(){
					var slice = $(this);
					slice.css('top','0px');
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ sliderw.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUp' || settings.effect == 'sliceUpRight' || vars.randAnim == 'sliceUpRight' ||
					settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft'){
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slicew', sliderw);
				if(settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft') slices = $('.nivo-slicew', sliderw).reverse();
				slices.each(function(){
					var slice = $(this);
					slice.css('bottom','0px');
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ sliderw.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUpDown' || settings.effect == 'sliceUpDownRight' || vars.randAnim == 'sliceUpDown' || 
					settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft'){
				var timeBuff = 0;
				var i = 0;
				var v = 0;
				var slices = $('.nivo-slicew', sliderw);
				if(settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft') slices = $('.nivo-slicew', sliderw).reverse();
				slices.each(function(){
					var slice = $(this);
					if(i == 0){
						slice.css('top','0px');
						i++;
					} else {
						slice.css('bottom','0px');
						i = 0;
					}
					
					if(v == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ sliderw.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					v++;
				});
			} 
			else if(settings.effect == 'fold' || vars.randAnim == 'fold'){
				var timeBuff = 0;
				var i = 0;
				$('.nivo-slicew', sliderw).each(function(){
					var slice = $(this);
					var origWidth = slice.width();
					slice.css({ top:'0px', height:'100%', width:'0px' });
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed, '', function(){ sliderw.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			}  
			else if(settings.effect == 'fade' || vars.randAnim == 'fade'){
				var i = 0;
				$('.nivo-slicew', sliderw).each(function(){
					$(this).css('height','100%');
					if(i == settings.slices-1){
						$(this).animate({ opacity:'1.0' }, (settings.animSpeed*2), '', function(){ sliderw.trigger('nivo:animFinished'); });
					} else {
						$(this).animate({ opacity:'1.0' }, (settings.animSpeed*2));
					}
					i++;
				});
			}
		}
	};
	
	//Default settings
	$.fn.nivoSliderw.defaults = {
		caption:true,
		effect:'random',
		slices:99,
		animSpeed:500,
		pauseTime:3000,
		startSlide:0,
		directionNav:false,
		directionNavHide:true,
		controlNav:true,
		controlNavThumbs:false,
		controlNavThumbsSearch: '.jpg',
		controlNavThumbsReplace: '_thumb.jpg',
		keyboardNav:true,
		pauseOnHover:true,
		manualAdvance:false,
		captionOpacity:0.8,
		beforeChange: function(){},
		afterChange: function(){},
		slideshowEnd: function(){}
	};
	
	$.fn.reverse = [].reverse;
	
})(jQuery);