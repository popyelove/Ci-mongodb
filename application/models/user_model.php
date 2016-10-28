<?php
/**
 * 相关模型类
 * Created by PhpStorm.
 * User: lichao
 * Date: 2016/6/6
 * Time: 14:27
 */
class user_model extends CI_Model
{
    /**
     * user_model constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('cimongo');
    }

    /**
     * @{获取热门主播}
     * @author lichao
     * @${2016/06/06}
     * @modifier lichao
     * @${DATE}
     * @param int $limit
     * @param int $page
     * @ return bool
     */
    function get_hot_anchor($limit=10, $page=1)
    {
        $skip   =   ($page-1)*$limit;
        if($page<1)
        {
            return;
        }
        $sort	=	array
        (
            'sort_order'=>-1,
        );
        $hot_anchor_id	=	$this->cimongo->order_by($sort)->skip($skip)->limit($limit)->get('picked_hot_anchor')->result_array();
        $hot_anchor     =   array();
        foreach ($hot_anchor_id as $haik=>$haiv)
        {
            $where              =   array('user_id'=>intval($haiv['user_id']));
            $anchor_info        =   $this->cimongo->where($where)->get('user')->row_array();
            if(!empty($anchor_info))
            {
                $hot_anchor[$haik]  =   $anchor_info;
            }
        }
        return $hot_anchor;
    }

    /**
     * @{获取新进主播}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $limit
     * @param $page
     * @ return bool
     */
    function get_new_anchor($limit=10,$page=1)
    {
        if($page<1)
        {
            return ;
        }
        $skip=($page-1)*$limit;
        $sort	=	array
        (
            'sort_order'=>-1,
        );
        $new_anchor_id	=	$this->cimongo->order_by($sort)->skip($skip)->limit($limit)->get('picked_new_anchor')->result_array();
        $new_anchor     =   array();
        foreach ($new_anchor_id as $naik=>$naiv)
        {
            $where              =   array('user_id'=>intval($naiv['user_id']));
            $anchor_info        =   $this->cimongo->where($where)->get('user')->row_array();
            if(!empty($anchor_info))
            {
                $new_anchor[$naik]  =   $anchor_info;
            }
        }
        return $new_anchor;
    }

    /**
     * @{发现里的电台-优秀电台}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param int $limit 每页数量
     * @param int $page 页码
     * @param int $type 1-上升最快2-最热电台
     * @ return bool
     */
    function get_good_radio($limit=12, $page=1, $type=1,$last_request_time)
    {
        if($page<1)
        {
            return;
        }
        if(empty($last_request_time))
        {
            $last_request_time  =   time();
        }
        $skip	=	($page-1)*$limit;
        $where	=	array
        (
            'time'=>strtotime(date('Y-m-d')),
            'create_time'=>array('$lte'=>$last_request_time)
        );
        if($type==1)
        {
            //上升最快
            $sort	=	array
            (
                'num_7'=>-1
            );
            $radio_id	=	$this->cimongo->skip($skip)->limit($limit)->order_by($sort)->where($where)->get('pick_good_radio')->result_array();

        }else
        {
            //最热电调，按照关注数排序
            $sort	=	array
            (
                'follow_count'=>-1
            );
            $radio_id	=	$this->cimongo->skip($skip)->limit($limit)->order_by($sort)->where($where)->get('pick_good_radio')->result_array();
        }
        $good_radio	=	array();
        if(!empty($radio_id))
        {
            foreach ($radio_id as $rak=>$rav)
            {
                $where      =   array
                (
                    'user_id'=>intval($rav['user_id'])
                );
                $radio_info =   $this->cimongo->where($where)->get('user')->row_array();
                if(!empty($radio_info))
                {
                    array_push($good_radio,$radio_info);
                }
            }
            return $good_radio;
        }else
        {
            return;
        }
    }

    /**
     * @{获取个人信息}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $user_id
     * @param $type
     * @ return bool
     */
    function get_personal_info($user_id)
    {
        $select =   array
        (
            'avatar',
            'nickname',
            'signature',
            'friend_count',
            'voice_count',
            'follower_count',
            'album_count',
            'level'
        );
        $where  =   array
        (
            'user_id'=>intval($user_id),
        );
        $user   =   $this->cimongo->select($select)->where($where)->get('user')->row_array();
        if(!empty($user))
        {
            return $user;
        }else
        {
            return;
        }

    }

    /**
     * @{获取好友列表}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $user_id
     * @ return bool
     */
    function get_friend_list($limit=16,$page=1,$user_id,$last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip       =   ($page-1)*$limit;
        $opt        =   array
        (
            array('$match'=>array('user_id'=>intval($user_id),'reg_time'=>array('$lte'=>$last_request_time))),
            array('$project'=>array('friend_id_array'=>1,'_id'=>0)),
            array('$unwind'=>'$friend_id_array'),
            array('$skip'=>intval($skip)),
            array('$limit'=>intval($limit))
        );
        $res=$this->cimongo->aggregate('user',$opt);
        $friend_id  =   $res['result'];
        $friend_list    =   array();
        if(!empty($friend_id))
        {
            foreach ($friend_id as $frk=>$frv)
            {
                $user_info  =   $this->cimongo->where(array('user_id'=>intval($frv['friend_id_array'])))->get('user')->row_array();
                if(!empty($user_info))
                {
                    array_push($friend_list,$user_info);
                }
            }
        }
        return $friend_list;
    }

    /**
     * @{他们也在一说}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param int $limit
     * @param int $page
     * @ return bool
     */
    function thay_in_yishuo($limit=5, $page=1,$user_id,$last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip   =   ($page-1)*$limit;
        $friend_id_list =   $this->cimongo->select(array('friend_id_array','_id'))
                            ->where(array('user_id'=>$user_id))->get('user')->row_array();
        if(!empty($friend_id_list))
        {
            $friend_id_list=$friend_id_list['friend_id_array'];
            array_push($friend_id_list,$user_id);
        }
        $select =   array
        (
            '_id',
            'user_id',
            'nickname',
            'avatar_s_thumb',
            'signature'
        );
        $user   =   $this->cimongo->skip($skip)->limit($limit)
                    ->select($select)->where(array('reg_time'=>array('$lte'=>$last_request_time)))
                    ->where_not_in('user_id',$friend_id_list)
                    ->where_ne('bind_weibo','')->get('user')->result_array();
        return $user;
    }

    /**
     * @{获取电台主播的粉丝列表}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $user_id
     * @param $limit
     * @param $page
     * @param $last_request_time
     * @ return bool
     */
    function get_fans_list($user_id, $limit, $page, $last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip       =   ($page-1)*$limit;
        $opt        =   array
        (
            array('$unwind'=>'$follower_user_list'),
            array('$match'=>array('user_id'=>intval($user_id),'follower_user_list.create_time'=>array('$lte'=>$last_request_time),'level'=>array('$in'=>array(1,2)))),
            array('$project'=>array('follower_user_list'=>1,'_id'=>0)),
            array('$skip'=>intval($skip)),
            array('$limit'=>intval($limit))
        );
        $res=$this->cimongo->aggregate('user',$opt);
        if(!empty($res['result']))
        {
            $friend_id_array    =   $res['result'];
            $fans_list          =   array();//粉丝列表
            foreach ($friend_id_array as $fiak=>$fiav)
            {
                $select =   array
                (
                    '_id',
                    'user_id',
                    'ys_username',
                    'nickname',
                    'voice_count',
                    'friend_count',
                    'avatar',
                    'avatar_l_thumb',
                    'avatar_s_thumb',
                    'signature'

                );
                $this->cimongo->select($select);
                $where  =   array
                (
                    'user_id'=>intval($fiav['follower_user_list']['user_id']),
                );
                $user   =   $this->cimongo->where($where)->get('user')->row_array();
                if(!empty($user_id))
                {
                    array_push($fans_list,$user);
                }
            }
            return $fans_list;
        }else
        {
            return array();
        }
    }

    /**
     * @{获取主播专辑列表}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $user_id
     * @param $limit
     * @param $page
     * @param string $last_request_time
     * @return array
     * @ return bool
     */
    function get_subscribe_list($user_id, $limit, $page, $last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip       =   ($page-1)*$limit;
        $opt        =   array
        (
            array('$unwind'=>'$subscribe_album_array'),
            array('$match'=>array('user_id'=>intval($user_id),'subscribe_album_array.create_time'=>array('$lte'=>$last_request_time),'level'=>1)),
            array('$project'=>array('subscribe_album_array'=>1,'_id'=>0)),
            array('$skip'=>intval($skip)),
            array('$limit'=>intval($limit))
        );
        $res=$this->cimongo->aggregate('user',$opt);
        if(!empty($res['result']))
        {
            $album_id_array    =   $res['result'];
            $album_list          =   array();//专辑列表
            foreach ($album_id_array as $aiak=>$aiav)
            {
                $where  =   array
                (
                    'album_id'=>intval($aiav['subscribe_album_array']['album_id']),
                );
                $album   =   $this->cimongo->where($where)->get('voice_album')->row_array();
                if(!empty($user_id))
                {
                    array_push($album_list,$album);
                }
            }
            return $album_list;
        }else
        {
            return array();
        }
    }

    /**
     * @{获取关注电台列表}
     * @author lichao
     * @${DATE}
     * @modifier lichao
     * @${DATE}
     * @param $user_id
     * @param $limit
     * @param $page
     * @param string $last_request_time
     * @ return bool
     */
    function get_radio_list($user_id, $limit, $page, $last_request_time='')
    {
        if(empty($last_request_time))
        {
            $last_request_time=time();
        }
        $skip       =   ($page-1)*$limit;
        $opt        =   array
        (
            array('$unwind'=>'$follower_user_list'),
            array('$match'=>array('user_id'=>intval($user_id),'follower_user_list.create_time'=>array('$lte'=>$last_request_time))),
            array('$project'=>array('follower_user_list'=>1,'_id'=>0)),
            array('$skip'=>intval($skip)),
            array('$limit'=>intval($limit))
        );
        $res=$this->cimongo->aggregate('user',$opt);
        $radio_list          =   array();//电台
        if(!empty($res['result']))
        {
            $radio_id_array    =   $res['result'];
            foreach ($radio_id_array as $riak=>$riav)
            {
                $where  =   array
                (
                    'user_id'=>intval($riav['follower_user_list']['user_id']),
                );
                $select =   array
                (
                    '_id',
                    'nickname',
                    'avatar_s_thumb',
                    'signature',
                    'follow_count'
                );
                $user   =   $this->cimongo->select($select)->where($where)->get('user')->row_array();
                if(!empty($user_id))
                {
                    array_push($radio_list,$user);
                }
            }

        }
        return $radio_list;
    }














}