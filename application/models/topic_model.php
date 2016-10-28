<?php
/**
 * topic相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class topic_model extends CI_Model
{

    /**
     * topic_model constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('cimongo');
    }

    /**
     * @{获取热门话题}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param int $skip
     * @param int $limit
     * @ return bool
     */
    function get_hot_topic($limit=10,$page=1)
    {
        $skip=($page-1)*$limit;
        $this->cimongo->select(array('topic_id','_id'));
        $this->cimongo->order_by(array('sort_order'=>-1));
        $topic_info=$this->cimongo->skip($skip)->limit($limit)->get('picked_hot_topic')->result_array();
        foreach ($topic_info as $topk=>$topv)
        {
            $topic_name=$this->cimongo->select(array('topic_name','_id'))->where(array('topic_id'=>intval($topv['topic_id'])))->get('voice_topic')->row_array();
            $topic_info[$topk]['topic_name']    =   $topic_name['topic_name'];
        }
        return $topic_info;
    }
}