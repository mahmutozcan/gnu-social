<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Show information about a group
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  API
 * @package   StatusNet
 * @author    Zach Copley <zach@status.net>
 * @copyright 2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/api.php';

/**
 * Outputs detailed information about the group specified by ID
 *
 * @category API
 * @package  StatusNet
 * @author   Zach Copley <zach@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 */

class ApiGroupShowAction extends TwitterApiAction
{
    var $format = null;
    var $group = null;

    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        parent::prepare($args);

        $this->format = $this->arg('format');
        $this->group = $this->getTargetGroup($this->arg('id'));

        return true;
    }

    /**
     * Handle the request
     *
     * Check the format and show the user info
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (empty($this->group)) {
            $this->clientError(
                'Group not found!',
                404,
                $this->format
            );
            return;
        }

        switch($this->format) {
        case 'xml':
            $this->show_single_xml_group($this->group);
            break;
        case 'json':
            $this->show_single_json_group($this->group);
            break;
        default:
            $this->clientError(_('API method not found!'), 404, $this->format);
            break;
        }

    }

    /**
     * When was this group last modified?
     *
     * @return string datestamp of the latest notice in the stream
     */

    function lastModified()
    {
        if (!empty($this->group)) {
            return strtotime($this->group->modified);
        }

        return null;
    }

    /**
     * An entity tag for this group
     *
     * Returns an Etag based on the action name, language, and
     * timestamps of the notice
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->group)) {

            return '"' . implode(
                ':',
                array($this->arg('action'),
                      common_language(),
                      $this->group->id,
                      strtotime($this->group->modified))
            )
            . '"';
        }

        return null;
    }

}
