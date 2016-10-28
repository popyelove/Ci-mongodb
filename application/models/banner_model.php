<?php
/**
 * banner相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class banner_model extends CI_Model
{
    /**
     * banner_model constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('cimongo');
    }

    /**
     * @{获取主页轮播图}
     * @author lichao
     * @${2016-06-06}
     * @modifier lichao
     * @${DATE}
     * @param int $per_page
     * @return mixed
     * @return bool
     */
    function get_banner_list($per_page = 4)
    {
        $this->cimongo->order_by(array('sort_order'=>-1));
        $this->cimongo->limit($per_page);
        return $this->cimongo->get('banner')->result_array();
    }
}