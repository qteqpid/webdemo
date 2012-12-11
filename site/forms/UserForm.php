<?php
/**
 * UserForm类，用于处理注册时候：用户提交的表单，
 * 表单信息如下：
 * $nickname
 * $password
 * $email
 * 
 * //非提交表单信息，生成的信息
 * $username
 * @author instreet
 *
 */
class UserForm extends AbstractForm{
	
	//protected  $mail;
	protected  $passwd;
	protected  $name;
	
	public function init(){
	}
	
	public function validate(){
		//验证昵称长度, 密码长度，邮箱格式
		foreach ($this as $key => $value){
			switch ($key){
				case 'mail' :
					$result = $this->checkMail();
					break;
				case 'name' :
					$result = $this->checkName();
					break;
				case 'passwd':
					$result = $this->checkPasswd();
					break;
				default:
					$result = true;
			}
			if(!$result)return false;
		}
		return true;
	}
	
	/**
	 *  检查邮件的合法性
	 */
	protected function checkMail(){
		$result = preg_match("/^[0-9a-zA-Z_\.-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/",$this->mail);
		if(!$result){
			$this->errorMessage = "邮箱格式不正确";
			return false;
		}
		return true;
	}
	/**
	 * 检查用户昵称的合法性
	 */
	protected function checkName(){
		$length = strlen($this->name);
		if($length > 16){
			$this->errorMessage = "昵称不能超过16个字符";
			return false;
		} 
		if($length < 1){
			$this->errorMessage = "昵称不能为空";
			return false;
		}
		return true;
	}
	/**
	 * 检查密码的合法性
	 */
	protected function checkPasswd(){
		$length = strlen($this->passwd);
		if($length > 32){ 
			$this->errorMessage = "密码不能超过32个字符";
			return false;
		}
		if($length < 6){
			$this->errorMessage = "密码不能少于6个字符";
			return false;
		} 
		return true;
	}
	
	public function getFormData(){
		//数据库需要的字段的值
		$data = array();
		foreach ($this as $key => $value){
			switch ($key){
				case 'mail' :
					$data['email'] = $value;
					break;
				case 'name' :
					$data['username'] = md_escape_string(htmlspecialchars($value));
					break;
				case 'passwd':
					$data['password'] = md5($value);
					break;
			}
		}
		return $data;
	}
}
