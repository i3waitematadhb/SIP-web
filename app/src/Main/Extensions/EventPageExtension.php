<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\Image;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\ORM\DataExtension;

    class EventPageExtension extends DataExtension
    {
        private static $has_one = [
            'FeaturedImage' => Image::class
        ];

        private static $owns = [
            'FeaturedImage'
        ];

        public function updateCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', UploadField::create('FeaturedImage')->setFolderName('EventPage_Images/Featured_Images'), 'Location');
        }
    }
}
