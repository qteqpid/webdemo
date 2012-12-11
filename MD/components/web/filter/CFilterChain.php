<?php

class CFilterChain extends AbstractComponent {
	
	/**
	 * 创建filter链并返回第一个filter
	 * @param AbstractAction $action
	 * @param array $filter
	 * @return AbstractFilter or NULL
	 */
	public static function createChain($action, $filters) {
		$firstFilter = NULL;
		$curFilter = NULL;
		foreach ($filters as $filter) {
			if (!is_array($filter) || !isset($filter['class']) || !class_exists($filter['class'])) {
				continue; // filter初始化失败
				//throw new CException('');
			}
			$clazz = $filter['class'];
			$params = array();
			isset($filter['rules']) and $params = $filter['rules'];
			$filterObject = new $clazz($params, $action);
			if (!($filterObject instanceof AbstractFilter)) continue;
			
			if ($firstFilter == NULL) {
				$firstFilter = $curFilter = $filterObject;
			} else {
				$curFilter->setSuccessor($filterObject);
				$curFilter = $filterObject;
			}
		}
		return $firstFilter;
	}
}
