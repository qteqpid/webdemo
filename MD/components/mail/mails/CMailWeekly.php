<?php
/**
 * Imeiding每周精选邮件
 * @author qteqpid
 */
class CMailWeekly extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, activeLink
	 */
	
	protected $products;
	
	protected $stars;
	
    protected $tpl = 'mail.weekly.tpl';
    
	private $goods_template = "<li style=\"float:left;width:168px;margin:0 1px 10px 0;list-style:none;\">
                	<a href=\"{product_url}\" style=\"text-decoration:none;color:#878787;\" target=\"_blank\">
                    	<p style=\"margin:0;\"><img width=\"168\" height=\"168\" border=\"0\" src=\"{product_src}\" /></p>
                        <p style=\"width:100%;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;-o-text-overflow:ellipsis;line-height:33px;\">来自：{product_author}</p>
                    </a>
                    </li>";
	
	private $stars_template = "<li style=\"float:left;width:168px;height:300px;margin-right:1px;list-style:none;background:#f6f6f6;overflow:hidden;\">
                	<a href=\"{userlink}\" style=\"text-decoration:none;color:#878787;\" target=\"_blank\">
                    	<p style=\"margin:0;\"><img width=\"168\" height=\"168\" border=\"0\" src=\"{avatar}\" /></p>
                        <p style=\"margin:8px 10px 4px;font-size:20px;color:#666;line-height:23px;font-family:Microsoft yahei;\">{nickname}</p>
                        <p style=\"margin:0 10px;line-height:19px;\">{introduction}</p>
                    </a>
                    </li>";
	
	public function init() {
		$this->checkValid(array('products', 'stars'));
	}
	
	public function getBody() {
		if (!empty($this->body)) return $this->body;
		$goods_content = "";
		foreach($this->products as $p) { // 8个商品
			$goods_content .= str_replace(
							array('{product_url}','{product_src}','{product_author}'), 
							array($p->product_url, $p->product_src, $p->product_author), 
							$this->goods_template);
		}
		$stars_content = "";
		foreach($this->stars as $s) { // 4个达人
			$stars_content .= str_replace(
							array('{userlink}','{avatar}','{nickname}','{introduction}'), 
							array($s->userlink, $s->avatar, $s->nickname, $s->introduction),
							$this->stars_template);
		}
		$this->tpl =  str_replace('{goods_content}',$goods_content,$this->tpl);
		$this->tpl =  str_replace('{stars_content}',$stars_content,$this->tpl);	
		return $this->tpl;				
	}
	
	public function getSubject() {
		return '相同喜好的TA，才更懂你想要的!【Imeiding每周精选】';
	}
}
