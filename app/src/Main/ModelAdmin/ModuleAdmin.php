<?php

namespace {

    use SilverStripe\Admin\ModelAdmin;

    class ModuleAdmin extends ModelAdmin
    {
        private static $menu_icon_class = 'font-icon-book';
        private static $url_segment = 'modules';
        private static $menu_title  = 'Modules';

        private static $managed_models  = [
            MemberType::class
        ];
    }
}
