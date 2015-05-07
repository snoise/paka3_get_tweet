jQuery(function($) {

	//ソート
	$(".strBlock").sortable( {
		cursor : 'move',
		distance : 3,
		opacity : 0.5,
		tolerance : 'pointer'
	});

	//ここまで



				var link_array = []
				$(document).ready(function() {


				//チェックボックスをクリックしたら
				$(document).on('click','.pgp_chk', function(){
				 if($(this).attr('checked')){
						$(this).closest('.twt').css({background:"#ccccff"});
						str_profile = $(this).closest('.twt').find('.profile').html()
						str_tweet   = $(this).closest('.twt').children('.tweet').html();
						str_img = "";
						if( $(this).closest('.twt').children('.img').length != 0 ){
										str_img  = $(this).closest('.twt').children('.img').html();
						}

						//値を作る
						str=""
						if(str_img){
								str   += "<ul class='twt sort' id='t"+$(this).val()+"'>"
											 + "<li class='img'>" +str_img+ "</li>"
											 + "<li class='profile'><span class='tdel' title='"+ $(this).val() +"'>[ x ]</span>" +str_profile+ "</li>"
											 + "<li class='tweet'>" +str_tweet+ "</li>"
											 + "</ul>";
						}else{
								str   += "<ul class='twt sort' id='t"+$(this).val()+"'>"
											 + "<li class='profile'><span class='tdel' title='"+ $(this).val() +"'>[ x ]</span>" +str_profile+ "</li>"
											 + "<li class='tweet'>" +str_tweet+ "</li>"
											 + "</ul>";
						}
						//alert(str)
						html = $('div.strBlock').html()
						$('div.strBlock').html( html + str )

				 //クリックを取り消されたら
				 }else{
					$(this).closest('.twt').css({background:""});
					$("#t"+$(this).val()).remove();
				 }
			  });
				//バツをクリックしたら
				$(document).on('click','.tdel', function(){
					//alert($("#pgp_chk_"+$(this).attr('title')).val());
					$("#t"+$(this).attr('title')).remove();
					//alert($("#pgp_chk_"+$(this).attr('title')))
					$("#pgp_chk_"+$(this).attr('title')).trigger("click")
					
					$(this).remove();
				});
				//タイトルをクリック
				$(document).on('click','.h2t', function(){
						str = $(this).html()
						$(this).html("")
						$(this).append("<input type='text' value='"+str+"' />")
						$(this).children().focus();
						$(this).attr('class','h2t_edit')

						$(this).children().focus(function(){
						   //$(this).css("background","#b3eaef");
						 }).blur(function(){
						   $(this).parent().attr('class','h2t')
						   $(this).parent().html($(this).val()) 
						 });

				});


				//OKクリックされたら
					$('#paka3_ei_btn_yes').on('click', function() {
						//$('.img').css({ width:""});
						//$('.twt_p_img').show();
						//$('.twt').css({margin:"",border:""});

						var str = "";
						//$('[name="pgp_chk"]:checked').each(function(){
						//		obj = $( '#twt_' + $(this).attr('value') );//.children('a');
						//	
						//		str_profile = obj.find('.twt').find('.profile').html();
						//		str_tweet   = obj.find('.twt').children('.tweet').html();
						//		str_img = "";
						//		if( obj.find('.twt').children('.img').length != 0 ){
						//			str_img  = obj.find('.twt').children('.img').html();
						//		}
						//		
						//	if(str_img){
						//		str   += "<ul class='twt'>"
						//					 + "<li class='img'>" +str_img+ "</li>"
						//					 + "<li class='profile'>" +str_profile+ "</li>"
						//					 + "<li class='tweet'>" +str_tweet+ "</li>"
						//					 + "</ul>";
						//	}else{
						//		str   += "<ul class='twt'>"
						//					 + "<li class='profile'>" +str_profile+ "</li>"
						//					 + "<li class='tweet'>" +str_tweet+ "</li>"
						//					 + "</ul>";
						//	}

						//});

						$(".tdel").remove();
						str = $('div.strBlock').html()

						top.send_to_editor( str );
						top.tb_remove(); 
					});


					$('#paka3_ei_btn_no').on('click', function() {
						top.tb_remove(); 
					});
					
				});

})