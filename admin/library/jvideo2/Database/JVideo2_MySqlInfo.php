<?php
class JVideo2_MySqlInfo
{
    public function getVersion()
    {
        $db = JFactory::getDBO();

        $query = "SELECT VERSION() as mysql_version";
        $db->setQuery($query);

        $mysqlVersion = $db->loadResult();

        list($version) = explode('-', $mysqlVersion);
		
        return $version;
    }
}