(function( $ ) {
		'use strict';

		/**
		* All of the code for your admin-facing JavaScript source
		* should reside in this file.
		*
		* Note: It has been assumed you will write jQuery code here, so the
		* $ function reference has been prepared for usage within the scope
		* of this function.
		*
		* This enables you to define handlers, for when the DOM is ready:
		*
		* $(function() {
		*
		* });
		*
		* When the window is loaded:
		*
		* $( window ).load(function() {
		*
		* });
		*
		* ...and/or other possibilities.
		*
		* Ideally, it is not considered best practise to attach more than a
		* single DOM-ready or window-load handler for a particular page.
		* Although scripts in the WordPress core, Plugins and Themes may be
		* practising this, we should strive to set a better example in our own work.
		*/
		jQuery(document).ready(function(){
			jQuery("#add_more").on('click' , function(){
 	
					var n = jQuery('#firstRow').clone().appendTo(jQuery('#cloneDiv'));
					//console.log(n);
					jQuery(n).addClass('more_rows').removeAttr('id');
					jQuery(n).find('th').html('');
					jQuery(n).find('input').val('');
					jQuery(n).find('td.forminp-number').append('<button class="button remove_rows" type="button" >Remove</button>');
					jQuery('.remove_rows').on('click',function(){ 
							jQuery(this).closest('.more_rows').remove(); 
						});
				});
	   			setTimeout(function(){
	   				jQuery('.remove_rows').on('click',function(){ 
						jQuery(this).closest('.more_rows').remove(); 
					});
	   			},500);
			
			});
	})( jQuery );
