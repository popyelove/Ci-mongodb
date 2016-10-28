<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M extends Api_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(
			array
			(
				'album_model',
			)
		);
		$this->load->library('cimongo');
	}


	/**
	 * @{用户私信}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function index()
	{
		$select	=	array
		(
			'id',
			'sponsor_uid',
			'participant_uid',
			'type',
			'message',
			'create_time',
		);
		$where	=	array(
			'status'=>1
		);
		$mes	=	$this->cimongo->select($select)->where($where)->get('user_private_message')->result_array();
		foreach ($mes as $k=>$v)
		{
			$data['id']=new MongoInt64($v['id']);
			$data['from_user _id']=new MongoInt64($v['sponsor_uid']);
			$data['to_user_id']=new MongoInt64($v['participant_uid']);
			$data['msg_type']=($v['type']);
			$data['msg_content']=($v['message']);
			$data['send_time']=($v['create_time']);
			$data['is_read']=false;
			$data['read_time']=0;
			$this->cimongo->insert('aa',$data);
			unset($data['_id']);

		}

	}


	/**
	 * @{用户集合}
	 * @author lichao
	 * @${DATE}
	 * @modifier lichao
	 * @${DATE}
	 * @ return bool
     */
	function user()
	{
		$user	=	$this->cimongo->get('user')->result_array();
		foreach ($user as $uk=>$uv)
		{
			unset($uv['_id']);
			unset($uv['is_online']);
			unset($uv['recommend']);
			$uv['user_id']=new MongoInt64($uv['user_id']);
			if(empty($uv['user_tags']))
			{
				$uv['user_tags']=array();
			}else
			{
				$uv['user_tags']=explode(',',$uv['user_tags']);
			}

			//用户拥有的声音id列表
			$voice_ids	=	array();
			$voice_id=	$this->cimongo->select(array('_id','voice_id'))->where(array('user_id'=>$uv['user_id']))->get('voice')->result_array();
			if(!empty($voice_id))
			{
				foreach ($voice_id as $vv)
				{
					$id=new MongoInt64($vv['voice_id']);
					array_push($voice_ids,$id);
				}
			}

			$uv['voice_id_list']	=	$voice_ids;
			//用户点赞过得声音列表
			$voice_like	=	$this->cimongo->select(array('voice_id','_id','create_time'))->where(array('user_id'=>$uv['user_id']))->get('voice_like')->result_array();
			if(!empty($voice_like))
			{
				foreach ($voice_like as $vk=>$vv)
				{
					$voice_like[$vk]['voice_id']=new MongoInt64($vv['voice_id']);
				}
			}

			$uv['liked_voice_list']=$voice_like;
			//当前用户关注的电台用户
			$follow	=	$this->cimongo->select(array('_id','follow_user_id','follow_time'))->where(array('user_id'=>$uv['user_id']))->get('follow')->result_array();

			foreach ($follow as $fk=>$fv)
			{
				$level=$this->cimongo->select(array('_id','level'))->where(array('user_id'=>$fv['follow_user_id']))->get('user')->row_array();
				if($level['level']==1)
				{
					$follow[$fk]['user_id']=new MongoInt64($fv['follow_user_id']);
					$follow[$fk]['create_time']=$fv['follow_time'];
					unset($follow[$fk]['follow_user_id']);
					unset($follow[$fk]['follow_time']);
				}
			}
			$uv['followed_user_list']=$follow;
			//当前用户的粉丝列表
			if($uv['level']==1)
			{
				$follower	=	$this->cimongo->select(array('_id','user_id','follow_time'))->where(array('follow_user_id'=>$uv['user_id']))->get('follow')->result_array();
				if(!empty($follower))
				{
					foreach ($follower as $fk=>$fv)
					{
						$follower[$fk]['user_id']=new MongoInt64($fv['user_id']);
						$follower[$fk]['create_time']=$fv['follow_time'];
						unset($follower[$fk]['follow_time']);
					}
				}

			}
			$uv['follower_user_list']=$follower;
			//用户加入的群组
			$group	=	$this->cimongo->select(array('_id','group_id','level','join_time'))->where(array('user_id'=>$uv['user_id']))->where_ne('level',-100)->get('group_user')->result_array();
			if(!empty($group))
			{
				foreach ($group as $gk=>$gv)
				{

					if($gv['level']==0)
					{
						$group[$gk]['is_creator']=false;
					}elseif ($gv['level']==1)
					{
						$group[$gk]['is_creator']=true;
					}
					unset($group[$gk]['level']);
					$group[$gk]['create_time']=$gv['join_time'];
					unset($group[$gk]['join_time']);

				}
			}
			$uv['group_list']=$group;
			//
			$this->cimongo->insert('user_bak',$uv);
		}

	}

}
