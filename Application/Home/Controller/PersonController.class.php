<?php
namespace Home\Controller;
use Home\Model\UserinfoModel;
use Think\Controller;
class PersonController extends BaseController {

    public function PersonInfo(){
        $active = array(1=>'',2=>'',3=>'');
        switch (I('get.type')){
            case 1 :
                $this->baseInfo(session('login.id'));
                break;
            case 2:
                $this->teacherInfo(session('login.id'));
                break;
            case 3:
                $this->modifyPwd();
                break;
            default:
                $this->redirect('Index/index');
                break;
        }
        $this->assign('chooseType',I('get.type'));
        $active[I('get.type')]='class = active';
        $this->assign('active',$active);
        $this->display('Person/person');
    }
    public function modify(){
        $user = D('Userinfo');
        $oldPwd = $user->encryption(I('post.oldPwd'));
        $userInfo = $user->where("phone = '".I('post.telphone')."' AND password='{$oldPwd}'")->find();
        if(empty($userInfo) || $userInfo['id'] != session('login.id')){
            $this->ajaxReturn(0);
        }
        if(strlen(I('post.newPwd')) < 6 || strlen(I('post.newPwd')) > 15){
            $this->ajaxReturn(0);
        }
        $newPwd = $user->encryption(I('post.newPwd'));
        $result= $user->where('id='.$userInfo['id'])->save(array('password'=>$newPwd));
        if($result == false){
            $this->ajaxReturn(0);
        }
        session('login',null);
        $this->ajaxReturn(1);
    }
    public function apply(){
        if(empty(session('photoPath')) && I('post.etype')!='1'){
            $this->ajaxReturn('头像不能为空!');
            exit;
        }
        if(empty(I('post.name')) || strlen(I('post.name')) > 15){
            $this->ajaxReturn('名字填写异常');
            exit;
        }
        if(!in_array(I('post.sex'),array(0,1))){
            $this->ajaxReturn('性别选择有误');
            exit;
        }
        if(strlen(I('post.college')) < 3 || strlen(I('post.college')) > 50){
            $this->ajaxReturn('请检查大学填写是否正常！');
            exit;
        }
        if(strlen(I('post.major')) < 3 || strlen(I('post.major')) > 50){
            $this->ajaxReturn('请检查专业填写是否正常！');
            exit;
        }
        if(strlen(I('post.region')) < 3 || strlen(I('post.region')) > 50){
            $this->ajaxReturn('请检查所在区域填写是否正常！');
            exit;
        }
        if(strlen(I('post.phistory')) < 30 || strlen(I('post.phistory')) > 2000){
            $this->ajaxReturn('个人介绍30到2000个字符以内！');
            exit;
        }
        if(strlen(I('post.subjectInfo')) < 30 || strlen(I('post.phistory')) > 2000){
            $this->ajaxReturn('学科解读30到2000个字符以内');
            exit;
        }
        $data = array();
        $data['uid']   = session('login.id');
        if(I('etype') == 1 && empty(session('photoPath'))){
        }else{
            $src_photo  = session('photoPath');
            $type       = array_pop(explode('.',session('photoPath')));
            $newPaths   = '/userpic/'.session('login.id').'_'.date(YmdHis).'.'.$type;
            copy($src_photo,'Public/person'.$newPaths);
            $image      = new \Think\Image();
            $image->open($src_photo);
            $image->thumb(100, 100)->save('Public'.$newPaths);
            session('photoPath','');
            $data['photo'] = $newPaths;
        }
        $data['name']  = I('post.name');
        $data['sex']   = I('post.sex');
        $data['college']=I('post.college');
        $data['major']=I('post.major');
        $data['study']=I('post.study');
        $data['region']=I('post.region');
        $data['phistory']=I('post.phistory');
        $data['subjectinfo']=I('post.subjectInfo');
        $data['time']=time();
        $teacher = M('teacher');
        $tInfo= $teacher->where('uid='.$data['uid'])->find();
        //修改
        if($tInfo){
             $teacher->where('uid='.$data['uid'])->save($data);
             $chose = array(
                'one'   => 0,
                'two'   => 0,
                'three' => 0,
                'four'  => 0,
                'five'  => 0,
                'six'   => 0,
                'seven' => 0,
                'eight' => 0,
                'nine'  => 0,
                'ten'   => 0,
                'eleven'=> 0,
                'twelve'=> 0,
                'chinese'=> 0,
                'math'  => 0,
                'english'=>0,
                'physics'=>0,
                'chemistry'=>0,
                'biology'  =>0,
                'politics'=>0,
                'geography'=>0,
                'history' => 0,
                'art'      =>0,
                 'status'  => 0,
                 'sex'     => I('post.sex'),
                 'city'    => $data['region'],
                 'time'    => time()
            );
            $n=0;
            foreach($chose as $key=>$val){
                if(I('post.'.$key) == 1){
                    $chose[$key] =1;
                    $n++;
                }
            }
            if(!$n){
                $this->ajaxReturn('年级或学科不能为空');
            }
            M('search')->where('uid='.$data['uid'])->save($chose);
            $this->ajaxReturn(1);
        }
        //增加
        if(empty($tInfo) && $teacher->add($data)){
            $chose = array(
                'one'   => 0,
                'two'   => 0,
                'three' => 0,
                'four'  => 0,
                'five'  => 0,
                'six'   => 0,
                'seven' => 0,
                'eight' => 0,
                'nine'  => 0,
                'ten'   => 0,
                'eleven'=> 0,
                'twelve'=> 0,
                'chinese'=> 0,
                'math'  => 0,
                'english'=>0,
                'physics'=>0,
                'chemistry'=>0,
                'biology'  =>0,
                'politics'=>0,
                'geography'=>0,
                'history' => 0,
                'art'     => 0,
                'sex'     => I('post.sex'),
                'status'  => 0,
                'city'    => $data['region'],
                'time'    => time()
                );
            $n=0;
            foreach($chose as $key=>$val){
                if(I('post.'.$key) == 1){
                    $chose[$key] =1;
                    $n++;
                }
            }
            if(!$n){
                $this->ajaxReturn('年级或学科不能为空');
            }
            $chose['uid'] = $data['uid'];
            if(M('search')->add($chose)){
                M('userinfo')->where('id='.$data['uid'])->save(['type'=>2]);
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn('年级或学科选择失败');
            }
        }else{
            $this->ajaxReturn('操作失败！');
        }

    }
    public function photo(){
        $config = array(
            'maxSize' => 819200,//800k
            'rootPath'=> './Application/Runtime',
            'savePath' => '/Temp',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg' , 'png', 'jpeg'),
            'autoSub' => true,
            'subName' => array('date', '/Y/m/d'),
        );
        $upload = new \Think\Upload($config);
        $info = $upload->upload(); // 上传文件

        if (!$info) {
             $error = $upload->getError();
            echo '<script>window.parent.uploadFileError("' . $error. '")</script>';
        } else {
            foreach ($info as $file) {
                $file['savepath']= trim($file['savepath'],'.');
                $picname ='/Application/Runtime'.$file['savepath'].$file['savename'];
            }
            session('photoPath',RUNTIME_PATH.$file['savepath'].$file['savename']);
            $picname = trim($picname, '.');
            echo '<script>window.parent.uploadFile("' . $picname . '")</script>';
        }
    }
    private function modifyPwd(){

    }
    private function teacherInfo($id){
        $teaInfo = M('teacher');
        $info = $teaInfo->where('uid ='.$id)->find();
        if($info){
            if($infos = M('search')->where('uid='.$id)->find()){
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
                unset($infos['id']);
                unset($infos['uid']);
                $info['class']  ='';
                $info['subject']='';
                foreach ($infos as $k=>$v){
                    if($v==1){
                        if(in_array($k,array_keys($class))){
                            $info['class'].=$class[$k].' ';
                        }
                        if(in_array($k,array_keys($subject))){
                            $info['subject'].=$subject[$k].' ';
                        }
                    }
                    
                }
                $info['chose']=$infos;
                $info['photo']='/person'.$info['photo'];
                $this->assign('teaType',1);
                $this->assign('teaInfo',$info);
            }

        }else{
            $this->assign('teaType',0);
        }
    }
    private function baseInfo($id){

        $user = M("Userinfo")->where('id = '.$id)->find();
       
        $res = array(
            'nickname'  => $user['nickname'],
            'phone'     => $user['phone'],
            'qq'        => $user['qq'],
            'type'      => $user['type']
        );
        $this->assign('baseInfo',$res);
    }
}