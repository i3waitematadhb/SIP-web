<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\File;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\ORM\DataExtension;
    use SilverStripe\Versioned\Versioned;

    class SiteConfigExtension extends DataExtension
    {
        private static $db = [
            'LogoWebsiteURL' => 'Varchar',
        ];

        private static $has_one = [
            'Logo' => File::class, //File allows you to upload an svg logo
        ];

        private static $owns = [
            'Logo',
        ];

        public function updateCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab("Root.Header", UploadField::create("Logo")->setFolderName('Logo'));

            /*
             * Member type
             */
            $configMemberType = GridFieldConfig_RecordEditor::create('999');
            $editorMemberType = GridField::create('MemberType', 'Member types', MemberType::get(), $configMemberType);
            $fields->addFieldToTab('Root.Member', $editorMemberType);

            /*
             * Animations
             */
            $configAnimation = GridFieldConfig_RecordEditor::create('999');
            $editorAnimation = GridField::create('Animation', 'Animations', Animation::get(), $configAnimation);
            $fields->addFieldToTab('Root.SectionSettings', $editorAnimation);

            /*
             * Section Width
             */
            $configWidth = GridFieldConfig_RecordEditor::create('999');
            $editorWidth = GridField::create('SectionWidth', 'Width', SectionWidth::get(), $configWidth);
            $fields->addFieldToTab('Root.SectionSettings', $editorWidth);

            /*
             * Ethnicity
             */
            $configEthnicity = GridFieldConfig_RecordEditor::create('999');
            $editorEthnicity = GridField::create('Ethnicity', 'Ethnicity', Ethnicity::get(), $configEthnicity);
            $fields->addFieldToTab('Root.Ethnicity', $editorEthnicity);

            /*
             * PHO's
             */
            $configPHO = GridFieldConfig_RecordEditor::create('999');
            $editorPHO = GridField::create('Pho', 'Pho', PhoList::get(), $configPHO);
            $fields->addFieldToTab('Root.PhoLists', $editorPHO);

            /*
             * Header Pre-navigation
             */
            $configTopNavs = GridFieldConfig_RecordEditor::create('999');
            $editorTopNavs = GridField::create('TopBarNavigations', 'Top-bar navigations', TopBarNavigations::get(), $configTopNavs);
            $fields->addFieldToTab('Root.Header', $editorTopNavs);

            /*
             * Footer
             */
            $configFooter = GridFieldConfig_RecordEditor::create('999');
            $editorFooter = GridField::create('Footer', 'Footer', Footer::get(), $configFooter);
            $fields->addFieldToTab('Root.Footer', $editorFooter);
        }

        public function onAfterWrite()
        {
            if (!$this->owner->hasExtension(Versioned::class)) {
                $this->owner->publishRecursive();
            }
        }
    }
}
