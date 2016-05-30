```lang=yml
symfonyid_admin:
    app_title: 'SYMFONYID ADMIN BUNDLE'
    app_short_title: 'SIAB'
    per_page: 10
    identifier: 'id'
    max_records: 1000
    date_time_format: 'd-m-Y'
    menu:
        name: 'symfonyid_menu'
        loader: 'symfonyid.admin.menu.default_menu_loader'
    upload_dir: 'uploads'
    driver: 'orm' #odm or both
    translation_domain: 'SymfonyIdAdminBundle'
    profile_fields: ['full_name', 'username', 'email', 'roles', 'enabled']
    filters: ['name']
    number_format:
        decimal_precision: 0
        decimal_separator: ','
        thousand_separator: '.'
    user:
        form_class: 'symfonyid.admin.form.user_form'
        model_class: 'AppBundle\Entity\User'
        auto_enable: true
        show_fields: ['full_name', 'username', 'email', 'roles', 'enabled']
        grid_columns: ['full_name', 'username', 'email', 'roles', 'enabled']
        filters: ['full_name', 'username']
        password_form: 'symfonyid.admin.form.change_password_form'
    themes:
        dashboard: 'SymfonyIdAdminBundle:Index:index.html.twig'
        profile: 'SymfonyIdAdminBundle:Index:profile.html.twig'
        change_password: 'SymfonyIdAdminBundle:Index:change_password.html.twig'
        form_theme: 'SymfonyIdAdminBundle:Form:fields.html.twig'
        new_view: 'SymfonyIdAdminBundle:Crud:new.html.twig'
        bulk_new_view: 'SymfonyIdAdminBundle:Crud:bulk-new.html.twig'
        edit_view: 'SymfonyIdAdminBundle:Crud:new.html.twig'
        show_view: 'SymfonyIdAdminBundle:Crud:show.html.twig'
        list_view: 'SymfonyIdAdminBundle:Crud:list.html.twig'
        pagination: 'SymfonyIdAdminBundle:Layout:pagination.html.twig'
```