<?php
/**
 * 验证类的接口
 * @author qteqpid
 */
interface IIdentity {
	/**
	 * 验证函数,对实现该接口的类的对象进行验证
	 * 验证成功返回TRUE,并把引用参数$status赋值，作为验证的成功获得的数据
	 * 验证失败返回FALSE,对传递的$status不做任何修改
	 */
	public function validate(& $status);
}
