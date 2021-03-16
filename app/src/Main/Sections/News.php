<?php

namespace {

    use CWP\CWP\PageTypes\NewsPage;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\ListboxField;

    class News extends Section
    {
        private static $singular_name = 'News';

        private static $db = [
            'Content' => 'HTMLText'
        ];

        private static $many_many = [
            'NewsPage' => NewsPage::class
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
            $fields->addFieldToTab('Root.Main', ListboxField::create('NewsPage', 'Select news', NewsPage::get()->map('ID', 'Title')));
        }
    }
}
