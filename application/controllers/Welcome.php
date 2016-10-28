<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library('cimongo');
	}
	public function index()
	{
		$user	=	$this->cimongo->get('user')->result_array();
		foreach ($user as $uk=>$uv)
		{
			$this->cimongo->where(array('user_id'=>intval($uv['user_id'])))->set(array('update_time'=>time()))->update('user');
		}
	}
}
