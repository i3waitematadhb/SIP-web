<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\ListboxField;

    class Team extends Section
    {
        private static $singular_name = 'Team';

        private static $db = [
            'Content'    => 'HTMLText',
            'ShowButton' => 'Boolean'
        ];

        private static $has_one = [
            'Page' => SiteTree::class
        ];

        private static $many_many = [
            'Teams' => TeamPage::class
        ];

        public function getSectionCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
            $fields->addFieldToTab('Root.Main', ListboxField::create('Teams', 'Select members', TeamPage::get()->map('ID', 'Title')));
            $fields->addFieldToTab('Root.Main', CheckboxField::create('ShowButton', 'Show "all" button'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('PageID', 'Select a page', SiteTree::get()->map('ID','Title'))
            ->setEmptyString('(Select one)'));
        }
    }
}
