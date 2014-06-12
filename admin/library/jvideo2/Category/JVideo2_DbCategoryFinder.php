<?php
require_once dirname(__FILE__) . '/JVideo2_ICategoryFinder.php';

require_once JPATH_ADMINISTRATOR.'/components/com_jvideo/import.php';
jvimport('CategoryFactory');

class JVideo2_DbCategoryFinder implements JVideo2_ICategoryFinder
{
	
	public function getCategory($id)
	{
		$rawCategoryData = $this->getRawCategoryData($id);
		$category = JVideo_CategoryFactory::create($rawCategoryData);
		return $category;
	}
	
	private function getRawCategoryData($id)
	{
		$sql = 'select * from #__jvideo_categories where id = ' . intval($id);
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		
		return $db->loadObject();
	}
}