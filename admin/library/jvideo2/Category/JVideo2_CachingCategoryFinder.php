<?php
require_once dirname(__FILE__) . '/JVideo2_ICategoryFinder.php';

class JVideo2_CachingCategoryFinder implements JVideo2_ICategoryFinder
{
	/**
	 * @var JVideo_ICategoryFinder
	 */
	private $categoryFinder;
	
	public function __construct(JVideo2_ICategoryFinder $categoryFinder)
	{
		$this->categoryFinder = $categoryFinder;
	}
	
	public function getCategory($id)
	{
		$cache = JFactory::getCache('jvideo');
		$cacheId = 'category_finder_getCategory_' . $id;
		
		return $cache->get(array($this->categoryFinder, 'getCategory'), array($id), $cacheId);
	}
}