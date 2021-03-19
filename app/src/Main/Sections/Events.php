<?php

namespace {

    use CWP\CWP\PageTypes\EventPage;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\ListboxField;

    class Events extends Section
    {
        private static $singular_name = 'Events';

        private static $db = [
            'Content'      => 'HTMLText',
            'DisplayStyle' => 'Varchar'
        ];

        private static $many_many = [
            'EventPage' => EventPage::class
        ];

        public function getSectionCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', DropdownField::create('DisplayStyle', 'Display style',
                array(
                    'grid'   => 'Grid',
                    'slider' => 'Slider'
                )));
            $fields->addFieldToTab('Root.Main', ListboxField::create('EventPage', 'Select events', EventPage::get()->map('ID', 'Title')));
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
        }
    }
}
