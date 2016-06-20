<?php

/*
 * This file is part of the SymfonyIdAdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyId\AdminBundle;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonyIdAdminConstrants
{
    /**
     * Configuration.
     */
    const ROOT_PARAMETER = 'symfonyid';
    const CONFIGURATION_ALIAS = self::ROOT_PARAMETER.'_admin';
    const APP_TITLE = 'SIAB';
    const APP_DESCRIPTION = 'SymfonyId Admin Bundle';

    /**
     * Crud Events.
     */
    const PRE_SAVE = self::ROOT_PARAMETER.'.pre_save';
    const POST_SAVE = self::ROOT_PARAMETER.'.post_save';
    const FILTER_LIST = self::ROOT_PARAMETER.'.filter_query';
    const PRE_DELETE = self::ROOT_PARAMETER.'.pre_delete';
    const PRE_SHOW = self::ROOT_PARAMETER.'.pre_show';

    /**
     * Crud Actions.
     */
    const ACTION_CREATE = 'ACTION_CREATE';
    const ACTION_UPDATE = 'ACTION_UPDATE';
    const ACTION_DELETE = 'ACTION_DELETE';
    const ACTION_READ = 'ACTION_READ';
    const ACTION_DOWNLOAD = 'ACTION_DOWNLOAD';

    /**
     * Grid Actions.
     */
    const GRID_ACTION_SHOW = 'GRID_ACTION_SHOW';
    const GRID_ACTION_EDIT = 'GRID_ACTION_EDIT';
    const GRID_ACTION_DELETE = 'GRID_ACTION_DELETE';

    /**
     * Model Utilities.
     */
    const MODEL_ALIAS = 'e';
    const SESSION_SORTED_ID = self::ROOT_PARAMETER.'_sorted';

    /**
     * Templating.
     */
    const TEMPLATE_CREATE = 'SymfonyIdAdminBundle:Crud:new.html.twig';
    const TEMPLATE_BULK_CREATE = 'SymfonyIdAdminBundle:Crud:bulk-new.html.twig';
    const TEMPLATE_EDIT = 'SymfonyIdAdminBundle:Crud:new.html.twig';
    const TEMPLATE_SHOW = 'SymfonyIdAdminBundle:Crud:show.html.twig';
    const TEMPLATE_LIST = 'SymfonyIdAdminBundle:Crud:list.html.twig';
    const TEMPLATE_AJAX = 'SymfonyIdAdminBundle:Crud:list_template.html.twig';
    const TEMPLATE_DASHBOARD = 'SymfonyIdAdminBundle:Index:index.html.twig';
    const TEMPLATE_PROFILE = 'SymfonyIdAdminBundle:Index:profile.html.twig';
    const TEMPLATE_CHANGE_PASSWORD = 'SymfonyIdAdminBundle:Index:change_password.html.twig';
    const TEMPLATE_FORM = 'SymfonyIdAdminBundle:Form:fields.html.twig';
    const TEMPLATE_PAGINATION = 'SymfonyIdAdminBundle:Layout:pagination.html.twig';

    /**
     * Cache.
     */
    const CACHE_DIR = self::ROOT_PARAMETER;
}
