<?php

namespace {

    use SilverStripe\Admin\CMSMenu;
    use SilverStripe\Admin\LeftAndMainExtension;

    class SiteLeftAndMainExtension extends LeftAndMainExtension
    {
        public function init()
        {
            CMSMenu::remove_menu_item('SilverStripe-CampaignAdmin-CampaignAdmin');
            CMSMenu::remove_menu_item('SilverStripe-Reports-ReportAdmin');
            CMSMenu::remove_menu_item('SilverStripe-VersionedAdmin-ArchiveAdmin');
        }
    }
}
