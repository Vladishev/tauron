

jQuery(document).ready(function() {
   
   
   jQuery('.product-inner-hover').hover(function() {
  		var pozycja = jQuery(this).position(); 
		var whereX = pozycja.left;
		var whereY = pozycja.top;
		var rozmiarRodzica = jQuery(this).parent().height(); 
			jQuery(this).css({'position':'absolute','z-index':'99999',left:whereX, top:whereY}).find('.actions').show();
			jQuery(this).parent().css({'height':rozmiarRodzica});
   			}, function() {
   		    jQuery(this).css({'position':'relative','z-index':'1'}).find('.actions').hide();
  });
	


});