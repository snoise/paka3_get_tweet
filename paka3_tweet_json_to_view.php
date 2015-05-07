<?php
//#################################################
//#################################################
class Paka3_tweet_json_to_view{
	//######################
	//コンストラクタ
	//######################
	function __construct(){
		//時差を求める
		$this->t_zone = floor(( current_time( 'timestamp' ) - time( ) ) / 3600);

	}

	//##########################
	//表示用の関数
	//##########################
	function html_view( $tweets , $imgMode = 0) {

		
		$arrayData = array();
		foreach ( $tweets as $key => $val ) {
			$tweet = array(); 

			//1.ユーザ名
			$tweet['user_name'] = $val->user->name;
			//2.ユーザアカウント
			$tweet['user_account']= '@'.$val->user->screen_name;
			//3.ユーザURL
			$tweet['user_url']= 'https://twitter.com/'.$val->user->screen_name;
			//4.画像
			if( $val->entities->media ) {
				$tweet['img']="";
				foreach( $val->entities->media as $imgObj ) {
					
					$tweet['img'] .= $imgObj->media_url ? "<img class='img' src='".$imgObj->media_url."' />" : "";
					$tweet['imgURL'] .= $imgObj->media_url ;
				}
			}
			//5.日付
			$tweet['date'] = date( 'Y年m月d日 H:i', 
							strtotime( $this->t_zone.'hour', strtotime( $val->created_at ) ) );

			//6.ツイート
			$text = $val->text;

			$text = mb_ereg_replace('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', '<a href="\1" target="_blank">\1</a>', $text);
			//絵文字→*
			//reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
			$text = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
			 '|[\x00-\x7F][\x80-\xBF]+'.
			 '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
			 '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
			 '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
			 '*', $text ); 
			//reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
			$tweet['tweet'] = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
			 '|\xED[\xA0-\xBF][\x80-\xBF]/S','*', $text );


			//7.RT
			$tweet['rt'] = $val->retweet_count;

			$tweet['tweet_id']= $val->id_str;

			//8.リンク
			$tweet['link'] = $tweet['user_url']."/statuses/".$tweet['tweet_id'];
			
			//9.プロフィール画像
			$tweet['profile_img'] = "<img class='twt_p_img' src=".$val->user->profile_image_url." />";

			//array_push($arrayData,$tweet);
			array_unshift($arrayData,$tweet);
		}
			
		 return $arrayData;
	}

	function post_css() {
				echo <<< EOS
				<style  type="text/css">
					ul.paka3Tweet,
					ul.paka3Tweet li,
					ul.twt,
					ul.twt li{
						margin:0 !important;
						padding:0 !important;
						list-style-type : none;
						background:#fff;
					}
					ul.twt{
						border:1px solid;
						padding:5pt !important;
						border-color:#EEEEEE #DDDDDD #BBBBBB;
						border-radius:5px;
						box-shadow:rgba(0, 0, 0, 0.14902) 0 1px 3px;
					}
					ul.twt {
						margin-bottom:10px !important;
					}
					ul.twt img.img{
						border-radius:5px;
						margin-bottom:5pt;
					}
				  ul.twt li.profile{
						margin-bottom:15pt !important;
					}
						ul.twt li.profile img{
							display:block;
							border-radius:5px;
							margin-right:10pt;
							float:left;
						}
					ul.twt li.profile b,
					ul.twt a.date{
						color:#999;display:block;
						font-size:10pt;
					}
						ul.twt a.date{
							margin:10pt 0;
						}
					  ul.twt li.tweet{
						clear:left;
						line-height:180%;
					}

					 ul.twt strong.cl{
					 	font-size:6pt;color:#999;
					 	font-weight:200;display:block;
					 }
					 ul.twt a.imglink{
					 		display:block;
					 }

				</style>
EOS;
		}
}