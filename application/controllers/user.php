<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Api_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'user_model',
				'album_model',
				'voice_model',
				'common_model',
			)
		);
	}

	/**
	 * @{发现里的电台-优秀电台}
	 * @author lichao
	 * @${2016/06/08}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function  good_radio()
	{
		$limit	=	trim($this->input->get_post('limit',true));
		$page	=	trim($this->input->get_post('page',true));
		$page	=	$page?$page:1;//默认为1
		$type	=	trim($this->input->get_post('type',true));//1-上升最快2-最热电台
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($type))
		{
			$this->response(400,'参数不完整');
		}
		$sum		=	$this->common_model->get_all_nums('pick_good_radio');//总数
		$data['total_count']	=	$sum;
		$data['last_request_time']	=$last_request_time;
		$good_radio	=	$this->user_model->get_good_radio($limit,$page,$type,$last_request_time);
		$data['list']	=	$good_radio;
		$this->response(200,'优秀电台','',$data);
	}

	/**
	 * @{个人信息}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function personal_info()
	{
		$user_id	=	intval(trim($this->input->get_post('user_id',true)));//用户id
		if(empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$user_info	=	$this->user_model->get_personal_info($user_id);
		$data['user_info']	=	$user_info;
		$this->response(200,'用户个人信息','',$data);

	}

	/**
	 * @{好友列表}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function friend_list()
	{
		$this->load->library('cimongo');
		$limit	=	trim($this->input->get_post('limit',true));
		$page	=	trim($this->input->get_post('page',true));
		$page	=	$page?$page:1;
		$user_id	=	intval(trim($this->input->get_post('user_id',true)));//用户id
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($user_id)||empty($limit)||empty($page))
		{
			$this->response(400,'参数不完整');
		}
		$friend_list	=	$this->user_model->get_friend_list($limit,$page,$user_id,$last_request_time);
		$sum			=	$this->cimongo->select(array('friend_id_array','_id'))->where(array('user_id'=>intval($user_id)))->get('user')->row_array();
		if(isset($sum['friend_id_array'])&&!empty($sum['friend_id_array']))
		{
			$sum			=	count($sum['friend_id_array']);
		}else
		{
			$sum			=	0;
		}
		$data['total_count']	=	$sum;
		$data['last_request_time']	=	$last_request_time;
		$data['list']	=	$friend_list;
		$this->response(200,'好友列表','',$data);
	}

	/**
	 * @{他们也在一说}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function in_yishuo()
	{
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$sum		=	count($this->user_model->thay_in_yishuo(0,1,$user_id,$last_request_time));//总数
		$data['total_count']	=	$sum;
		$data['last_request_time']	=	$last_request_time;
		$users	=	$this->user_model->thay_in_yishuo($limit,$page,$user_id,$last_request_time);
		$data['list']	=	$users;

		$this->response(200,'他们也在一说','',$data);

	}

	/**
	 * @{我的声音列表}
	 * @author lichao
	 * @${2016/06/012}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function my_voice_list()
	{



	}

	/**
	 * @{我的专辑列表}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function my_album_list()
	{
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$sum		=	count($this->album_model->get_album_list($user_id,0,1,$last_request_time));//总数
		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;
		$album_list	=	$this->album_model->get_album_list($user_id,$limit,$page,$last_request_time);
		$data['list']	=	$album_list;
		$this->response(200,'专辑列表','',$data);
	}

	/**
	 * @{电台用户获取粉丝列表}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function my_fans()
	{
		$this->load->library('cimongo');
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$follower_user_list=$this->cimongo->select(array('follower_user_list','_id'))->where(array('user_id'=>$user_id,'level'=>array('$in'=>array(1,2))))->get('user')->row_array();
		if(isset($follower_user_list['follower_user_list'])&&!empty($follower_user_list['follower_user_list']))
		{
			$sum		=	count($follower_user_list['follower_user_list']);
		}else
		{
			$sum		=	0;
		}

		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;

		$fans	=	$this->user_model->get_fans_list($user_id,$limit,$page,$last_request_time);
		$data['list']=$fans;
		$this->response(200,'粉丝列表','',$data);

	}

	/**
	 * @{我关注的电台列表}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
	 */
	function my_radio()
	{
		$this->load->library('cimongo');
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$radio_list=$this->cimongo->select(array('follower_user_list','_id'))->where(array('user_id'=>$user_id))->get('user')->row_array();
		if(isset($radio_list['follower_user_list'])&&!empty($radio_list['follower_user_list']))
		{
			$sum		=	count($radio_list['follower_user_list']);
		}else
		{
			$sum		=0;
		}
		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;
		$radio	=	$this->user_model->get_radio_list($user_id,$limit,$page,$last_request_time);
		$data['list']=$radio;
		$this->response(200,'粉丝列表','',$data);
	}

	/**
	 * @{我的订阅}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function my_subscribe()
	{
		$this->load->library('cimongo');
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$subscribe_album_array=$this->cimongo->select(array('subscribe_album_array','_id'))->where(array('user_id'=>$user_id))->get('user')->row_array();
		if(isset($subscribe_album_array['subscribe_album_array'])&&!empty($subscribe_album_array['subscribe_album_array']))
		{
			$sum		=	count($subscribe_album_array['subscribe_album_array']);
		}else
		{
			$sum		=0;
		}
		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;
		$subscribe	=	$this->user_model->get_subscribe_list($user_id,$limit,$page,$last_request_time);
		$data['list']=$subscribe;
		$this->response(200,'订阅列表','',$data);
	}

	function my_voice()
	{
		$limit	=	intval(trim($this->input->get_post('limit',true)));
		$page	=	intval(trim($this->input->get_post('page',true)));
		$page	=	$page?$page:1;
		$user_id=	intval(trim($this->input->get_post('user_id',true)));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($user_id))
		{
			$this->response(400,'参数不完整');
		}
		$sum		=	count($this->voice_model->get_voice_list($user_id,0,1,$last_request_time));//总数
		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;

	}










}
