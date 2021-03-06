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
    const CONFIGURATION_ALIAS = 'symfonyid_admin';
    const APP_TITLE = 'SIAB';
    const APP_DESCRIPTION = 'SymfonyId Admin Bundle';

    /**
     * Crud Events.
     */
    const PRE_UPLOAD = 'symfonyid.pre_upload';
    const PRE_SAVE = 'symfonyid.pre_save';
    const POST_SAVE = 'symfonyid.post_save';
    const FILTER_LIST = 'symfonyid.filter_query';
    const PRE_DELETE = 'symfonyid.pre_delete';
    const PRE_SHOW = 'symfonyid.pre_show';
    const POST_UPLOAD = 'symfonyid.post_upload';

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
    const SESSION_SORTED_ID = 'symfonyid_sorted';
    const LIST_HANDLER = 'symfonyid.admin.crud.default_records_handler';

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
    const TEMPLATE_PAGINATION = 'SymfonyIdAdminBundle:Layout:pagination.html.twig';

    /**
     * Cache.
     */
    const CACHE_DIR = 'ad3n';
}
