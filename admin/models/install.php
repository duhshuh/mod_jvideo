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
jvimport('Factory');
jvimport('ConfigFactory');
jvimport('Services.CategoryTreeService');
jvimport('VideoCategoryRepositoryFactory');
jvimport2('Database.DbBatch');
jvimport2('ServiceLocator');

class JVideoModelInstall extends JModelLegacy
{
    private $_stepTasks;

    public function doStepTasks($step)
    {
        $this->_stepTasks = array();

        switch ($step)
        {
            case "step1":
                $this->doStep1();
                break;
            case "step2":
                $this->doStep2();
                break;
            case "step3":
                $this->doStep3();
                break;
            case "step4":
                $this->doStep4();
                break;
            case "step5":
                $this->doStep5();
                break;
            default:
                break;
        }

        return $this->_stepTasks;
    }

    private function doStep1()
    {
		$serviceLocator = JVideo2_ServiceLocator::getInstance();
		$requirementsChecker = $serviceLocator->getRequirementsChecker();
		$this->_stepTasks = $requirementsChecker->getRequirementsStatus();
    }

    private function doStep2()
    {
        if ($this->isFreshInstall()) {
            $this->createDatabaseTables();
            $this->createDatabaseIndexes();
            $this->insertDefaultData();
            $this->skipToStep5();
            $this->saveInstallStatus('step5');
        } else {
            if ($this->isAccountSetup()) {
                $this->upgradeDatabaseTables();
                $this->upgradeDatabaseIndexes();
                $this->saveInstallStatus('step3');
            } else {
                $this->skipToStep5();
                $this->saveInstallStatus('step5');
            }
        }
    }

    private function doStep3()
    {
        $this->upgradeCategories();
        $this->saveInstallStatus('step4');
    }

    private function doStep4()
    {
        $this->upgradeVideoCategories();
        $this->saveInstallStatus('step5');
    }

    private function doStep5()
    {
        $this->cleanupDatabaseTables();
        $this->updateVersionBasedOnManifest();
        $this->_stepTasks['Finalize installation'] = "OK";
        $this->saveInstallStatus('complete');
    }

    private function saveInstallStatus($step)
    {
        JVideo_ConfigFactory::destroy(); // unset config in the event we added columns
        $config = JVideo_ConfigFactory::create();

        $config->installStatus = $step;

        $repository = JVideo_ConfigRepositoryFactory::create();
        $repository->update($config);
    }

    private function isFreshInstall()
    {
        $db = JFactory::getDBO();

        $tableList = $db->getTableList();

        foreach ($tableList as $table) {
            if (false !== stristr($table, 'jvideo_config')) {
                return false;
            }
        }

        return true;
    }

    private function isAccountSetup()
    {
        try
        {
            $config = JVideo_Factory::getConfig();

            return $config->infinoAccountKey != "";
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    private function isLegacyVersion($version)
    {
        return strcmp($version, '0.5.3') == -1;
    }

    private function createDatabaseTables()
    {
        $db = JFactory::getDBO();

        if ($db->getUTFSupport()) {
            $sqlDump = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.utf8.sql');
        } else {
            $sqlDump = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.nonutf8.sql');
        }

        $this->executeStepBatch($db, $sqlDump, 'Add tables to database', 'Error occurred while adding tables');
    }

    private function createDatabaseIndexes()
    {
        $db = JFactory::getDBO();

        $sqlDump = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.indexes.sql');

        $this->executeStepBatch($db, $sqlDump, 'Add indexes to tables', 'Error occurred while adding indexes');
    }

    private function upgradeDatabaseTables()
    {
        try
        {
            $db = JFactory::getDBO();
            $config = JVideo_Factory::getConfig();

            $oldVersion = $config->version;
            $newVersion = $this->getVersionFromManifest();

            if ($oldVersion == $newVersion) {
                $this->_stepTasks['Upgrade tables in database'] = "OK";
                return;
            }

            if ($this->isLegacyVersion($oldVersion)) {
                $this->prepLegacyDatabaseTablesForUpgrade();
                $oldVersion = '0.5.3';
            }

            $sqlDiffFile = JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/upgrade/diff/'.$oldVersion.'.sql';
                
            if (file_exists($sqlDiffFile)) {
                $sqlDiff = file_get_contents($sqlDiffFile);

                $this->executeStepBatch($db, $sqlDiff, 'Upgrade tables in database', 'Database tables already upgraded');
            } else {
                $this->_stepTasks['Upgrade tables in database'] = "OK";
            }
        }
        catch (Exception $ex)
        {
            $this->_stepTasks['Upgrade tables in database'] = $ex;
        }
    }

    private function upgradeDatabaseIndexes()
    {
        $db = JFactory::getDBO();
        $tableList = $db->getTableList();

        try
        {
            if (file_exists(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.indexes.sql'))
            {
                foreach ($tableList as $table)
                {
                    if (false === stristr($table, 'jvideo')) {
                        continue;
                    }

                    $sql = "SELECT DATABASE();";
                    $db->setQuery($sql);
                    $schema = $db->loadResult();

                    $sql = "SELECT DISTINCT INDEX_NAME FROM information_schema.statistics "
                        ."WHERE table_schema = " . $db->quote($schema) . " "
                        ."AND table_name = " . $db->quote($table) . " "
                        ."AND INDEX_NAME <> 'PRIMARY' "
                        ."AND NON_UNIQUE <> 0;";
                    $db->setQuery($sql);
                    $oldIndexes = $db->loadAssocList();

                    if (is_array($oldIndexes))
                    {
                        foreach ($oldIndexes as $oldIndex)
                        {
                            if (!is_array($oldIndex)) {
                                $oldIndex = array($oldIndex);
                            }

                            foreach ($oldIndex as $index) {
                                $sql = "ALTER TABLE " . $table . " DROP INDEX " . $index . ";";
                                $db->setQuery($sql);
                                $db->execute();
                            }
                        }
                    }
                }

                $addIndexesBatch = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.indexes.sql');
                $db->setQuery($addIndexesBatch);

                $this->_stepTasks['Upgrade table indexes'] = "OK";
            }
            else
            {
                $this->_stepTasks['Upgrade table indexes'] = "Skipped";
            }
        }
        catch (Exception $ex)
        {
            $this->_stepTasks['Upgrade table indexes'] = $ex;
        }
    }

    private function prepLegacyDatabaseTablesForUpgrade()
    {
        //
        // Migration function for legacy database tables. Schema is brought up to
        // version 0.5.3 database schema, and then updated via standard upgrade procedures.
        //
        try
        {
            $db = JFactory::getDBO();

            $tables = array(
                      "#__jvideos"
                    , "#__jvideos_categories"
                    , "#__jvideo_config"
                    , "#__jvideo_categories"
                    , "#__jvideo_featured"
                    , "#__jvideo_ranking"
                    , "#__jvideo_rating"
                    , "#__jvideo_thumbnails"
                    , "#__jvideo_users"
                    );

            // Drop existing tmp tables (if any)
            foreach ($tables as $table)
            {
                $table = str_replace("#__jvideo", "#__tmp_jvideo", $table);
                $sql = "DROP TABLE IF EXISTS `" . $table . "`;";
                $db->setQuery($sql);
                $db->execute();
            }

            $sqlDump = "";

            if ($db->getUTFSupport()) {
                $sqlDump = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/upgrade/legacy/legacy.mysql.utf8.sql');
            } else {
                $sqlDump = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/upgrade/legacy/legacy.mysql.nonutf8.sql');
            }

            // Create new tables with new tmp names
            $sqlDump = str_replace("#__jvideo", "#__tmp_jvideo", $sqlDump);
            try
            {
                JVideo2_DbBatch::execute($db, $sqlDump);
            }
            catch (DbBatchException $e) { }

            // Loop through columns on all tables and find changes
            foreach ($tables as $table)
            {
                $addedColumns = array();

                $sql = "SHOW COLUMNS FROM " . $table;
                $db->setQuery($sql);
                $oldCols = $db->loadAssocList();

                $sql = "SHOW COLUMNS FROM " . str_replace("#__jvideo", "#__tmp_jvideo", $table);
                $db->setQuery($sql);
                $newCols = $db->loadAssocList();

                $prevCol = "";

                foreach ($newCols as $newCol)
                {
                    $found = false;
                    $sql = "";
                    reset($oldCols);

                    foreach ($oldCols as $oldCol)
                    {
                        $sql = "";

                        if ($newCol['Field'] == $oldCol['Field'])
                        {
                            $found = true;

                            $diff = array_diff($oldCol, $newCol);

                            if (count($diff) > 0)
                            {
                                // ALTER TABLE #__jvideos MODIFY COLUMN colName colDefinition
                                $sql = "ALTER TABLE " . $table . " MODIFY COLUMN ". $newCol['Field'] ." "
                                    . $newCol['Type'] ." "
                                    . ($newCol['Null'] == "NO" ? "NOT NULL" : "NULL") ." "
                                    . ($newCol['Default'] != "" ? "DEFAULT '" . $newCol['Default'] ."'" : "") ." "
                                    . $newCol['Extra'];
                                $db->setQuery($sql);
                                $db->execute();
                            }
                        }
                    }

                    if (!$found)
                    {
                        // ALTER TABLE #__jvideos ADD COLUMN colName colDefinition
                        $sql = "ALTER TABLE " . $table . " ADD COLUMN ". $newCol['Field'] ." "
                            . $newCol['Type'] ." "
                            . ($newCol['Null'] == "NO" ? "NOT NULL" : "NULL") ." "
                            . ($newCol['Default'] != "" ? "DEFAULT '" . $newCol['Default'] ."'" : "") ." "
                            . $newCol['Extra'];

                        if ($prevCol != "")
                        {
                            $sql .= " AFTER " . $prevCol ." ";
                        }
                        else
                        {
                            $sql .= " FIRST ";
                        }

                        $db->setQuery($sql);
                        $db->execute();

                        $addedColumns[] = $newCol['Field'];
                    }

                    $prevCol = $newCol['Field'];
                }

                reset($oldCols);

                foreach ($oldCols as $oldCol)
                {
                    $found = false;
                    $sql = "";

                    foreach ($newCols as $newCol)
                    {
                        if ($oldCol['Field'] == $newCol['Field'])
                        {
                            $found = true;
                        }
                    }

                    if (!$found)
                    {
                        if (false === array_search($oldCol['Field'], $addedColumns))
                        {
                            // ALTER TABLE #__jvideos DROP COLUMN colName
                            $sql = "ALTER TABLE " . $table . " DROP COLUMN ". $oldCol['Field'];
                            $db->setQuery($sql);
                            $db->execute();
                        }
                    }
                }
            }

            // Drop tmp tables
            reset($tables);
            foreach ($tables as $table)
            {
                $table = str_replace("#__jvideo", "#__tmp_jvideo", $table);
                $sql = "DROP TABLE IF EXISTS `" . $table . "`;";
                $db->setQuery($sql);
                $db->execute();
            }

            $this->_stepTasks['Prepare legacy tables'] = 'OK';
        }
        catch (Exception $ex)
        {
            $this->_stepTasks['Prepare legacy tables'] = $ex;
        }
    }

    private function getVersionFromManifest()
    {
        $manifest = $this->loadManifestFromXml();
        $version = "0.0.0";

        if ($manifest && $manifest->version)
        {
            $version = $manifest->version;
        }

        return $version;
    }

    private function loadManifestFromXml()
    {
        $file = JPATH_ADMINISTRATOR.'/components/com_jvideo/jvideo.xml';
		$xml = JFactory::getXML($file);

		if (!$xml) {
			unset($xml);
			return null;
		}

		$type = $xml->attributes()->type;
		if ($type != 'install' && $type != 'mosinstall' && $type != 'extension' && $type != 'component') {
			unset($xml);
			return null;
		}

		return $xml;
    }

    private function upgradeCategories()
    {
        $localCategoryTree = $this->buildTreeFromLocalCategories();

        $this->_stepTasks['Prepare categories'] = 'OK';

        $remoteCategoryTree = $this->getCategoryTreeFromWarp();

        $ids = $this->mergeLocalTreeWithRemoteTree($localCategoryTree, $remoteCategoryTree);

        $this->insertIdsIntoTempUpgradeTable($ids);

        $this->_stepTasks['Upgrade categories'] = 'OK';
    }

    private function upgradeVideoCategories()
    {
        $batchSize = 10;
        $remaining = 0;

        if ($this->verifyLookupTableExists()) {
            $this->processVideoCategoryUpgradeBatch($batchSize);
            $remaining = $this->getRemainingVideoCategoriesInQueue();
        }

        if ($remaining > 0) {
            $this->_stepTasks['Upgrade video categories']
                = "Processed " . $batchSize . " items with " . $remaining . " remaining...";
            $this->_stepTasks['processing'] = true;
        } else {
            $this->_stepTasks['Upgrade video categories'] = "OK";
            $this->_stepTasks['processing'] = false;
        }
    }

    private function verifyLookupTableExists()
    {
        $db = JFactory::getDBO();
        $tableList = $db->getTableList();

        foreach ($tableList as $table)
            if (false !== stristr($table, 'jvideo_upgrade_categories_lookup'))
                return true;

        return false;
    }

    private function processVideoCategoryUpgradeBatch($batchSize)
    {        
        $db = JFactory::getDBO();
        $localRepository = new JVideo_JoomlaVideoCategoryRepository();
        $remoteRepository = new JVideo_WarpVideoCategoryRepository();

        $query = "SELECT * FROM #__jvideo_upgrade_categories_lookup";
        $db->setQuery($query);
        $categoryLookup = $db->loadObjectList();

        $processedCount = 0;
        
        foreach ($categoryLookup as $categoryPair)
        {
            if ($processedCount == $batchSize)
                return;

            $query = "SELECT v.id AS videoId, v.infin_vid_id AS videoGuid, vc.`category_id` "
                    ."FROM `#__jvideo_videos_categories` vc "
                    ."INNER JOIN `#__jvideo_videos` v ON v.id = vc.video_id "
                    ."WHERE vc.`category_id` = " . (int) $categoryPair->oldCategoryId . " "
                    ."AND v.status = 'complete'";
            $db->setQuery($query);
            $videoObjectList = $db->loadObjectList();

            foreach ($videoObjectList as $videoObject)
            {
                if ($processedCount == $batchSize)
                    return;

                $videoCategory = JVideo_VideoCategoryFactory::create(
                    $videoObject->videoId,
                    $categoryPair->newCategoryId, "", "", "", "",
                    $videoObject->videoGuid
                );

                $remoteRepository->add($videoCategory);

                $query = "UPDATE `#__jvideo_videos_categories` "
                        ."SET `category_id` = " . $categoryPair->newCategoryId . " "
                        ."WHERE `category_id` = " . $categoryPair->oldCategoryId . " "
                        ."AND `video_id` = " . $videoCategory->videoId;
                $db->setQuery($query);
                $db->execute();
                    
                $processedCount++;
            }

            $query = "DELETE FROM `#__jvideo_upgrade_categories_lookup` "
                    ."WHERE oldCategoryId = " . $categoryPair->oldCategoryId . " "
                    ."AND newCategoryId = " . $categoryPair->newCategoryId . " "
                    ."LIMIT 1";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private function getRemainingVideoCategoriesInQueue()
    {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) "
                ."FROM `#__jvideo_videos_categories` vc "
                ."INNER JOIN `#__jvideo_videos` v ON v.id = vc.video_id "
                ."WHERE v.status = 'complete' "
                ."AND vc.category_id IN ("
                ."      SELECT oldCategoryId FROM `#__jvideo_upgrade_categories_lookup`"
                .")";
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    private function buildTreeFromLocalCategories()
    {
        $db = JFactory::getDBO();

        $query = "SHOW FIELDS FROM #__jvideo_categories";
        $db->setQuery($query);

        $parentIdExists = false;

        foreach ($db->loadObjectList() as $field) {
            if ($field->Field == 'parent_id') {
                $parentIdExists = true;
                break;
            }
        }

        if ($parentIdExists) {
            $query = "SELECT * FROM #__jvideo_categories ORDER BY `parent_id` ASC";
            $db->setQuery($query);

            $localCategories = $db->loadObjectList();
        } else {
            return new JVideo_CategoryTree();
        }

        if ($db->getErrorNum()) {
            return new JVideo_CategoryTree();
        }
        
        if (count($localCategories) == 0) {
            return new JVideo_CategoryTree();
        }

        $categoryTree = new JVideo_CategoryTree();

        foreach ($localCategories as $localCategory)
        {
            $category = new JVideo_Category();
            $category->id = $localCategory->id;
            $category->name = is_null($localCategory->name) ? $localCategory->category_name : $localCategory->name;

            if ($localCategory->parent_id == "") {
                $categoryTree->addRoot($category);
            } else {
                $iterator = new JVideo_CategoryTreeIterator($categoryTree);

                foreach ($iterator as $node) {
                    if ($node->id == $localCategory->parent_id) {
                        $categoryTree->addChild($node, $category);
                    }
                }
            }
        }

        return $categoryTree;
    }

    private function getCategoryTreeFromWarp()
    {
        $repository = new JVideo_WarpCategoryTreeRepository();

        return $repository->getCategoryTree();
    }

    private function mergeLocalTreeWithRemoteTree($localCategoryTree, $remoteCategoryTree)
    {
        $remoteIterator = new JVideo_CategoryTreeIterator($remoteCategoryTree);
        $localIterator = new JVideo_CategoryTreeIterator($localCategoryTree);

        $idMap = array();
        $idMapItem = array(); // [0] = old cat ID; [1] = new cat ID; [2] = name for lookup
        
        foreach ($localIterator as $localCategory) {
            $category = new JVideo_Category();
            $category->name = $localCategory->name;

            $idMapItem[0] = $localCategory->id;
            $idMapItem[1] = null;

            $remoteIterator->rewind();

            if ($localCategoryTree->isRootElement($localCategory)) {
                $remoteCategory = $remoteCategoryTree->addRoot($category);
                $idMapItem[2] = $category->name;
            } else {
                $localParentCategory = $localCategoryTree->getDirectParentOf($localCategory);

                foreach ($remoteIterator as $remoteParentCategory) {
                    if ($remoteParentCategory->name == $localParentCategory->name) {
                        $remoteCategory = $remoteCategoryTree->addChild($remoteParentCategory, $category);
                        $idMapItem[2] = $category->name;
                        break;
                    }
                }
            }

            $idMap[] = $idMapItem;
        }

        $remoteRepository = new JVideo_WarpCategoryTreeRepository();
        $localRepository = new JVideo_JoomlaCategoryTreeRepository();
        $localCategoryTree = $remoteRepository->update($remoteCategoryTree);
        $localRepository->update($localCategoryTree);

        $lookupIterator = new JVideo_CategoryTreeIterator($localCategoryTree);

        foreach ($idMap as &$idMapItem) {
            $lookupIterator->rewind();
            foreach ($lookupIterator as $localCategory) {
                if ($localCategory->name == $idMapItem[2]) {
                    $idMapItem[1] = $localCategory->id;
                    $localCategory->name = '[nulled]';
                    break;
                }
            }
        }

        return $idMap;
    }   

    private function insertIdsIntoTempUpgradeTable($idMap)
    {
        if (count($idMap) == 0) {
            return;
        }

        $db = JFactory::getDBO();

        $query = "INSERT INTO `#__jvideo_upgrade_categories_lookup` (oldCategoryId, newCategoryId) "
                ."VALUES ";
        foreach ($idMap as $idPair) {
            if (!is_null($idPair[0]) && !is_null($idPair[1])) {
                $query .= "(" . $idPair[0] . ", " . $idPair[1] . "),";
            }
        }
        $query = substr($query, 0, -1);

        $db->setQuery($query);
        $db->execute();
    }

    private function updateVersionBasedOnManifest()
    {
        $db = JFactory::getDBO();

        $version = $this->getVersionFromManifest();

        if ($version != "0.0.0")
        {
            $sql = "UPDATE #__jvideo_config SET version = '" . $version . "' WHERE 1 = 1 LIMIT 1;";
            $db->setQuery($sql);
            $db->execute();
        }
    }

    private function insertDefaultData()
    {
        $db = JFactory::getDBO();

        $insertDefaultData = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/install/install.mysql.default-data.sql');

        if ($insertDefaultData != "")
        {
            $this->executeStepBatch($db, $insertDefaultData, 'Insert default data', 'Error inserting default data!');
        }

        return true;
    }

    private function skipToStep5()
    {
        $this->_stepTasks['Bypass upgrade path'] = 'OK';
        $this->_stepTasks['skip'] = "1";
    }

    private function cleanupDatabaseTables()
    {
        $db = JFactory::getDBO();
        $config = JVideo_Factory::getConfig();

        $query = "DELETE FROM `#__jvideo_videos` "
                ."WHERE status = 'deleted'";
        $db->setQuery($query);
        $db->execute();

        $oldVersion = $config->version;
        $newVersion = $this->getVersionFromManifest();

        if (($oldVersion != '0.0.0') && ($oldVersion == $newVersion)) {
            $this->_stepTasks['Cleanup database'] = "OK";
            return;
        }

        if ($this->isLegacyVersion($oldVersion)) {
            $oldVersion = '0.5.3';
        }
        
        $postSQLFile = JPATH_ADMINISTRATOR.'/components/com_jvideo/sql/upgrade/cleanup/'.$oldVersion.'.sql';
            
        if (file_exists($postSQLFile)) {
            $sqlDump = file_get_contents($postSQLFile);
            $this->executeStepBatch($db, $sqlDump, 'Cleanup database', 'OK');
        }
    }

    private function executeStepBatch($db, $sql, $label, $failureMessage)
    {
        try
        {
            JVideo2_DbBatch::execute($db, $sql);
            $this->_stepTasks[$label] = 'OK';
        }
        catch (DbBatchException $e)
        {
            $this->_stepTasks[$label] = $failureMessage;
        }
    }
}
