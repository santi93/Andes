var fv = swfobject.getFlashPlayerVersion();
function FlAGClass(ExtendVar) {
  this.ExtendVar = ExtendVar;
  // Проверяем, используется ли jQuery на данном сайте
  if (typeof(jQuery) == 'undefined') { // Если нет, то подключаем
	var JQ = document.createElement('script');
	JQ.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
	JQ.type = 'text/javascript';
	document.getElementsByTagName('head')[0].appendChild(JQ);
  }

  waitJQ(); // Ожидаем подзагрузки и инициализации jQuery
}

function waitJQ() {
	if (typeof(jQuery) == 'undefined') {
	  window.setTimeout(waitJQ, 100); // Рекурсия каждые 100 миллисекунд, пока не загрузиться jQuery
	}
	else {
	  // После успешного определения jQuery проверяем, используется ли нужный нам плагин на данном сайте (в данном случае fancybox)
	  if (typeof(jQuery.fn.fancybox) == 'undefined') { // Если нет, то подключаем
		jQuery("head").append("<script type='text/javascript' src='"+this.ExtendVar+"admin/js/jquery.fancybox-1.3.4.pack.js'></script><link rel='stylesheet' href='"+this.ExtendVar+"admin/js/jquery.fancybox-1.3.4.css' type='text/css' media='screen' />");
	  }

	  waitFB(); // Ожидаем подзагрузки и инициализации плагина fancybox
	}
}

function waitFB() {
	if (typeof(jQuery.fn.fancybox) == 'undefined') {
	  window.setTimeout(waitFB, 100); // Рекурсия каждые 100 миллисекунд, пока не загрузиться fancybox
	}
	else {
	  jQuery(document).ready(function() {
		jQuery('.flag_alternate').each(function(i){
			jQuery(this).show();
			var catMeta = jQuery('.flagCatMeta',this).hide().get();
			for(j=0; j<catMeta.length; j++) {
				var catName = jQuery(catMeta[j]).find('h4').text();
				var catDescr = jQuery(catMeta[j]).find('p').text();
				var catId = jQuery(catMeta[j]).next('.flagcategory').attr('id');
				var act = '';
				if(j==0) act = ' active';
				jQuery('.flagcatlinks',this).append('<a class="flagcat'+act+'" href="#'+catId+'" title="'+catDescr+'">'+catName+'</a>');
			}
		});
		jQuery('.flag_alternate .flagcat').click(function(){
			if(!jQuery(this).hasClass('active')) {
				var catId = jQuery(this).attr('href');
				jQuery(this).addClass('active').siblings().removeClass('active');
				jQuery('.flag_alternate '+catId).show().siblings('.flagcategory').hide();
				alternate_flag_e(catId);
			}
			return false;
		});

		alternate_flag_e('.flagcategory:first');
	  });
	}
}

function alternate_flag_e(t){
	jQuery('.flag_alternate').find(t).not('.loaded').each(function(){
		var d = jQuery(this).html();
		if(d) {
			d = d.replace(/\[/g, '<');
			d = d.replace(/\]/g, ' />');
			jQuery(this).addClass('loaded').html(d);
		}
		jQuery(this).show();
		jQuery('a',this).fancybox({
			'overlayShow'	: true,
			'overlayOpacity': '0.5',
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'titlePosition'	: 'over',
			'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
				var descr = jQuery('img', currentArray[currentIndex]).attr("alt");
				title = jQuery('img', currentArray[currentIndex]).attr("title");
				return '<span id="fancybox-title-over"><em>'+(currentIndex + 1)+' / '+currentArray.length+' &nbsp; </em>'+(title.length? '<strong class="title">'+title+'</strong>' : '')+(descr.length? '<span class="descr">'+descr+'</span>' : '')+'</span>';
			},
			'onClosed' 		: function(currentArray, currentIndex){
				jQuery(currentArray[currentIndex]).removeClass('current').addClass('last');
			},
			'onComplete'	: function(currentArray, currentIndex) {
				jQuery(currentArray).removeClass('current last');
				jQuery(currentArray[currentIndex]).addClass('current');
			}
		});
	});
}
if(fv.major<10) {
	new FlAGClass(ExtendVar);
}