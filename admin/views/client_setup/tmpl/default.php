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
?>
<script language="javascript" src="<?php echo JURI::root(true); ?>/media/com_jvideo/admin/js/form_controls.js"></script>
<form action="index.php" onSubmit="return jvideo_verify_admin_client_form();" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="option" value="com_jvideo" />
    <input type="hidden" name="view" value="client_setup" />
    <input type="hidden" name="task" value="do_client_setup" />
    <?php
    if ($this->success == 'true') {
        echo "<h2 style=\"text-size: medium; color: #0a0;\">" . JText::_("JV_SAVED") . "</h2>";
    }

    if ($this->user != "") {
        echo "<p>" . JText::_("JV_ALREADY_SETUP") . "</p>";
    }
    ?>

    <table border="0" cellpadding="5" >
        <tr>
            <td>
                <strong><?php echo JText::_("JV_USERNAME"); ?></strong>
            </td>
            <td>
                <input type="text" name="user_name" maxlength="25" value="<?PHP echo $this->user; ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <strong><?php echo JText::_("JV_PASSWORD"); ?></strong>
            </td>
            <td>
                <input type="password" name="pass" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td>
                <strong><?php echo JText::_("JV_CONFIRM_PASSWORD"); ?></strong>
            </td>
            <td>
                <input type="password" name="passConfirm" maxlength="25" />
            </td>
        </tr>
        <tr>
            <td>
                <strong><?php echo JText::_("JV_DOMAIN"); ?></strong>
            </td>
            <td>
                <input type="text" name="infindomain" maxlength="100" value="<?PHP echo $this->infin_domain; ?>" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="Setup Account" /></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo JText::_("JV_NO_WARP_ACCOUNT"); ?>
                <a href="http://www.warphd.com/signup" target="_blank"><?php echo JText::_("JV_GET_ONE_HERE"); ?></a>
            </td>
        </tr>
    </table>
</form>