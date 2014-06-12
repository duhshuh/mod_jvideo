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
require_once(dirname(__FILE__) . '/../JVideo_Exception.php');

class JVideo_SyncServiceManager
{
    private $services = array();
    private $force = false;

    public function __construct($force = false)
    {
        $this->force = $force;
    }

    public function add($service)
    {
        $this->services[] = $service;
    }

    public function sync()
    {
        $result = "";
        
        foreach ($this->services as $service) {
            $result .= $service->sync($this->force);
        }

        return $result;
    }
}