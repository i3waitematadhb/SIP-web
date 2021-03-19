<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\Image;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

    class ImageWithText extends Section
    {
        private static $singular_name = 'Image with Text';

        private static $db = [
            'Content'      => 'HTMLText',
            'TextPosition' => 'Varchar'
        ];

        private static $has_one = [
            'Image' => Image::class
        ];

        private static $owns = [
            'Image'
        ];

        public function getSectionCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', UploadField::create('Image')->setFolderName('ImageWithText/Images'));
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('TextPosition', 'Text position',
            array(
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right'
            )));
        }
    }
}
