<?php

namespace {

    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    class TextWithIcon extends Section
    {
        private static $singular_name = 'Text with Icon';

        private static $db = [
            'Content' => 'HTMLText'
        ];

        private static $has_many = [
            'TextIconItems' => TextIconItem::class
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', new HTMLEditorField('Content'));
            $config = GridFieldConfig_RecordEditor::create('999');
            if ($this->TextIconItems()->Count()) {
                $config->addComponent(new GridFieldSortableRows('Sort'));
            }
            $editor = GridField::create('TextIconItems', 'Text with icon items', $this->TextIconItems(), $config);
            $fields->removeByName("TextIconItems");
            $fields->addFieldToTab('Root.Main', $editor);
        }

        public function getVisibleItems()
        {
            return $this->TextIconItems()->filter('Archived', false)->sort('Sort');
        }
    }
}
