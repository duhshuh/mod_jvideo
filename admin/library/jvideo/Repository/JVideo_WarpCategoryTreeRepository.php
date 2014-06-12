<?php
/*
 *    @package    JVideo
 *    @subpackage Library
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

require_once(dirname(__FILE__) . '/../../infin-lib.php');
require_once(dirname(__FILE__) . '/../JVideo_Category.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryCollection.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryFactory.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryTree.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryTreeXmlSerializer.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');
require_once(dirname(__FILE__) . '/../JVideo_ICategoryTreeRepository.php');
require_once(dirname(__FILE__) . '/../JVideo_RemoteService.php');

class JVideo_WarpCategoryTreeRepository extends JVideo_RemoteService implements JVideo_ICategoryTreeRepository
{
    public function __construct()
    {
        parent::__construct();

        $this->addRemoteService(
            new InfinovationVideo(
                $this->config->infinoAccountKey,
                $this->config->infinoSecretKey));
    }

	public function update(JVideo_CategoryTree $categoryTree)
	{
		$serializer = new JVideo_CategoryTreeXmlSerializer();

        $xmlCategoryTree = $serializer->serialize($categoryTree);

        $warpCategories = $this->remoteService->updateCategories($xmlCategoryTree);

        return $serializer->unserialize($warpCategories);
	}
    
    public function getCategoryTree()
    {
        $serializer = new JVideo_CategoryTreeXmlSerializer();
        
        $xml = $this->remoteService->getCategories();

        return $serializer->unserialize($xml);
    }

    public function getCategoryById($categoryId)
    {
        $categoryTree = $this->getCategoryTree();

        $categoryTreeIterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($categoryTreeIterator as $category) {
            if ($category->id == $categoryId)
                return $category;
        }
    }

    public function getParentCategoryById($categoryId)
    {
        $categoryTree = $this->getCategoryTree();

        $categoryTreeIterator = new JVideo_CategoryTreeIterator($categoryTree);

        foreach ($categoryTreeIterator as $category) {
            if ($category->id == $categoryId)
                break;
        }

        $categoryTreeIterator->rewind();

        foreach ($categoryTreeIterator as $parentCategory) {
            if ($parentCategory->isDirectParentOf($category))
                return $parentCategory;
        }
    }
}
