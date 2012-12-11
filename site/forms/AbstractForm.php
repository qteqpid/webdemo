<?php
/**
 * 表单处理基类，表单中的每一条数据都是该类对象的一个属性
 * @author instreet
 *
 */
abstract class AbstractForm extends AbstractComponent{
		
	public $errorMessage;//错误信息，检查表单失败时候，给此变量赋值
	
	/**
	 * 验证表单的有效性
	 */
	abstract public function validate();
	
	/**
	 * 返回表单对象的属性数据
	 * 将表单对象传递到前端显示时候调用
	 * @param string $name
	 */
	public function __get($name){
		if(property_exists($this,$name))return $this->$name;
		return $this->getDefaultValue($name);
	}
	
	/**
	 * 当表单对象的属性不存在的时候，调用该函数
	 * 以返回一个默认值
	 * @param string $name
	 */
	protected function getDefaultValue($name){
		return null;
	}
	
	/**
	 * 当表单对象中的数据验证完毕以后，
	 * 由model层对象调用该函数获取数据库中
	 * 对应字段的值
	 */
	public function getFormData(){
		return null;
	}
	
}