# Featured tool #

Featured tool is a Moodle block plugin that allows admins to easily display information/announcements to teachers.

The featured tool can be configured to cycle between multiple sets of information. Admins can enter a subtitle, thumbnail, and the content to display to the users when they click on a button. There are currently three tools to hold this information, and the featured tool will decide which to show randomly any time the dashboard is loaded. This information in the featured tool is configured only to show to users with a teacher role on the site.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/featured_tool

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Configuration ##

In order to set up the featured tool to show on any teacher's dashboard, you can set up the tool in the Default Dashboard and set it for all users.

The configuration to make sure every user is seeing the same instance of the block is:

1. Original block location: System
2. Display on page types: Dashboard page
3. Select pages: Any page matching the above

## License ##

2023 Derek Wilson <wilsondc5@appstate.edu>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
