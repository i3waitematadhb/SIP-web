<?php

namespace {

    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    class Carousel extends Section
    {
        private static $singular_name = 'Carousel';

        private static $db = [
            'Content' => 'HTMLText'
        ];

        private static $has_many = [
            'CarouselItems' => CarouselItem::class
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));

            $config = GridFieldConfig_RecordEditor::create('999');
            if ($this->CarouselItems()->Count()) {
                $config->addComponent(new GridFieldSortableRows('Sort'));
            }
            $editor = GridField::create('CarouselItems', 'Carousel items', $this->CarouselItems(), $config);
            $fields->removeByName("CarouselItems");
            $fields->addFieldToTab('Root.Main', $editor);
        }

        public function getVisibleItems()
        {
            return $this->CarouselItems()->filter('Archived', false)->sort('Sort');
        }
    }
}
