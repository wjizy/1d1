<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/28 0028
 * Time: 下午 11:27
 */
namespace Home\Model;
use Think\Model;
class UserinfoModel extends Model {
    protected $_validate = array(
    array('nickname','checkName','姓名过长',0,'callback'), //
    array('passagain','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
    array('phone','number','手机号码格式错误',2),
    array('phone','','手机号已经存在！',0,'unique',1),
    array('password','checkPwd','密码格式不正确',0,'callback'), // 自定义函数验证密码格
    array('qq','','QQ已经存在！',0,'unique',1),
    array('qq','checkQQ','QQ格式不正确',0,'callback'),

);
    protected $_auto = array(
        array('password','passFunc',3,'callback'),
        array('status',1),
        array('type',1),
        array('admin',0),
        array('time','startTime',3,'callback')
    );
  
    public function checkQQ(){
        $QQ  = I('post.qq');
        if(strlen($QQ) < 12 && strlen($QQ) > 4 && intval($QQ) > 0){
            return true;
        }else{
            return false;
        }
    }
    public function checkName(){
        $nickname  = I('post.nickname');
        if(strlen($nickname) < 12 &&  !empty($nickname)){
            return true;
        }else{
            return false;
        }
    }
    public function startTime(){
        return time();
    }
    public function passFunc(){
        $pwd  = I('post.password');
        return $this->encryption($pwd);
    }
    public function encryption($pwd){
        return md5('1d1-'.$pwd);
    }
    public function checkPwd(){
            $pwd  = I('post.password');
            if(strlen($pwd) < 6 || strlen($pwd)>15){
                return false;
            }else{
                return true;
            }
    }
}