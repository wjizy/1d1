<?php
namespace Home\Controller;
class AdminController extends AdminBaseController {

    public function back(){
        layout(false);
        $this->assign('m',I('get.do'));
        $this->display('./Public/admin/index.html');
    }
    public function getAdminInfo(){
        $userInfo = M('Userinfo');
        $where = array(
            'id' => session('login.id')
        );
        $info = $userInfo->where($where)->find();
        $this->ajaxReturn($info);
    }
    public function getUserList(){
        if(I('status')!=2){
            $this->getUserListBystatus(I('status'),I('start'),I('length'));
        }
        $userInfo = M('Userinfo');
        $list = $userInfo->order('time DESC')->limit(I('start'). ',' .I('length'))->select();
        $searchs = M('Search');
        $teacher = M('Teacher');
        foreach ($list as &$v){
            $v['search_status']=$searchs->where('uid='.$v['id'])->getField('status');
            $v['realname']     =$teacher->where('uid='.$v['id'])->getField('name');
        }
        $this->ajaxReturn($list);
    }
    public function getUserListBystatus($status,$start,$length){
        $find = array('status'=>$status);
        $searchinfo = M('search');
        $list = $searchinfo->where($find)->order('time DESC')->limit($start . ',' . $length)->select();
        $userInfo = M('userinfo');
        $tearchInfo = M('teacher');
        foreach($list as &$v){
            $v['realname']  = $tearchInfo->where(['uid'=>$v['uid']])->getField('name');
            $v['search_status']= $v['status'];
            $v['id'] = $v['uid'];
            $info = $userInfo->where(['id'=>$v['id']])->find();
            $v['phone'] = $info['phone'];
            $v['qq'] = $info['qq'];
            $v['type'] = $info['type'];
            $v['status'] = $info['status'];
            $v['admin'] = $info['admin'];
        }
        $this->ajaxReturn($list);
    }
    public function searchUser(){
        switch (I('select')){
            case 'phone':
                $where = array('phone'=>I('condition'));
                break;
            case 'qq':
                $where = array('qq'=>I('condition'));
                break;
            case 'uid':
                $where = array('id'=>I('condition'));
                break;
            default:
                $where = array('phone'=>I('condition'));
        }
        $searchs = M('Search');
        $teacher = M('Teacher');
        $userInfo = M('Userinfo');
        $list=$userInfo->where($where)->select();
        foreach ($list as &$v){
            $v['search_status']=$searchs->where('uid='.$v['id'])->getField('status');
            $v['realname']     =$teacher->where('uid='.$v['id'])->getField('name');
        }
        $this->ajaxReturn($list);
    }


    //封禁用户不准登陆
    public function shutUser(){
        $uid = I('uid');
        if(empty($uid)){
            $this->ajaxReturn(false);
        }
        if($uid == session('login.id')){
            $this->ajaxReturn(false);
        }
        $where = array('id'=>$uid);
        $data  = array('status'=>0);
        $uInfo = M('Userinfo');
        $result= $uInfo->where($where)->save($data);
        if($result){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
    //教员下上线
    public function teacherStatus(){
         $info = M('Search');
         $status = I('status');
         $uid    = I('uid');
        $where   = array(
            'uid'   =>$uid,
        );
        $data= array('status'=>$status);
        if($info->where($where)->save($data)){
            $this->ajaxReturn(true);
        }
        $this->ajaxReturn(false);

    }
    public function resetPwd(){
        $uid = I('uid');
        $pwd = I('pwd');
        $user = D('Userinfo');
        $passwd= $user->encryption($pwd);
        if(session('login.id')==$uid){
            $this->ajaxReturn(false);
        }
        $data = array('password'=>$passwd);
        $where = array('id'=>$uid);
        trace(json_encode($data));
        if($user->where($where)->save($data)){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
    public function addArticle(){
        $uid=session('login.id');
        $title=I('title');
        $content=I('content');
        $tag=I('tag');
        $abstract=I('abstract');
        $time = time();
        $viewnum=1;
        $status=0;
        if(!$uid || !$title || !$content || !$tag || !$abstract || !$time){
            $this->ajaxReturn(false);
        }
        $data=array(
            'uid'=>$uid,
            'title'=>$title,
            'content'=>$content,
            'tag'=>$tag,
            'abstract'=>$abstract,
            'time'=>$time,
            'viewnum'=>$viewnum,
            'status'=>$status
        );
        trace(json_encode($data));
        $article = M('article');
        if($article->add($data)){
           $this->ajaxReturn(true);
        }
        $this->ajaxReturn(false);
    }
    public function getArticleList()
    {
        $article = M('article');
        $list= $article->where(null)->order('time DESC')->limit(intval(I('start')) . ',' . intval(I('length')))->select();
        foreach ($list as &$value){
            $value['abstract']=  htmlspecialchars_decode($value['abstract']);
            $value['content'] =  htmlspecialchars_decode($value['content']);
        }
        $this->ajaxReturn($list);
    }
    public function getArticleDetail()
    {
       $id = I('id');
       $article = M('article');
       $detail =  $article->find($id);
        $detail['content'] =  htmlspecialchars_decode($detail['content']);
       $this->ajaxReturn($detail);
    }
    public function articleOnline()
    {
       $article = M('article');
        $result =$article->where(['id'=>intval(I('id'))])->save(['status'=>I('status')]);
        if($result){
            $this->ajaxReturn(true);
        }
        $this->ajaxReturn(false);
    }
    public function articleDelete()
    {
        $article = M('article');
        $result =$article->where(['id'=>intval(I('id'))])->delete();
        if($result){
            $this->ajaxReturn(true);
        }
        $this->ajaxReturn(false);
    }
}