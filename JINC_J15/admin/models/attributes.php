<?php
/**
 * @copyright           Copyright (C) 2010 - Lhacky
 * @license		GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
 *   This file is part of JINC.
 *
 *   JINC is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   JINC is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with JINC.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class JINCModelAttributes extends JModel {
    function __construct() {
        parent::__construct();
        global $mainframe, $option;
    }

    function _buildQuery() {
        $query = 'SELECT attr_id, attr_name, attr_description, attr_type, attr_name_i18n ' .
            'FROM #__jinc_attribute a ORDER BY attr_id DESC ';
        return $query;
    }

    function & getData() {
        $query = $this->_buildQuery();
        $data = $this->_getList($query);
        return $data;
    }

    function createAttribute($attr_name, $attr_description, $attr_type, $attr_name_i18n = '') {
        jincimport('utility.servicelocator');
        jincimport('utility.jsonresponse');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $response = new JSONResponse();

        if (strlen($attr_name) == 0) {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR044');
            $response->set('errmsg', JText::_('ERROR_ERR044'));
            return $response->toString();
        }

        $dbo =& JFactory::getDBO();
        $attr_name_db = $dbo->getEscaped( $attr_name );
        $attr_description_db = $dbo->quote( $dbo->getEscaped( $attr_description ), false );
        $attr_name_i18n_db = $dbo->quote( $dbo->getEscaped( $attr_name_i18n ), false );

        $attr_name_db_table = $dbo->getEscaped( '' . $attr_name );

        $query = 'CREATE TABLE IF NOT EXISTS `#__jinc_attribute_' . $attr_name_db . '` (' .
            '`news_id` int(10) unsigned NOT NULL, ' .
            '`id` int(10) unsigned NOT NULL, ' .
            '`value` varchar(255), ' .
            'PRIMARY KEY  (`news_id`, `id`) )';
        $logger->debug('JINCModelAttributes: Executing query: ' . $query);
        $dbo->setQuery($query);
        if ( ! $dbo->query() ) {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR043');
            $response->set('errmsg', JText::_('ERROR_ERR043'));
            return $response->toString();
        }

        if (strlen($attr_name_i18n_db) == 0) $attr_name_i18n_db = $attr_name_db;
        $query = 'INSERT INTO #__jinc_attribute (attr_name, attr_description, attr_type, attr_name_i18n) ' .
            'VALUES ('. $dbo->quote( $attr_name_db, false) . ', ' . 
            $attr_description_db .', ' .
            (int) $attr_type . ', ' .
            $attr_name_i18n_db . ')';

        $logger->debug('JINCModelAttributes: Executing query: ' . $query);
        $dbo->setQuery($query);
        if ( $dbo->query() ) {
            $response->set('status', 0);
            $response->set('attr_id', $dbo->insertid());
            $response->set('attr_name', $attr_name);
            $response->set('attr_description', $attr_description);
            $response->set('attr_type', $attr_type);
            $response->set('attr_name_i18n', $attr_name_i18n);
        } else {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR043');
            $response->set('errmsg', JText::_('ERROR_ERR043'));
        }

        return $response->toString();
    }

    function removeAttribute($attr_name) {
        jincimport('utility.servicelocator');
        jincimport('utility.jsonresponse');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $response = new JSONResponse();
        if (strlen($attr_name) == 0) {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR044');
            $response->set('errmsg', JText::_('ERROR_ERR044'));
            return $response->toString();
        }

        $dbo =& JFactory::getDBO();
        $attr_name_db = $dbo->getEscaped( $attr_name );

        $query = 'DROP TABLE IF EXISTS `#__jinc_attribute_' . $attr_name_db . '`';
        $logger->debug('JINCModelAttributes: Executing query: ' . $query);
        $dbo->setQuery($query);
        if ( ! $dbo->query() ) {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR046');
            $response->set('errmsg', JText::_('ERROR_ERR046'));
            return $response->toString();
        }

        $query = 'SELECT news_name FROM #__jinc_newsletter ' .
            'WHERE news_attributes LIKE  ' . $dbo->quote('%' . $attr_name_db . '%', false) ;
        $dbo->setQuery($query);
        $logger->debug('JINCModelAttributes: Executing query: ' . $query);
        $result = $dbo->loadAssoc();
        if ($result == null) {
            $query = 'DELETE FROM #__jinc_attribute  ' .
                'WHERE attr_name = ' . $dbo->quote( $attr_name_db, false );

            $logger->debug('JINCModelAttributes: Executing query: ' . $query);
            $dbo->setQuery($query);
            if ( $dbo->query() ) {
                $response->set('status', 0);
            } else {
                $response->set('status', -1);
                $response->set('errcode', 'ERROR_ERR046');
                $response->set('errmsg', JText::_('ERROR_ERR046'));
            }
        } else {
            $response->set('status', -1);
            $response->set('errcode', 'ERROR_ERR045');
            $response->set('errmsg', JText::_('ERROR_ERR045') . ' ' . $result['news_name']);
        }
        return $response->toString();
    }
}
