<?php
/**
 * album(专辑)相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class album_model extends CI_Model
{

    /**
     * album_model constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('cimongo');
    }

    /**
     * @{获取精选专辑}
     * @author lichao
     * @${2016-06-06}
     * @modifier lichao
     * @${DATE}
     * @param int $limit
     * @param int $page
     * @ return bool
     */
    function get_best_album($limit=8,$page=1,$sort=array('sort_order'=>-1),$last_request_time='')
    {
        if($page<1)
        {
            return ;
        }
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip=($page-1)*$limit;
        $this->cimongo->order_by($sort);
        $this->cimongo->select(array('_id','album_id'));
        $where  =   array
        (
            'create_time'=>array('$lte'=>$last_request_time)
        );
        $album_id=$this->cimongo->skip($skip)->limit($limit)->where($where)->get('picked_album_omnibus')->result_array();
        $album  =   array();
        foreach ($album_id as $alk=>$alv)
        {
            $where          =   array('album_id'=>$alv['album_id']);
            $album_info     =   $this->cimongo->where($where)->get('voice_album')->row_array();
            $album[$alk]    =   $album_info;
        }
        return $album;
    }
    function get_album_list($user_id,$limit=20,$page=1,$last_request_time)
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip   =   ($page-1)*$limit;
        $where  =   array
        (
            'user_id'=>intval($user_id),
            'create_time'=>array('$lte'=>$last_request_time)
        );
        $alnum_list =   $this->cimongo->skip($skip)->limit($limit)->where($where)->get('voice_album')->result_array();
        return $alnum_list;
    }
}