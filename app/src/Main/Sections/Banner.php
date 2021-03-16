<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\Image;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

    class Banner extends Section
    {
        private static $singular_name = 'Banner';

        private static $db = [
            'Content' => 'HTMLText'
        ];

        private static $has_one = [
            'BannerImage' => Image::class
        ];

        private static $owns = [
            'BannerImage',
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', UploadField::create('BannerImage')->setFolderName('Banner_Images'));
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
        }
    }
}
