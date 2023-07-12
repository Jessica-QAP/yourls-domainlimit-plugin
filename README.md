domainlimit-yourls-plugin
=========================

This plugin for [YOURLS](https://github.com/YOURLS/YOURLS) limits the creation of shorturls to a list of defined domains. Limiting the domains allowed for shortlinks helps prevent the service from being misued.

Configuration
------------
1. Install the plugin in user/plugins in a folder called domainlimit
2. Define an array of allowed domains in user/config called domainlimit_list. For example, `$domainlimit_list = array( 'mydomain.com', 'otherdomain.com' );`
3. Optionally defined a list of usernames that are exempt from this restriction. For example, `domainlimit_exempt_users = array( 'bobadmin' );`
4. Activate the plugin with the plugin manager in the admin interface.

License
-------
Copyright 2013 Nicholas Waller (code@nicwaller.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

<sub>Based on plugin by nicwaller</sub>
