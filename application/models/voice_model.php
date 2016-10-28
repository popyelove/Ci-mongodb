<?php
/**
 * voice相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class voice_model extends CI_Model
{
    /**
     * voice_model constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('cimongo');
    }

    /**
     * @{获取热门声音}
     * @author lichao
     * @${2016-06-06}
     * @modifier lichao
     * @${DATE}
     * @param int $limit
     * @param int $page
     * @return mixed
     * @ return bool
     */
    function get_hot_voice($limit=4, $page=1,$sort=array('sort_order'=>-1),$last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip=($page-1)*$limit;
        $this->cimongo->order_by($sort);
        $this->cimongo->select(array('voice_id','_id'));
        $this->cimongo->where(array('create_time'=>array('$lte'=>$last_request_time)));
        $voice_id   =   $this->cimongo->skip($skip)->limit($limit)->get('picked_hot_voice')->result_array();
        $voice      =   array();
        foreach ($voice_id as $vik=>$viv)
        {
            $where      =   array
            (
                'voice_id'=>intval($viv['voice_id'])
            );
            $voice_info =   $this->cimongo->where($where)->get('voice')->row_array();
            if(!empty($voice_info))
            {
                $voice[$vik]    =   $voice_info;
            }
        }
        return $voice;
    }

    /**
     * @{获取今日头条=1,心灵驿站=2,音乐前线=3,脱口秀场=4,百味书声=5,说学逗唱=6}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $type
     * @param $limit
     * @param $page
     * @ return bool
     */
    function get_section_data($type='',$limit=6,$page=1,$last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        if(!empty($type))
        {

            $skip   =   ($page-1)*$limit;
            $this->cimongo->where(array('create_time'=>array('$lte'=>$last_request_time)));
            $rel_id	=	$this->cimongo->skip($skip)->limit($limit)->select(array('rel_id'))->where(array('section_type'=>intval($type)))->get('section')->result_array();
            $return_into	=	array();
            foreach ($rel_id as $relk=>$relv)
            {
                if($type==5||$type==6)
                {
                    $table  =   'voice_album';
                    $where  =   array('album_id'=>intval($relv['rel_id']));
                }else
                {
                    $table  =   'voice';
                    $where  =   array('voice_id'=>intval($relv['rel_id']));
                }
                $info	=	$this->cimongo->where($where)->get($table)->row_array();
                if (!empty($info))
                {
                    $return_into[$relk]	=	$info;
                }
            }
            return $return_into;

        }else
        {
            return ;
        }
    }

    /**
     * @{根据id数组获取播放器声音列表}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param array $voice_id
     * @ return bool
     */
    function get_player_voice($voice_id=array())
    {
        $voice_list=array();
        foreach ($voice_id as $id)
        {
            $voice  =   $this->cimongo->where(array('voice_id'=>intval($id)))->get('voice')->row_array();
            if(!empty($voice))
            {
                $username   =$this->cimongo->select(array('nickname','_id'))->where(array('user_id'=>intval($voice['user_id'])))->get('user')->row_array();
                if(!empty($username))
                {
                    $voice['nickname']=$username['nickname'];//此首歌的上传者
                }else
                {
                    $voice['nickname']='';//此首歌的上传者
                }
                //获取声音的封面
                foreach ($voice['voice_pic_list'] as $vpk=>$vpv)
                {
                    if($vpv['is_cover']==1)
                    {
                        $voice['voice_pic']=$vpv['picture_s_thumb_path_md5'];
                    }
                }
                if (empty($voice['voice_pic']))
                {
                    if(!empty($voice['voice_pic_list']))
                    {
                        $voice['voice_pic']=$voice['voice_pic_list'];
                    }else
                    {
                        $voice['voice_pic']='';
                    }
                }

            }
            unset($voice['voice_pic_list']);
            if(!empty($voice_id))
            {
                array_push($voice_list,$voice);
            }
        }
        return $voice_list;


    }
    function get_voice_list()
    {
        
    }







}