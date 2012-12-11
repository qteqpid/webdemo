<?php

require_once dirname(__FILE__).'/Api.php';

class UserController extends Api {
	
	/**
	 * 关注某人
	 * *
	 */
	public function actionPostFollow() {
		if ($this->verifyUser() && $this->checkLogin()) {
			$optObj = $this->parseParam(array(
				'un' => MD::app()->user->getUsername(),
			));
			if ($optObj === false) {
				return $this->returnError("user/follow.post", "param error", IApi::ERROR_NO_PARAM);
			}
			$un = $optObj->un;
			$uid = MD::app()->user->getId();
			$result = UserFollowModel::model()->find(new Criteria(array(
				'where' => array('whom' => $optObj->uid,'who' => $uid),
				'limit' => 1
			)));
			if(!$result){//?:没有判断用户是否存在啊
				$result = UserFollowModel::model()->addUserFollow(array('whom' => $optObj->uid, 'who' => $uid));
				if($result){
							
					$me_un = MD::app()->user->getUsername();
					$me_nick = MD::app()->user->getNickname();
					$content = "${me_nick}关注你啦，快去<a href='/user/un/${me_un}' target='_blank'>打声招呼</a>吧<br/>";
					$ps = ProductModel::model()->getUserProduct(MD::app()->user->getId(),'small',5);
					foreach ($ps as $p) {
						$content .= '<img src="'.MD::app()->urlmanager->getImageAvatarLink($p->image_avatar).'"/>';
					}
					if ($content_id = DashboardContentModel::model()->addDashboardContent(array('content'=>$content))) {
						DashboardModel::model()->addDash(array('from_uid'=>$uid, 'to_uid'=>$optObj->uid, 'content_id'=>$content_id, 'tab'=>'sys'));
					}
					//增加关系的model已经有了
					return $this->returnInfo(array('result' => '1'));
				}else{
					return $this->returnError("user/follow.post", "db fail", IApi::ERROR_MYSQL);
				}
			} else {
				return $this->returnError("user/follow.post", "db fail", IApi::ERROR_MYSQL);
			}
		} else {
			return $this->returnError("user/follow.post", "auth fail", IApi::ERROR_AUTH_FAIL);
		}
	}
	
	/**
	 * 取消关注某人
	 * *
	 */
	public function actionPostUnfollow() {
		return;//禁止
		if ($this->verifyUser() && $this->checkLogin()) {
			$optObj = $this->parseParam(array(
				'un' => MD::app()->user->getUsername(),
			));
			if ($optObj === false) {
				return $this->returnError("user/unfollow.post", "param error", IApi::ERROR_NO_PARAM);
			}
			$un = $optObj->un;
			$uid = MD::app()->user->getId();
			$result = UserFollowModel::model()->find(new Criteria(array(
				'where' => array('whom' => $optObj->uid,'who' => $uid),
				'limit' => 1
			)));
			if($result){
				$sqlArray = array('where'=>array('whom' => $optObj->uid, 'who' => $uid));
				$result = UserFollowModel::model()->delete(new Criteria($sqlArray));
				if($result){
					$sqlArray = array(
						'set' => 'num_followers=num_followers-1',
						'where' => array('id' =>  $uid),
					);
					UserModel::model()->update(new Criteria($sqlArray), false);//保证只更新一条记录
					
					//--->所关注用户的粉丝数
					$sqlArray = array(
							'set' => 'num_fans=num_fans-1',
							'where' => array('id' =>  $optObj->uid),
						);
					UserModel::model()->update(new Criteria($sqlArray), false);					
					return $this->returnInfo(array('result' => '1'));
				}else{
					return $this->returnError("user/unfollow.post", "db fail", IApi::ERROR_MYSQL);
				}
			} else {
				return $this->returnError("user/unfollow.post", "db fail", IApi::ERROR_MYSQL);
			}
		} else {
			return $this->returnError("user/unfollow.post", "auth fail", IApi::ERROR_AUTH_FAIL);
		}
	}
		
}
