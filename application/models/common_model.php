<?php
/**
 * banner相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class common_model extends CI_Model
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
     * @{获取集合文档总数}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $name
     * @ return bool
     */
    function get_all_nums($name)
    {
        $num=$this->cimongo->get($name)->num_rows();
        return $num;
    }
}