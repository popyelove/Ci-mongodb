<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends Api_Controller {
	private $banner_num=4;//首页banner数
	private $hot_topic_num=10;//热门话题数
	private $best_album_num=8;//精选专辑数
	private $hot_voice_num=4;//热门声音数
	private $section_num=6;//专题类数
	private $hot_anchor_num=10;//热门主播数
	private $new_anchor_num=10;//新进主播数
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'banner_model',
				'topic_model',
				'album_model',
				'voice_model',
				'user_model',
			)
		);
	}
	public function index()
	{
		
		//首页banner
		$banner_list	=	$this->banner_model->get_banner_list($this->banner_num);
		if(!empty($banner_list))
		{
			$dada['banner']	=	$banner_list;
		}
		//热门话题
		$hot_topic = $this->topic_model->get_hot_topic($this->hot_topic_num);
		if(!empty($hot_topic))
		{
			$dada['hot_topic']=$hot_topic;
		}
		//精选专辑
		$best_album	=	$this->album_model->get_best_album($this->best_album_num);
		if(!empty($best_album))
		{
			$dada['best_album']=$best_album;
		}
		//热门声音
		$hot_voice	=	$this->voice_model->get_hot_voice($this->hot_voice_num);
		if(!empty($hot_voice))
		{
			$dada['hot_voice']=$hot_voice;
		}
		//今日头条
		$dada['today_news']	=	$this->voice_model->get_section_data(1,$this->section_num);
		//心灵驿站
		$dada['mind_post']	=	$this->voice_model->get_section_data(2,$this->section_num);
		//音乐前线=
		$dada['music_line']	=	$this->voice_model->get_section_data(3,$this->section_num);
		//脱口秀场
		$dada['talk_show']	=	$this->voice_model->get_section_data(4,$this->section_num);
		//百味书声
		$dada['book_sound']	=	$this->voice_model->get_section_data(5,$this->section_num);
		//说学逗唱
		$dada['sxdc']		=	$this->voice_model->get_section_data(6,$this->section_num);
		//热门主播数
		$dada['hot_anchor']	=	$this->user_model->get_hot_anchor($this->hot_anchor_num);
		//新进主播
		$dada['new_anchor']	=	$this->user_model->get_new_anchor($this->new_anchor_num);
		$this->response(200,'首页数据','',$dada);

	}

}
