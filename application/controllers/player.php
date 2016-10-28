<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player extends Api_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'voice_model',
			)
		);
	}
	function index()
	{
		$voice_ids	=	$this->input->get_post('voice_id',true);
		if(empty($voice_ids))
		{
			$this->response(400,'参数不完整');
		}
		if(is_array($voice_ids))
		{
			$voice_list	=	$this->voice_model->get_player_voice($voice_ids);
			$data['list']	=	$voice_list;
			$this->response(200,'声音列表','',$data);
		}
	}
	
}
