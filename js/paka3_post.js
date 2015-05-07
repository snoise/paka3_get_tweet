jQuery(function($) {

//初回、ページの読み込みが完了したら実行
$(function(){
  $(window).load(function () {
    $('#getPostsSubmit').trigger("click");
  });
});

$(document).ready(function(){
  //Enterキーが入力されたとき
  $('#sword').on('keypress',function () {
    if(event.which == 13) {
      $('#o_sword').val('');
      $('#getPostsSubmit').trigger("click");
    }
      //Form内のエンター：サブミット回避
      return event.which !== 13;
    });
});

$(document).on('click','#beforeID',function () {
       if($('#o_sword').val()==""){
         $('#o_sword').val($('#sword').val());
       }
       $('#sword').val($('#o_sword').val() +" "+ $('#beforeID').attr('title'));
       $('#getPostsSubmit').trigger("click");  
       $('#sword').val($('#o_sword').val());
});

//読み込み関数

$(document).ready(function(){
    //ローディグ画像の非表示とボタン表示
    $('#loadingmessage').hide();
    $('#getPostsSubmit').removeAttr("disabled");
    //タイトル
    $(document).on('click','#h2TitleButton', function(){
        //id = jQuery.now( ) 
        html = $('div.strBlock').html()
        $('div.strBlock').html(  "<h2 class='h2t'>タイトル</h2>"+html)
    });

    //
    $(document).on('click','#getPostsSubmit', function(){

       
        var $sword = $("#sword").val();
        //枠を空白に

        $("#res").empty();
        $("#beforeID").remove()
        //ローディグ画像の表示とボタン非表示
        $('#loadingmessage').show();
        //$('#loadingmessage').hide();
        
        $('#getPostsSubmit').attr("disabled", "disabled");
        $.post(
           paka3GetTweet.ajaxurl,
              {
                 action : 'paka3_getTweet_action',
                 security : paka3GetTweet.security,
                 sword : $sword,
              },
              function( response ) {
                console.log( response );
                var max_id;
                for(var i in response){
                  
                  if(i==0){ max_id = response[i].tweet_id;}
                  
                  //1
                  $("#res").append( '<li id="twt_'+response[i].tweet_id+'"></li>' );
                  obj = $("#twt_"+response[i].tweet_id);
                  //2
                  twtdata = '<label for="pgp_chk_'+response[i].tweet_id+'"><ul class="twt">'
                          + '<li><input type="checkbox" class="pgp_chk" name="pgp_chk" id="pgp_chk_'+response[i].tweet_id+'" value="'+response[i].                  tweet_id+'" />'
                          + '<span class="profile"><a rel="nofollow" href="'+response[i].user_url+'">'+response[i].profile_img+response[i].user_name+                 '<b>'+response[i].user_account+'</b></a></span>'
                          +'</li>'
                  
                          +'<li class="tweet">'+response[i].tweet+'<a href="'+response[i].link+'" rel="nofollow" class="date">'+response[i].date+'                  参照元:twitter.com</a></li>';
                  
                  if( response[i].img ){
                    twtdata += '<li class="img"><a rel="nofollow" href="'+response[i].imgURL+'" target="_blank">'+response[i].img+'</a></li>';
                  }
                  twtdata += '</ul></label>'
                  obj.append(twtdata);
                }
                if(max_id){

                  $(".resblock").prepend( '<button id="beforeID" type="button" title="max_id:'+max_id+'">前へ</button>');
                }
                
               
                //$('.twt').css({margin:"10px 0",border:"0 0 2px 0",border:"dotted #ccc"});
                //$('.img').css({ width:"30px"});

                //$('.twt_p_img').hide();
                
                $('#loadingmessage').hide();
                $('#getPostsSubmit').removeAttr("disabled");
            }
          );
       return false;
    });	
   


  });
});



