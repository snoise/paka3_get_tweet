jQuery(function($) {
				var link_array = []
				$(document).ready(function() {


				//チェックボックスをクリックしたら
				$(document).on('click','.pgp_chk', function(){
				 if($(this).attr('checked')){
					$(this).closest('.twt').css({background:"#ccccff"});
				 }else{
					$(this).closest('.twt').css({background:""});
				 }
			  });



				//OKクリックされたら
					$('#paka3_ei_btn_yes').on('click', function() {
						$('.img').css({ width:""});
						$('.twt_p_img').show();
						$('.twt').css({margin:"",border:""});

						var str = "";
						$('[name="pgp_chk"]:checked').each(function(){
								obj = $( '#twt_' + $(this).attr('value') );//.children('a');
							
								str_profile = obj.find('.twt').find('.profile').html();
								str_tweet   = obj.find('.twt').children('.tweet').html();
								if( obj.find('.twt').children('.img').length != 0 ){
									str_img  = obj.find('.twt').children('.img').html();
								}
	
								str   += "<ul class='twt'>"
											 + "<li class='img'>" +str_img+ "</li>"
											 + "<li class='profile'>" +str_profile+ "</li>"
											 + "<li class='tweet'>" +str_tweet+ "</li>"
											 + "</ul>";

						});
						
						top.send_to_editor( str );
						top.tb_remove(); 
					});


					$('#paka3_ei_btn_no').on('click', function() {
						top.tb_remove(); 
					});
					
				});

})