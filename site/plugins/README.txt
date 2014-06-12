JVideo Plugin System

by Matt Beckman
Infinovation, LLC
www.infinovation.com


OVERVIEW

Jvideo plugin system includes:
- Profile system plugins
- Comment system plugins
- Video system plugins

INTRODUCTION

The plugin system allows developers to use a variety of different components
and modules with JVideo. For example, JVideo includes a user profile system 
that displays videos and information on each user. However, the website owner
may already use another system (i.e., Community Builder) to maintain community 
profiles. So, we have a plugin called 'profile' that has been abstracted in the
JVideo component so that if you want to use Community Builder as your profile 
system, all links and references to a user profile are directed there instead.

The plugin system also includes a "Custom Mapping" plugin that allows "lazy" 
admins to tie simple profile systems to JVideo via the Joomla! control panel.
The custom mapping only requires the database table name, a few database table 
columns, and an avatar prefix.


HOW TO ROLL YOUR OWN

1. Copy the PHP files from the "default" directory to a new directory:

Example:
	/plugins/default/profile.php
	-to-
	/plugins/my_profile_system/profile.php



2. Update any SQL queries and/or methods that you need to customize.

Example:
	return JRoute::_("index.php?option=com_jvideo&view=user&user_id=" . $userID);
	-to-
	return JRoute::_("index.php?option=com_myprofilesys&task=showProfile&id=" . $userID);



3. Select your custom plugin from the control panel. You can pick & choose 
between all the installed plugins, so you don't have to use a specific one for
everything.

Example:
	Use Community Builder plugin for the Profile system, !JoomlaComment plugin 
	for the Comments system, and the default JVideo plugin for the video system.



QUESTIONS / COMMENTS / SUPPORT

Contact us if you have any questions at all. We hope we developed this in a way
that makes it easy for everybody to use & extend.

JVideo Joomla! Component
http://jvideo.infinovation.com

Brought to you by Infinovation LLC
http://www.infinovation.com
