<?php
/*
 *    @package    JVideo
 *    @subpackage Components
 *    @link http://jvideo.warphd.com
 *    @copyright (C) 2007 - 2010 Warp
 *    @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

jvimport('CategoryTreeRepositoryFactory');
jvimport('CategoryTreeIterator');
jvimport('VideoCategoryRepositoryFactory');
jvimport('Services.CategoryTreeService');

class JVideoModelCategories extends JModelLegacy
{
	private $config;

	public function __construct()
	{
		$this->config = JVideo_Factory::getConfig();

		parent::__construct();
	}

    public function getCategories()
	{
		$repository = JVideo_CategoryTreeRepositoryFactory::create();
        
        return $repository->getCategoryTree();
	}

	public function verifyAccountExists()
	{
		return $this->config->infinoAccountKey != "";
	}

	public function getCategoryById($categoryId)
	{
		$repository = JVideo_CategoryTreeRepositoryFactory::create();

		return $repository->getCategoryById($categoryId);
	}

	public function getParentCategoryIdByCategoryId($categoryId)
	{
		$repository = JVideo_CategoryTreeRepositoryFactory::create();

		$category = $repository->getParentCategoryById($categoryId);

		if (!is_null($category)) {
			return $category->id;
		} else {
			return null;
		}
	}

	public function getCategoriesByVideoId($videoId)
	{
		$repository = JVideo_VideoCategoryRepositoryFactory::create();

		return $repository->getVideoCategoriesByVideoId($videoId);
	}

    public function getCategoriesByVideoGuid($videoGuid)
    {
        $repository = JVideo_VideoCategoryRepositoryFactory::create();

		return $repository->getVideoCategoriesByVideoGuid($videoGuid);
    }

    public function addVideoCategory($videoId, $categoryId)
    {
        $videoRepository = JVideo_VideoRepositoryFactory::create();
        $video = $videoRepository->getVideoById($videoId);

        $videoCategory = new JVideo_VideoCategory();
        $videoCategory->videoId = $videoId;
        $videoCategory->categoryId = $categoryId;
        $videoCategory->videoGuid = $video->infinoVideoID;

        $repository = JVideo_VideoCategoryRepositoryFactory::create();

        return $repository->add($videoCategory);
    }

    public function removeVideoCategory($videoId, $categoryId)
    {
        $repository = JVideo_VideoCategoryRepositoryFactory::create();

        $videoCategories = $repository->getVideoCategoriesByVideoId($videoId);

        foreach ($videoCategories as $videoCategory) {
            if ($videoCategory->categoryId == $categoryId) {
                return $repository->remove($videoCategory);
            }
        }
    }

    public function addVideoCategoryByVideoGuid($videoGuid, $categoryId)
    {
        $repository = JVideo_VideoRepositoryFactory::create();

        return $this->addVideoCategory(
                    $repository->getVideoIdByVideoGuid($videoGuid),
                    $categoryId);
    }

    public function removeVideoCategoryByVideoGuid($videoGuid, $categoryId)
    {
        $repository = JVideo_VideoRepositoryFactory::create();

        return $this->removeVideoCategory(
                    $repository->getVideoIdByVideoGuid($videoGuid),
                    $categoryId);
    }

    public function getNestedCategoryArray($categoryId)
    {
        $category = new JVideo_Category();
        $categoryTree = $this->getCategories();

        $iterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach($iterator as $categoryTreeNode)
        {
            if ($categoryTreeNode->id == $categoryId)
            {
                $category = $categoryTreeNode;
                break;
            }
        }

        return $categoryTree->getNestedCategoryIds($category);
    }

    public function addCategory($name, $parentCategoryId)
    {
        $repository = JVideo_CategoryTreeRepositoryFactory::create();

        $parentCategory = $repository->getCategoryById($parentCategoryId);

		$category = new JVideo_Category();
		$category->name = $name;
        $category->active = 1;

        $service = new JVideo_CategoryTreeService();

        return $service->addCategory($category, $parentCategory);
    }

    public function editCategory($categoryId, $name, $active, $parentCategoryId)
    {
        $service = new JVideo_CategoryTreeService();
        
        return $service->editCategory($categoryId, $name, $active, $parentCategoryId);
    }

    public function removeCategory($categoryIds)
    {
        $service = new JVideo_CategoryTreeService();
        $collection = new JVideo_CategoryCollection();

        foreach ($categoryIds as $categoryId) {
            $category = new JVideo_Category();
            $category->id = $categoryId;

            $collection->add($category);
        }

        return $service->removeCategories($collection);
    }

    public function getRootCategories()
    {
        $categoryTree = $this->getCategories();

        return $categoryTree->getRootCategories();
    }

    public function getChildCategoriesByCategoryId($categoryId)
    {
        $repository = JVideo_CategoryTreeRepositoryFactory::create();
        $categoryTree = $this->getCategories();
        
        $category = $repository->getCategoryById($categoryId);

        return $categoryTree->getChildCategories($category);
    }
}

?>
