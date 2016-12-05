<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $index = 'class=active';
        $this->assign( 'index', $index);
        $this->redirect('Index/teacherList');
    }
    public function video(){
        $this->display('Index/video');
    }

    public function article(){
        $this->display('Index/articlelist');
    }
    public function articleDetail(){
        $article = M('article');
        $where=['status'=>1,'id'=>intval(I('id'))];
        $detail=$article->where($where)->find();
        $detail['content']=htmlspecialchars_decode($detail['content']);
        $this->assign('detail',$detail);
        $this->display('Index/articledetail');
    }
    public function articleList(){
        $article = M('article');
        $where=['status'=>1];
        $start= I('start');
        $length= I('length');
        $list=$article->where($where)->order('id desc')->limit($start,$length)->select();
        foreach ($list as &$value){
           $value['time'] = date('Y-m-d',$value['time']);
        }
        $this->ajaxReturn($list);
    }

    public function teacherList(){
        $teacher = 'class=active';
        $wheres =array('status'=>1);
        $searchInfo = $this->searchDatas($wheres);
        $result = array();
        foreach ($searchInfo as $value){
            $tmp=$this->selectData($value);
            $result[]=$tmp;
        }
        $this->assign('searchInfo',$result);
        $this->assign( 'teacher', $teacher);
        $this->display('Index/teachers');
    }
    public function teacherInfo(){
        $teacher = M('Teacher');
        $searchs = M('Search');
        $where = array();
        $where['uid']=I('get.uid');
        $teacherInfo = $teacher->where($where)->find();
        $teacherInfo['photo'] = '/person'.$teacherInfo['photo'];
        $searchInfo  =  $searchs->where($where)->find();
        $infos = $this->selectData($searchInfo);
        $teacherInfo['class'] = $infos['class'];
        $teacherInfo['subject'] = $infos['subject'];
        $this->assign('teacherInfo',$teacherInfo);
        $this->display('Index/teacherInfo');

    }
    public function searchTeacher(){
        if(empty($_POST['name'])){
            return false;
        }
        $searchInfo= M('Search');
        $teachers  = M('Teacher');
        $teacherInfo = $teachers->where(['name'=>trim($_POST['name'])])->order('time desc')->select();
        foreach($teacherInfo as $value){
            $searchs = $searchInfo->where(['uid'=>$value['uid']])->find();
            if($searchs['status']==1){
                $newsearchs=$this->selectData($searchs);
                $result[]=array_merge($newsearchs,$value);
            }
        }
        $this->ajaxReturn($result);
    }
    public function teachers(){
           $where = '';
           if(isset($_POST['class']) && !empty($_POST['class'])){
                $where .=I('post.class').'=1';
           }
           if(isset($_POST['subject']) && !empty($_POST['subject'])){
               if(empty($where)){
                   $where = I('post.subject').'=1';
               }else{
                   $where .= ' AND '.I('post.subject').'=1';
               }
           }
        
            if($_POST['sex']!=NULL){
                if(empty($where)){
                    $where = 'sex='.I('post.sex');
                }else{
                    $where .= ' AND sex='.I('post.sex');
                }
            }
            if(isset($_POST['city'])&& !empty($_POST['city'])){
                if(empty($where)){
                    $where  =  'city="'.strval($_POST['city']).'"';
                }else{
                    $where .= ' AND city="'.strval($_POST['city']).'"';
                }
            }

           if(empty($where)){
               $wheres ='status=1';
           }else{
               $wheres=$where.' AND status=1';
           }
           if(!empty(I('lastTime'))){
               $wheres.=' AND time<'.I('lastTime');
           }
          
        $searchInfo = $this->searchDatas($wheres);
           $result = array();
           foreach ($searchInfo as $value){
               $tmp=$this->selectData($value);
               $result[]=$tmp;
           }
            $this->ajaxReturn($result);
    }
    private function searchDatas($wheres){
         $searchInfo= M('Search');
         $teachers  = M('Teacher');
        $searchs = $searchInfo->where($wheres)->order('time desc')->limit(4)->select();
      
         if($searchs){
             foreach ($searchs as &$val){
                 $infos= $teachers->where(['uid'=>$val['uid']])->find();
                 $val['name'] = $infos['name'];
                 $val['region'] = $infos['region'];
                 $val['photo']  = $infos['photo'];
             }
            return $searchs;
         }
        return array();
    }
    public function selectData($data){
        $class = array(
            'one'       =>'一年级',
            'two'       =>'二年级',
            'three'     =>'三年级',
            'four'      =>'四年级',
            'five'      =>'五年级',
            'six'       =>'六年级',
            'seven'     =>'初一',
            'eight'     =>'初二',
            'nine'      =>'初三',
            'ten'       =>'高一',
            'eleven'    =>'高二',
            'twelve'    =>'高三'

        );
        $subject = array(
            'chinese'   =>'语文',
            'math'      =>'数学',
            'english'   =>'英语',
            'physics'   =>'物理',
            'chemistry' =>'化学',
            'biology'   =>'生物',
            'politics'  =>'政治',
            'geography' =>'地理',
            'history'   =>'历史',
            'art'       =>'艺术'
        );
        $result = array('class'=>'','subject'=>'');
        foreach ($data as $k=>$v){
                if(in_array($k,array_keys($class))){
                    if($v==1) {
                        $result['class'] .= $class[$k] . ' ';
                    }
                }
                if(in_array($k,array_keys($subject))){
                    if($v==1) {
                        $result['subject'] .= $subject[$k] . ' ';
                    }
                }
            }
        $data['class'] = $result['class'];
        $data['subject'] = $result['subject'];

        return $data;
    }
  
    public function about(){
          $about = 'class=active';
          $this->assign( 'about', $about);
          $this->display();
    }
    public function introduction(){
        $introduction = 'class=active';
        $this->assign( 'introduction', $introduction);
        $this->display();
    }
    /**
     *注册
     */
    public function register(){
        $info =  D('Userinfo');
        if(!$info->create()){
            $this->ajaxReturn($info->getError());
            exit();
        }else{
            if($info->add()){
                $userInfo = M("Userinfo")->where("phone = ".I('post.phone'))->find();
                $sessionInfo = array(
                    'id'           =>  $userInfo['id'],
                    'nickname'     =>  $userInfo['nickname'],
                    'login'        => empty($userInfo['id']) ? 0 :1,
                    'admin'        => $userInfo['admin']
                );
                session('login',$sessionInfo);

                $this->ajaxReturn(1);
                exit();
            }else{
                $this->ajaxReturn('注册失败！');
            }


        }

    }
    public function logout(){
        session('login',null);
        $this->redirect('Index/index');
    }
   
    public function login(){
        $password = I('post.password');
        $pass     = md5('1d1-'.$password);
        $phone    = I('post.telphone');
        if(empty($password) || empty($phone)){
            $this->ajaxReturn('登陆失败,请检查账号是否有误!','JSON');
        }
        $userInfo = M("Userinfo")->where('phone='.$phone.' AND password="'.$pass.'"'.' AND status=1')->find();
        if(!empty($userInfo)){
            $sessionInfo = array(
                'id'           =>  $userInfo['id'],
                'nickname'     =>  $userInfo['nickname'],
                'login'        => 1,
                'admin'        => $userInfo['admin']
            );
            session('login',$sessionInfo);

            $this->ajaxReturn('success');
            exit();
        }else{
            $this->ajaxReturn('登陆失败,请检查账号是否有误!','JSON');
        }
    }
    private function ipaddr(){
        $Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
        $pp = get_client_ip();
        $area = $Ip->getlocation($pp); // 获取某个IP地址所在的位置
//        Array
//        (
//            [ip] => 223.167.129.240
//    [beginip] => 223.167.28.112
//    [endip] => 223.167.255.255
//    [country] => 上海市
//        [area] => 联通
//);
        return $area;

    }
}