<?php

/*
 * This file is part of the AdminBundle package.
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
    const CONFIGURATION_ALIAS = 'symfonyid_admin';
    const APP_TITLE = 'symfonyid';
    const APP_DESCRIPTION = 'SymfonyId Admin Bundle';

    /**
     * Crud Events.
     */
    const PRE_SAVE = 'symfonyid.pre_save';
    const POST_SAVE = 'symfonyid.post_save';
    const FILTER_LIST = 'symfonyid.filter_query';
    const PRE_DELETE = 'symfonyid.pre_delete';
    const PRE_SHOW = 'symfonyid.pre_show';

    /**
     * Crud Actions.
     */
    const ACTION_CREATE = 'ACTION_CREATE';
    const ACTION_UPDATE = 'ACTION_UPDATE';
    const ACTION_DELETE = 'ACTION_DELETE';
    const ACTION_READ = 'ACTION_READ';

    /**
     * Grid Actions.
     */
    const GRID_ACTION_SHOW = 'GRID_ACTION_SHOW';
    const GRID_ACTION_EDIT = 'GRID_ACTION_EDIT';
    const GRID_ACTION_DELETE = 'GRID_ACTION_DELETE';

    /**
     * Model Utilities.
     */
    const ENTITY_ALIAS = 'e';
    const SESSION_SORTED_ID = 'symfonyid_sorted';

    /**
     * Templating.
     */
    const TEMPLATE_CREATE = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';
    const TEMPLATE_BULK_CREATE = 'SymfonianIndonesiaAdminBundle:Crud:bulk-new.html.twig';
    const TEMPLATE_EDIT = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';
    const TEMPLATE_SHOW = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';
    const TEMPLATE_LIST = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';
    const TEMPLATE_AJAX = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';
    const TEMPLATE_DASHBOARD = 'SymfonianIndonesiaAdminBundle:Index:index.html.twig';
    const TEMPLATE_PROFILE = 'SymfonianIndonesiaAdminBundle:Index:profile.html.twig';
    const TEMPLATE_CHANGE_PASSWORD = 'SymfonianIndonesiaAdminBundle:Index:change_password.html.twig';
    const TEMPLATE_FORM = 'SymfonianIndonesiaAdminBundle:Form:fields.html.twig';
    const TEMPLATE_PAGINATION = 'SymfonianIndonesiaAdminBundle:Layout:pagination.html.twig';

    /**
     * Cache.
     */
    const CACHE_DIR = 'symfonyid';
}
