<?php
/*
Plugin Name: Paka3_get_tweet
Plugin URI: http://www.paka3.com/wpplugin
Description: Twitterから検索してツイートを取得するポップアップウィンドウ
Author: Shoji ENDO
Version: 0.1
Author URI:http://www.paka3.com/
*/
 
 
$paka3_get_tweet= new Paka3_get_tweet( );
//$p3EMB->my_action_callback();
//$paka3_get_tweet->my_action_callback();

class Paka3_get_tweet
{
	//###########################################
	//APIキー
	
	private $apiKey = '***********************' ;
	private $apiSecret = '***********************' ;
	private $accessToken = '***********************' ;
	private $accessTokenSecret = '************************' ;
	

	//###########################################
	private $lang = "ja";	//言語
	private $word = "";				//検索ワード


	public function __construct(){
		add_filter( "media_buttons_context" , array( &$this, "paka3_media_buttons_context" ) );
		//ポップアップウィンドウ
		//media_upload_{ $type }
		add_action('media_upload_paka3GetTweetType', array( &$this,'paka3_wp_iframe' ) );
		//クラス内のメソッドを呼び出す場合はこんな感じ。
		add_action( "admin_head-media-upload-popup", array( &$this, "paka3_head" ) );
 
		if( is_admin() ){
			//*ログインユーザ   
			add_action('wp_ajax_paka3_getTweet_action',array($this,'my_action_callback'));
		}
		if ( !class_exists('Paka3_tweet_json_to_view') ) {
					require_once( "paka3_tweet_json_to_view.php" );
		}
		if ( !class_exists('Paka3_task_tweet_view') ){
		add_action( 'wp_enqueue_scripts' , array( 'Paka3_tweet_json_to_view' , 'post_css' ) ) ;
		}

		
	}
 

	public function paka3_head(){
		global $type;
		if( $type == "paka3GetTweetType" ){

			//ポップアップで使うjavascript
			wp_enqueue_script( 'paka3_popup', plugin_dir_url( __FILE__ ) . '/js/paka3_popup.js', array( 'jquery' ));
 
			//既存記事を取得するajaxで使うjavascript
			wp_enqueue_script( 'paka3_submit', plugin_dir_url( __FILE__ ) . '/js/paka3_post.js', array( 'jquery' ));	
			wp_localize_script( 'paka3_submit', 'paka3GetTweet', array(
				'ajaxurl'		=>	admin_url( 'admin-ajax.php' ),
				'security'	=>	wp_create_nonce( get_bloginfo('url').'paka3GetTweet' ))
			) ;

			wp_enqueue_script("jquery-ui-sortable");
//wp_enqueue_script( 'jquery-ui-core' );
//wp_enqueue_script( 'jquery-ui-mouse' );



			//ポップアップ画面のCSS
			echo <<< EOS
			<style type="text/css">
			div#media-upload-header{
				display:none;
			}
				form{
					padding-bottom:80px;
					margin-top:0;
				}
				h2#popupTitle
				{
					background:#fff;
					padding:10pt 0;margin:0;
					display:none;
				}

				div.resblock{
					border:1px solid #eee;
					padding:5pt 0;
					min-height:100pt;
					margin-bottom:100px;
					overflow:auto;
				}
				div.resblock .img{
					width:30px;
				}
				.twt_p_img{
					display:none;
				}
				div.resblock  .twt{
					padding:10px 0;
					border:2px dotted #ccc;
					border-width:0 0 2px 0;
					margin:0 2pt;
				}
EOS;
if(wp_is_mobile()){
echo <<< EOS
				div.resblock{
					height:300px;
					overflow:scroll;
				}
EOS;
}
echo <<< EOS
				div#popup_button_area{
					position:fixed;
					width:100%;height:30px;
					background:#efefef;
					padding:10px 10px;
					bottom:0px;z-index:10;
				}
			</style>
EOS;
		}
	}
 
	//##########################
	//メディアボタンの表示
	//##########################
	public function paka3_media_buttons_context ( $context ) {
		$img = plugin_dir_url( __FILE__ ) ."icon.png";
		$link = "media-upload.php?tab=paka3GetTweetTab&type=paka3GetTweetType&TB_iframe=true&width=600&height=400";
 
		$context .= <<<EOS
    <a href='{$link}'
    class='thickbox' title='Twitterから取得するぜ！'>
      <img src='{$img}' /></a>
EOS;
		return $context;
	}
 
 
	//##########################
	//ポップアップウィンドウ
	//##########################
	function paka3_wp_iframe() {
		wp_iframe(array( $this , 'media_paka3_get_tweet_form' ) );
	}
 
	//関数名をmedia_***としないとスタイルシートが適用されない謎
	function media_paka3_get_tweet_form() {
		add_filter( "media_upload_tabs", array( &$this, "paka3_upload_tabs" ) ,1000);
		media_upload_header();
 
		$dirUrl = plugin_dir_url( __FILE__ );
		echo <<< EOS
		<style>
				.resblock{
						width:49%;
						display:inline-block;

				}
				div.strBlockBox{
					position:fixed;
					height:100%;
					top:0;
					width:49%;
					display:inline-block;
				}
				div.strBlock{
						height:90%;

						min-height:100pt;
						margin-bottom:100px;
						overflow:auto;
				}
				div.strBlock .twt{
						border-bottom:2px solid #ccc;
						clear:both;
						height:60px;
						cursor : move;
						margin:0 2pt;
				}
				div.strBlock h2{
						font-size:12pt;
						cursor:move;
				}
				div.strBlock .twt .profile{
					white-space:nowrap;
					overflow:hidden;
				}
				div.strBlock .twt .tweet{
					font-size:50%;white-space:nowrap;
					overflow:hidden;
				}
				div.strBlock .twt .img{
					float:left;
					width:50px;max-height:30px;
				}

		</style>
			<div id="paka3_popup_window" style="background:#fff">
			<form>
				<h2 id="popupTitle">ツイッターからツイートを取得する</h2>
			<div style="width:100%">
			<input type="text" id="sword" size="20" value="{$this->word}" style="display:inline">
			
			<button type="button" class="button" id="getPostsSubmit">検索する</button>
						<button type="button" class="button" id="h2TitleButton">h2挿入</button>
                        <input type="hidden" id="o_sword" value="" />
      </div>


			<!-- ここに表示 -->
			<div class="resblock">
				<ul id="res"></ul>
				<!-- このポイントで読み込み -->
				<div id=loadingmessage><img src="{$dirUrl}/loadimg.gif" /></div>
				<div class="paka3_trigger"></div>
			</div>
			<div class="strBlockBox">
		  	<div class="strBlock"></div>
			　</div>
			<div style="clear:left"></div>
			</form>

<div id="popup_button_area">

<input type="button" value="選択したツイートを挿入する" id="paka3_ei_btn_yes" class="button button-primary" /> 
				<input type="button" value="キャンセル" id="paka3_ei_btn_no"  class="button" />
			
</div>
EOS;
	}
 
	//##########################
	//ポップアップウィンドウのタブ
	//##########################
	function paka3_upload_tabs( $tabs )
	{
		$tabs = array();
		$tabs[ "paka3GetTweetTab" ] = "ツイッターからツイートを取得する" ;
		return $tabs;
	}

	//##################################
	//Ajaxコールバック関数
	//##################################
	public function my_action_callback(){
		//tweeter api
		
		if( isset($_POST['sword']) && check_admin_referer( get_bloginfo('url').'paka3GetTweet','security')){

			if ( !class_exists('TwitterOAuth') ) {
				require_once( "twitteroauth/twitteroauth.php" );
			}

			$this->word = $_POST['sword'] ? $_POST['sword'] : $this->word ;
			if ( !$this->word ) {
				$req = "";
			} else {
				$obj = new TwitterOAuth( $this->apiKey, $this->apiSecret, $this->accessToken, $this->accessTokenSecret );
				$array = array(	'q' => sprintf(esc_html("%s"),$this->word ), 
											'lang' => $this->lang, 
											'result_type' => 'recent',
											'count' => 100);
			
			
				//JSON(そのまま返すならこの値を返す)
				$req = $obj->OAuthRequest( 'https://api.twitter.com/1.1/search/tweets.json', 
																 'GET', 
																 $array );
			

				//**整形して配列→JSONにする。
				$tweets = json_decode( $req );
				if( isset( $tweets ) && empty( $tweets->errors ) ) {
					$req = $tweets->statuses;
					
					$view_obj = new Paka3_tweet_json_to_view;
					$req = $view_obj -> html_view( $req );
				}
			}
			//ここまで
			header( "Content-Type: application/json" );
			echo json_encode($req);
			exit;
		}else{
			die("エラー");
		}
	}



}