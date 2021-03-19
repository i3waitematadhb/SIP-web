<?php

namespace {

    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
    use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

    class Cards extends Section
    {
        private static $singular_name = 'Cards';

        private static $db = [
            'Content' => 'HTMLText'
        ];

        private static $has_many = [
            'CardItems' => CardItem::class
        ];

        public function getSectionCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));

            $gridConfig = GridFieldConfig_RecordEditor::create(999);
            if($this->CardItems()->Count())
            {
                $gridConfig->addComponent(new GridFieldOrderableRows());
            }
            $gridConfig->addComponent(new GridFieldEditableColumns());
            $gridColumns = $gridConfig->getComponentByType(GridFieldEditableColumns::class);
            $gridColumns->setDisplayFields([
                'Width' => [
                    'title' => 'Width',
                    'callback' => function($record, $column, $grid) {
                        return DropdownField::create($column, $column, SectionWidth::get()->map('Name','Name'));
                    }
                ],
                'Archived' => [
                    'title' => 'Archive',
                    'callback' => function($record, $column, $grid) {
                        return CheckboxField::create($column);
                    }]
            ]);

            $gridField = GridField::create(
                'CardItems',
                'Card',
                $this->CardItems(),
                $gridConfig
            );

            $fields->removeByName("CardItems");
            $fields->addFieldToTab('Root.Main', $gridField);
        }

        public function getVisibleCardItems()
        {
            return $this->owner->CardItems()->filter('Archived', false)->sort('Sort');
        }
    }
}
