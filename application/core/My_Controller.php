<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 基于CI控制器核心类进行扩展的API控制器核心基类
 * @author lichao
 */
class Api_Controller extends CI_Controller
{
	public function __construct() 
	{
		parent::__construct();	// 调用父类的构造函数
		header('Access-Control-Allow-Headers:Authority');
		header("Access-Control-Allow-Origin:*");
		header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
/*		$headers	=	getallheaders();
		$Authority	=	$headers['Authority'];
		if($Authority=='12345')
		{

		}*/

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
	public function response($code, $msg = '', $error = array(), $data = array())
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
		exit();

	}

}


