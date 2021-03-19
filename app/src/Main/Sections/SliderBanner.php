<?php

namespace {

    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    class SliderBanner extends Section
    {
        private static $singular_name = 'Slider Banner';

        private static $has_many = [
            'SliderBannerItems' => SliderBannerItem::class
        ];

        public function getSectionCMSFields(FieldList $fields)
        {
            $config = GridFieldConfig_RecordEditor::create('999');
            if ($this->SliderBannerItems()->Count()) {
                $config->addComponent(new GridFieldSortableRows('Sort'));
            }
            $editor = GridField::create('SliderBannerItems', 'Slider banner items', $this->SliderBannerItems(), $config);
            $fields->removeByName("SliderBannerItems");
            $fields->addFieldToTab('Root.Main', $editor);
        }

        public function getVisibleSliderItems()
        {
            return $this->SliderBannerItems()->filter('Archived', false)->sort('Sort');
        }
    }
}
