<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voice extends Api_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'voice_model',
				'common_model'
			)
		);
	}
	public function index()
	{
		$this->response(200,'ok','',000);
	}

	/**
	 * @{发现-声音-更多}
	 * @author lichao
	 * @${2016/06/07}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	public function get_more_voice()
	{
		$limit		=	trim($this->input->get_post('limit',true));
		$page		=	trim($this->input->get_post('page',true));
		$sort		=	trim($this->input->get_post('sort',true));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page))
		{
			$this->response(400,'参数不完整');
		}
		$sum		=	$this->common_model->get_all_nums('picked_hot_voice');//总数
		$data['total_count']		=	$sum;
		$data['last_request_time']	=	$last_request_time;
		if($sort==1)
		{
			$get_more_voice=$this->voice_model->get_hot_voice($limit,$page,array('create_time'=>-1),$last_request_time);
			$data['voice_list']	=	$get_more_voice;
		}else
		{
			//默认按热度
			$get_more_voice=$this->voice_model->get_hot_voice($limit,$page,array('sort_order'=>-1),$last_request_time);
			$data['list']	=	$get_more_voice;
		}
		$this->response(200,'热门声音更多','',$data);
	}

	/**
	 * @{首页专栏-换一批接口}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function exchange_some()
	{
/*		type类型
		1-今日头条
		2-心灵驿站
		3-音乐前线
		4-脱口秀场
		5-百味书声
		6-说学逗唱*/
		$limit		=	trim($this->input->get_post('limit',true));
		$page		=	trim($this->input->get_post('page',true));
		$type		=	trim($this->input->get_post('type',true));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page)||empty($type))
		{
			$this->response(400,'参数不完整');
		}
		$sum			=	30;//总数
		$data['total_count']	=	$sum;
		$data['last_request_time']=$last_request_time;
		$exchange_some		=	$this->voice_model->get_section_data($type,$limit,$page,$last_request_time);
		$data['list']	=	$exchange_some;
		$this->response(200,'换一批数据','',$data);

	}
	
}
