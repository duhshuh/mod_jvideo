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
defined('_JEXEC') or die("Cannot use outside of Joomla");

require_once(dirname(__FILE__) . '/../../infin-lib.php');
require_once(dirname(__FILE__) . '/../JVideo_CategoryTree.php');
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');
require_once(dirname(__FILE__) . '/../JVideo_ISyncService.php');
require_once(dirname(__FILE__) . '/../Repository/JVideo_JoomlaCategoryTreeRepository.php');
require_once(dirname(__FILE__) . '/../Repository/JVideo_WarpCategoryTreeRepository.php');

class JVideo_JoomlaCategorySyncService implements JVideo_ISyncService
{
    private $localRepository = null;
    private $remoteRepository = null;

    public function __construct()
    {
        $this->localRepository = new JVideo_JoomlaCategoryTreeRepository();
        $this->remoteRepository = new JVideo_WarpCategoryTreeRepository();
    }

    public function sync($force = false)
    {
        $remoteCategoryTree = $this->getRemoteCategoryTree();
        
        $this->updateLocalCategoryTree($remoteCategoryTree);

        return "<p>Categories synchronized (" . $remoteCategoryTree->count() . " total)</p>";
    }

    private function getRemoteCategoryTree()
    {
        return $this->remoteRepository->getCategoryTree();
    }

    private function updateLocalCategoryTree(JVideo_CategoryTree $categoryTree)
    {
        return $this->localRepository->update($categoryTree);
    }
}