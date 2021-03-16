<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\HiddenField;
    use SilverStripe\Forms\ReadonlyField;
    use SilverStripe\Forms\TextField;
    use SilverStripe\ORM\DataObject;

    class LinkItem extends DataObject
    {
        private static $default_sort = 'Sort ASC';

        private static $db = [
            'Name'     => 'Text',
            'Archived' => 'Boolean',
            'Sort'     => 'Int',
        ];

        private static $has_one = [
            'Parent' => Footer::class,
            'Page'   => SiteTree::class,
        ];

        private static $summary_fields = [
            'Name',
            'PageLink' => 'Page link',
            'Status'
        ];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields(); // TODO: Change the autogenerated stub
            $fields->removeByName('ParentID');
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('ParentRO', 'Parent', $this->Parent()->Name));

            $fields->addFieldToTab('Root.Main', TextField::create('Name'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('PageID', 'Select a page',
                SiteTree::get()->map('ID','Title'))->setEmptyString('(Select one)'));
            $fields->addFieldToTab('Root.Main', CheckboxField::create('Archived'));
            $fields->addFieldToTab('Root.Main', HiddenField::create('Sort'));

            return $fields;
        }

        public function getStatus()
        {
            if($this->Archived == 1) return _t('GridField.Archived', 'Archived');
            return _t('GridField.Live', 'Live');
        }

        public function getPageLink()
        {
            if ($this->Page) return $this->Page()->Link();
            return 'No selected page';
        }
    }
}
