<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 公共辅助函数
 * @author SC
 */

/**
 * HTTP请求函数
 * @param string $url
 * @param array|string $post
 * @param integer $connect_timeout
 * @param integer $read_timeout
 * @return mixed|boolean
 * @author SC
 */
function http_request($url, $post=array(), $connect_timeout = 15, $read_timeout = 300) 
{
	if (function_exists('curl_init')) {
		$timeout = $connect_timeout + $read_timeout;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	return false;
}



/**
 * curl请求函数
 * @param string $url
 * @param array|string $post
 * @param integer $connect_timeout
 * @param integer $read_timeout
 * @return mixed|boolean
 * @author SC
 */
function curl_request($url, $post=array(), $connect_timeout = 1, $read_timeout = 3) 
{
	if (function_exists('curl_init')) {
		$timeout = $connect_timeout + $read_timeout;		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);		
		return $result;
	}	
	return false;
}
/** 2013/11/1 
 * 获取用户基本信息
 * @author bruce
 * @param	int	 $uid 用户ID
 * 
 */
function user_info($uid='')
{
	if(empty($uid)){
		return FALSE;
	}
	$CI =& get_instance();
	$CI->load->library('cimongo');
	$CI->cimongo->where(array('user_id'=>intval($uid)));
	return $CI->cimongo->get('user')->row_array();
}

/** 
 * 获取圈子基本信息
 * @author bruce
 * 2014/07/07
 */
function group_info($group_id='')
{
	if(empty($group_id)){
		return FALSE;
	}
	$CI =& get_instance();
	$CI->db->where(array('group_id'=>$group_id));
	return $CI->db->get('group')->row_array();
}

/**
 * 随机码
 * @author bruce
 */
function random_code() 
{
	$str='0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
	$arr=explode(',',$str);
	$keys = array_rand($arr, 6);
	return $arr[$keys[0]].$arr[$keys[1]].$arr[$keys[2]].$arr[$keys[3]].$arr[$keys[4]].$arr[$keys[5]];
}

//手机密钥生成器
function getcheckpwd($time,$user) 
{
	/*
	* $time当前时间戳
	* $user用户名/手机号
 	* */
	$year=date('Y',$time);
	$month=date('m',$time);
	$day=date('d',$time);
	$hour=date('H',$time);
	$minute=date('i',$time);
	$minute1=$minute%10;//个位;[0-9]
	$minute2=floor($minute/10);//十位;[0-9]
	//生成时间键
	$timekey=md5($year+$month+$day+$hour+$minute);
	$timekey='0x'.substr($timekey,$minute1,5);
	//生成身份键
	$userkey=md5($user);
	$userkey='0x'.substr($userkey,$minute2,5);
	//生成口令
	$pwd=substr(($timekey+$userkey),0,4);
	return $pwd;
}

/**
 * 计算两个时间戳的时间差
 * Enter description here ...
 * @param  $begin_time 开始时间
 * @param $end_time 结束时间
 */
function timediff($begin_time,$end_time) 
{ 
	if($begin_time < $end_time){ 
		$starttime = $begin_time; 
		$endtime = $end_time; 
	} 
	else{ 
		$starttime = $end_time; 
		$endtime = $begin_time; 
	} 
	$timediff = $endtime-$starttime; 
	$days = intval($timediff/86400); 
	$remain = $timediff%86400; 
	$hours = intval($remain/3600); 
	$remain = $remain%3600; 
	$mins = intval($remain/60); 
	$secs = $remain%60; 
	$res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs); 
	return $res; 
}

/**
 * 根据算法计算发布或转发的显示时间
 * @param  $begin_time 开始时间
 * @param $end_time 结束时间
 */
function calc_rf_time($rf_time = 0)
{ 
	$now = time();
	$timediff = $now - $rf_time;
	$ret = '';
	if($timediff >= 7*24*3600){//发布或转发时间距当前时间大于等于7天
		$ret = date('Y-m-d',$rf_time);
	}else if(24*3600<=$timediff && $timediff<7*24*3600){//发布或转发时间距当前时间大于等于1天小于7天
		$ret = intval(floor($timediff/(3600*24))).'天前';
	}else if(3600<=$timediff && $timediff<24*3600){//发布或转发时间距当前时间大于等于1小时小于1天
		$ret = intval(floor($timediff/3600)).'小时前';
	}else if(60<=$timediff && $timediff<3600){//发布或转发时间距当前时间大于等于1分钟小于1小时
		$ret = intval(floor($timediff/60)).'分钟前';
	}else if(2<=$timediff && $timediff<60){//发布或转发时间距当前时间大于等于2秒小于1分钟
		$ret = $timediff.'秒前';
	}else{
		$ret = '刚刚';
	}
	return $ret;
}

/**
 * 根据算法计算声音时长
 * @param  $begin_time 开始时间
 * @param $end_time 结束时间
 */
function calc_voice_play_time($play_time = 0)
{ 
     $play_time = round($play_time/60);  
     if ($play_time >= 60){  
      $hour = floor($play_time/60);  
      $min = $play_time%60;  
      $res = $hour.' 小时 ';  
      $min != 0  &&  $res .= $min.' 分';  
     }else{  
      $res = $play_time.' 分钟';  
     }  
     return $res;  
}

function sec2time($sec)
{
	$res = '';
	$min = round($sec/60);
	if ($min >= 60){
		$hour = $min/60;
		$min = $min%60;
		$sec = $sec%60;
		$res = $hour.' 小时 '.$min.' 分'.$sec.' 秒';
	}else{
		$min = $min%60;
		$sec = $sec%60;
		$res = $min.' 分'.$sec.' 秒';
	}
	return $res;
}

/**
 * 创建头像
 * @param  $avatar 数据库头像字段，字符串
 */
function create_avatar_url($avatar = '')
{
	if($avatar !== ''){
		if(substr($avatar,0,7) !== 'http://' && substr($avatar,0,8) !== 'https://'){
			$avatar = HOST_URL.$avatar;
		} 
	}
	return $avatar;
}

/**
 * 创建绝对路径
 * @bruce
 * @2015/05/15
 */
function create_abs_url($url = '')
{
	if($url !== ''){
		if(substr($url,0,7) !== 'http://' && substr($url,0,8) !== 'https://'){
			$url = HOST_URL.$url;
		} 
	}
	return $url;
}

/*
 * 截取字符串
 */
// function substr_cut($str_cut,$length)
// {
//     if (strlen($str_cut) > $length)
//     {
//         for($i=0; $i < $length; $i++)
//         if (ord($str_cut[$i]) > 128)    $i++;
//         $str_cut = substr($str_cut,0,$i)."..";
//     }
//     return $str_cut;
// }

/*
 * 截取字符串
 * @param $sourcestr 源字符串
 * @param $cutlength 截取长度
 */
function cut_str($sourcestr,$cutlength)
{
	$returnstr='';
	$i=0;
	$n=0;
	$str_length=strlen($sourcestr);//字符串的字节数
	while (($n<$cutlength) and ($i<=$str_length))
	{
		$temp_str=substr($sourcestr,$i,1);
		$ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
		if ($ascnum>=224) //如果ASCII位高与224，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
			$i=$i+3; //实际Byte计为3
			$n++; //字串长度计1
		}
		elseif ($ascnum>=192) //如果ASCII位高与192，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
			$i=$i+2; //实际Byte计为2
			$n++; //字串长度计1
		}
		elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1; //实际的Byte数仍计1个
			$n++; //但考虑整体美观，大写字母计成一个高位字符
		}
		else //其他情况下，包括小写字母和半角标点符号，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1; //实际的Byte数计1个
			$n=$n+0.5; //小写字母和半角标点等与半个高位字符宽...
		}
	}
	if ($str_length>$cutlength){
		$returnstr = $returnstr."...";//超过长度时在尾处加上省略号
	}
	return $returnstr;
}

function truncate_utf8_string($string, $length, $etc = '......')
{
	$result = '';
	$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
	$strlen = strlen($string);
	for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
	{
	if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
	{
	if ($length < 1.0)
	{
	break;
	}
		$result .= substr($string, $i, $number);
		$length -= 1.0;
		$i += $number - 1;
	}
	else
		{
		$result .= substr($string, $i, 1);
		$length -= 0.5;
		}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		if ($i < $strlen)
		{
		$result .= $etc;
}
return $result;
}

/**
 * get_contents
 * 服务器通过get请求获得内容
 * @param string $url       请求的url,拼接后的
 * @return string           请求返回的内容
 */
function get_contents($url)
{
	if (ini_get("allow_url_fopen") == "1") {
		$response = file_get_contents($url);
	}else{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$response =  curl_exec($ch);
		curl_close($ch);
	}

	return $response;
}

/**
 * file_type
 * 通过获取文件头判断文件的类型
 * @param string $url       请求的url,拼接后的
 * @return string           请求返回的内容
 */
function file_type($filename)
{
	$file = fopen($filename, "rb");
	$bin = fread($file, 2); //只读2字节
	fclose($file);
	$strInfo = @unpack("C2chars", $bin);
	$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
	$fileType = '';
	switch ($typeCode)
	{
		case 7368:
			$fileType = 'mp3';
			break;
		case 7790:
			$fileType = 'exe';
			break;
		case 7784:
			$fileType = 'midi';
			break;
		case 8297:
			$fileType = 'rar';
			break;
		case 8075:
			$fileType = 'zip';
			break;
		case 255216:
			$fileType = 'jpg';
			break;
		case 7173:
			$fileType = 'gif';
			break;
		case 6677:
			$fileType = 'bmp';
			break;
		case 13780:
			$fileType = 'png';
			break;
		default:
			$fileType = 'unknown: '.$typeCode;
	}

	//Fix
	if ($strInfo['chars1']=='-1' AND $strInfo['chars2']=='-40' ) return 'jpg';
	if ($strInfo['chars1']=='-119' AND $strInfo['chars2']=='80' ) return 'png';

	return $fileType;
}

/*
 * 根据参数$key，获取系统设置信息，主要是app版本相关的信息
 */
function system_settings($key)
{
	$CI =& get_instance();
	$sql_get_version = "SELECT value FROM system_settings WHERE name = ?";
	$res = $CI->db->query($sql_get_version, array($key))->row_array();
	return $res['value'];
}

/**
 * 生成uuid
 * Set to true/false as your default way to do this.
 */
function guuid( $opt = true )
{
	if( function_exists('com_create_guid') ){
		if( $opt ){ 
			return com_create_guid(); 
		}else { 
			return trim( com_create_guid(), '{}' ); 
		}
	}
	else {
		mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
		$charid = strtoupper( md5(uniqid(rand(), true)) );
		$hyphen = chr( 45 );    // "-"
		$left_curly = $opt ? chr(123) : "";     //  "{"
		$right_curly = $opt ? chr(125) : "";    //  "}"
		$uuid = $left_curly
		. substr( $charid, 0, 8 ) . $hyphen
		. substr( $charid, 8, 4 ) . $hyphen
		. substr( $charid, 12, 4 ) . $hyphen
		. substr( $charid, 16, 4 ) . $hyphen
		. substr( $charid, 20, 12 )
		. $right_curly;
		return $uuid;
	}
}

/**
 * 第三方分享生成分享数据
 * @author bruce
 * @2015/09/02
 */
function create_share_data($text='',$title='',$pic='',$url='',$des='一说好声音')
{
	$text = '我觉得'.$text.'挺不错的，你觉得呢？（分享来自@一说)';
	return json_encode(array('text'=>$text,'title'=>$title,'pic'=>$pic,'url'=>$url,'bdDes'=>$des));
}

/**
 * 发送邮件验证码（用于注册及找回密码）
 * @author bruce
 * @2015/09/16
 */
function send_email_verification_code($type = 0,$email = '',$verification_code = '')
{
	//初始化全局对象
	$CI =& get_instance();
	//加载email的类库及配置
	$CI->load->library('email');
	$CI->load->config('email');
	$data = array('username' => $email, 'code' => $verification_code);
	switch($type){
		case 0:
			$subject = '注册验证码';
			$content = $CI->load->view('email/REGISTERVERICODE-txt', $data, TRUE);
			break;
		case 1:
			$subject = '找回密码验证码';
			$content = $CI->load->view('email/FORGETPWD-txt', $data, TRUE);
			break;
		case 2:
			$subject = '绑定邮箱验证码';
			$content = $CI->load->view('email/BINDEMAIL-txt', $data, TRUE);
			break;
		case 3:
			$subject = '解绑邮箱验证码';
			$content = $CI->load->view('email/UNBINDEMAIL-txt', $data, TRUE);
			break;
	}
	//发送邮件
	log_message('error',$CI->config->item('smtp_user'));	
	$CI->email->from($CI->config->item('smtp_user'), '一说');
	$CI->email->to($email);
	$CI->email->subject($subject);
	$CI->email->message($content);
	if ( ! $CI->email->send())//验证码发送失败
	{
		return false;
	}
	return true;
}


/**
 * 发送手机验证码（用于注册及找回密码）
 * @author bruce
 * @2015/09/16
 */
function send_moblie_verification_code($type = 0,$mobile = '',$verification_code = '')
{
	$post_data = array();

// 	$post_data['account'] = iconv('GB2312', 'GB2312',"shangwuE_jh");
// 	$post_data['pswd'] = iconv('GB2312', 'GB2312',"Tch123456");
	$post_data['account'] = iconv('GB2312', 'GB2312',"vip-jjd");
	$post_data['pswd'] = iconv('GB2312', 'GB2312',"Tch123456");
	$post_data['mobile'] =$mobile;
	switch($type){
		case 0:
			$msg = '您好！您的注册验证码是：';
			break;
		case 1:
			$msg = '您好！您的找回密码验证码是：';
			break;
		case 2:
			$msg = '您好！您的绑定手机验证码是：';
			break;
		case 3:
			$msg = '您好！您的解绑手机验证码是：';
			break;
	}
	$msg .= $verification_code;
	$post_data['msg']=mb_convert_encoding("$msg",'UTF-8', 'auto');
	$url='http://222.73.117.158/msg/HttpBatchSendSM?';
// 	$url='http://222.73.117.158/msg/index.jsp';
	$o="";
	foreach ($post_data as $k=>$v)
	{
		$o.= "$k=".urlencode($v)."&";
	}
	$post_data=substr($o,0,-1);
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上 1:保存为字符串，0是输出到屏幕
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	$results_arr = explode(',',$result);
	
	$code = $results_arr[1];
	$code = substr($code,0,1);
	$send_suc = $code == 0 ? true : false;
	if($send_suc){
		return true;
	}
	return true;
}


/**
 * 判断请求来源
 * @author bruce
 * @2015/09/17
 */
function check_request_source()
{
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$source = 'web';
	$is_wechat = false;
	if(strpos($agent, 'iphone')){
		$source = 'iphone';
	}
	if(strpos($agent, 'android')){
		$source = 'android';
	}
	if(strpos($agent, 'micromessenger')){
		$is_wechat = true;
	}
	return array('source'=>$source,'is_wechat'=>$is_wechat);
}

/**
 * 生成uuid
 * @author bruce
 * @2015/09/18
 */
function guid( $opt = false )
{
	if( function_exists('com_create_guid') ){
		if( $opt ){ return com_create_guid(); }
		else { return trim( com_create_guid(), '{}' ); }
	}
	else {
		mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
		$charid = strtoupper( md5(uniqid(rand(), true)) );
		$hyphen = chr( 45 );    // "-"
		$left_curly = $opt ? chr(123) : "";     //  "{"
		$right_curly = $opt ? chr(125) : "";    //  "}"
		$uuid = $left_curly
		. substr( $charid, 0, 8 ) . $hyphen
		. substr( $charid, 8, 4 ) . $hyphen
		. substr( $charid, 12, 4 ) . $hyphen
		. substr( $charid, 16, 4 ) . $hyphen
		. substr( $charid, 20, 12 )
		. $right_curly;
		if(!$opt){
			$uuid = trim( $uuid, '{}' );	
		}
		return $uuid;
	}
}

/**
 * 输入的字符串中特殊字符转义
 * 
 */
function check_input($value)
{
    // 去除斜杠
    if (get_magic_quotes_gpc())
    {
        $value = stripslashes($value);
    }
    // 如果不是数字则加引号
    if (!is_numeric($value))
    {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return $value;
}

/**
 * BMP 创建函数
 * @author zmx
 * @param string $filename path of bmp file
 * @example who use,who knows
 * @return resource of GD
 */
function imagecreatefrombmp( $filename ){
    if ( !$f1 = fopen( $filename, "rb" ) )
        return FALSE;
     
    $FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
    if ( $FILE['file_type'] != 19778 )
        return FALSE;
     
    $BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
    $BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
    if ( $BMP['size_bitmap'] == 0 )
        $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
    $BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
    $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
    $BMP['decal'] = 4 - (4 * $BMP['decal']);
    if ( $BMP['decal'] == 4 )
        $BMP['decal'] = 0;
     
    $PALETTE = array();
    if ( $BMP['colors'] < 16777216 ){
        $PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
    }
     
    $IMG = fread( $f1, $BMP['size_bitmap'] );
    $VIDE = chr( 0 );
     
    $res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
    $P = 0;
    $Y = $BMP['height'] - 1;
    while( $Y >= 0 ){
        $X = 0;
        while( $X < $BMP['width'] ){
            if ( $BMP['bits_per_pixel'] == 32 ){
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
                $B = ord(substr($IMG, $P,1));
                $G = ord(substr($IMG, $P+1,1));
                $R = ord(substr($IMG, $P+2,1));
                $color = imagecolorexact( $res, $R, $G, $B );
                if ( $color == -1 )
                    $color = imagecolorallocate( $res, $R, $G, $B );
                $COLOR[0] = $R*256*256+$G*256+$B;
                $COLOR[1] = $color;
            }elseif ( $BMP['bits_per_pixel'] == 24 )
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
            elseif ( $BMP['bits_per_pixel'] == 16 ){
                $COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 8 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 4 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 2) % 2 == 0 )
                    $COLOR[1] = ($COLOR[1] >> 4);
                else
                    $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 1 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 8) % 8 == 0 )
                    $COLOR[1] = $COLOR[1] >> 7;
                elseif ( ($P * 8) % 8 == 1 )
                    $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                elseif ( ($P * 8) % 8 == 2 )
                    $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                elseif ( ($P * 8) % 8 == 3 )
                    $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                elseif ( ($P * 8) % 8 == 4 )
                    $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                elseif ( ($P * 8) % 8 == 5 )
                    $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                elseif ( ($P * 8) % 8 == 6 )
                    $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                elseif ( ($P * 8) % 8 == 7 )
                    $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }else
                return FALSE;
            imagesetpixel( $res, $X, $Y, $COLOR[1] );
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P += $BMP['decal'];
    }
    fclose( $f1 );
     
    return $res;
}

/**
 *  调用java后台提供的api接口，同步内存中数据
 */
function synchronize_java_memory_data($api_url = '',$sync_data = array())
{
    $final_api_url = JAVA_API_FOR_PHP_COMMON_URL.$api_url;
    return curl_request($final_api_url,$sync_data);
}

/**
 *monogdb产生唯一id
 *@param $name-即原mysql中表名
 */
function make_auto_increment_id($name)
{
    $CI =& get_instance();
    $mongo_host =   $CI->config->item('host');
    $conn       =   new MongoClient ($mongo_host);
    $db         =   $conn->yishuo;
    $update     =   array('$inc'=>array("id"=>1));
    $query      =   array('name'=>$name);
    $command    =   array(
        'findandmodify' =>  'auto_increment_ids',
        'update'        =>  $update,
        'query'         =>  $query,
        'new'           =>  true,
        'upsert'        =>  true
    );
    $id = $db->command($command);
    $conn->close();
    return $id['value']['id'];
}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if (!$strict) {
		if (ini_get('html_errors')) {
			$output = print_r($var, true);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		} else {
			$output = $label . print_r($var, true);
		}
	} else {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
	}
	if ($echo) {
		echo($output);
		return null;
	}else
		return $output;
}
/**
 * 函数用途描述  方便打印数据
 * @date: 2015年7月14日
 * @author: Administrator
 * @param: $GLOBALS
 * @return:
 */
function diedump($a)
{
	dump($a);
	exit();
}

/**
 * @{数组根据某个字段排序}
 * @author lichao
 * @${2016/05/16}
 * @modifier lichao
 * @${DATE}
 * @ return bool
 */
function array_sort_by_field($field='',$sort='SORT_ASC',$arr=array())
{
	$arrSort = array();
	foreach($arr AS $uniqid => $row)
	{
		foreach($row AS $key=>$value)
		{
			$arrSort[$key][$uniqid] = $value;
		}
	}
	if($sort)
	{
		array_multisort($arrSort[$field], constant($sort),$arr);
	}
	return $arr;
}
/**
 * @{api 响应函数}
 * @author lichao
 * @${DATE}
 * @modifier lichao
 * @${DATE}
 * @param $code
 * @param string $msg
 * @param array $error
 * @param array $data
 * @ return bool
 */
function response($code, $msg = '', $error = array(), $data = array())
{
	$response_data = array('code' => $code);
	if (!empty($error))
	{
		$response_data['error'] = array(
			'code' => (isset($error[0])) ? $error[0] : '',
			'msg' => (isset($error[1])) ? $error[1] : '',
		);
	} else
	{
		$response_data['msg'] = $msg;
	}
	$response_data['data'] = (!empty($data)) ? $data : new stdClass();
	$response_str = json_encode($response_data);
	echo $response_str;

}