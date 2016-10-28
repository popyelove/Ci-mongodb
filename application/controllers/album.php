<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Album extends Api_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'album_model',
				'common_model'
			)
		);
	}
	public function index()
	{
		$this->response(200,'ok','',000);
	}

	/**
	 * @{发现-专辑-更多}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	public function get_more_album()
	{

		$limit	=	trim($this->input->get_post('limit',true));
		$page	=	trim($this->input->get_post('page',true));
		$last_request_time	=	intval(trim($this->input->get_post('last_request_time',true)));
		if(empty($last_request_time))
		{
			$last_request_time	=	time();
		}
		if(empty($limit)||empty($page))
		{
			$this->response(400,'参数不完整');
		}
		$sort		=	trim($this->input->get_post('sort',true));
		$sum		=	$this->common_model->get_all_nums('picked_album_omnibus');//总数
		$data['total_count']	=	$sum;
		$data['last_request_time']	=	$last_request_time;
		if($sort==1)
		{
			//按照发布时间
			$get_more_album=$this->album_model->get_best_album($limit,$page,array('create_time'=>-1),$last_request_time);
			$data['list']=$get_more_album;
		}else
		{
			//默认按照热度排序
			$get_more_album=$this->album_model->get_best_album($limit,$page,array('sort_order'=>-1),$last_request_time);
			$data['list']=$get_more_album;
		}
		$this->response(200,'精选专辑','',$data);
	}

	/**
	 * @{专辑详情页-静态}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function album_detail()
	{

	}
	
}
