<?php

namespace {

    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    class Accordion extends Section
    {
        private static $singular_name = 'Accordion';

        private static $has_many = [
            'AccordionItems' => AccordionItem::class
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $config = GridFieldConfig_RecordEditor::create('999');
            if ($this->AccordionItems()->Count()) {
                $config->addComponent(new GridFieldSortableRows('Sort'));
            }
            $editor = GridField::create('AccordionItems', 'Accordion items', $this->AccordionItems(), $config);
            $fields->removeByName("AccordionItems");
            $fields->addFieldToTab('Root.Main', $editor);
        }

        public function getVisibleItems()
        {
            return $this->AccordionItems()->filter('Archived', false)->sort('Sort');
        }
    }
}
