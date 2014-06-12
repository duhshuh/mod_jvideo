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

class JVideo_S3PathConverter
{
	public function toUrl($s3Path)
	{
		$parts = explode('/', $s3Path, 2);

		if (strcasecmp($parts[0], "infinovision") == 0)
		{
			return 'http://files.infinovision.com/' . urlencode($parts[1]);
		}
		elseif (strcasecmp($parts[0], "infinovision-logos") == 0)
		{
			return 'http://logos.infinovision.com/' . urlencode($parts[1]);
		}
		elseif (strcasecmp($parts[0], "infin-video-437be3bc6b40048e7ecd808acbfb5ad879e521d8") == 0)
		{
			return 'http://files1.infinovision.com/' . urlencode($parts[1]);
		}
		elseif (strcasecmp($parts[0], "infin-webcam-437be3bc6b40048e7ecd808acbfb5ad879e521d8") == 0)
		{
			return 'http://files2.infinovision.com/' . urlencode($parts[1]);
		}
		else
		{
			return 'http://' . $parts[0] . '.s3.amazonaws.com/' . urlencode($parts[1]);
		}
	}
}