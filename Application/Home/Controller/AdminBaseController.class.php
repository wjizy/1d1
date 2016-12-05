<?php
namespace Home\Controller;
use Think\Controller;
class AdminBaseController extends Controller {
    public function _initialize() {
        if (empty(session('login')) || session('login.login')!=1 || session('login.admin')!=1) {
            $this->redirect("Index/index");
        }
    }
}