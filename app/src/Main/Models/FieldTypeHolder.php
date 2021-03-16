<?php

namespace {

    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\Forms\HiddenField;
    use SilverStripe\Forms\ReadonlyField;
    use SilverStripe\Forms\TextField;
    use SilverStripe\ORM\ArrayList;
    use SilverStripe\ORM\DataObject;
    use SilverStripe\View\ArrayData;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    class FieldTypeHolder extends DataObject
    {
        private static $default_sort = 'Sort';
        private static $table_name = 'FieldTypeHolder';

        private static $singular_name = "Field type holder";
        private static $plural_name = "Field type holders";

        private static $db = [
            'Name'     => 'Varchar',
            'FormType' => 'Varchar',
            'FormLimit'=> 'Int',
            'Archived' => 'Boolean',
            'Sort'     => 'Int'
        ];

        private static $has_one = [
            'Module' => Modules::class
        ];

        private static $has_many = [
            'Fields' => FieldType::class
        ];

        private static $summary_fields = [
            'Name',
            'FormType',
            'FormLimit',
            'Status'
        ];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields(); // TODO: Change the autogenerated stub
            $fields->removeByName('ModuleID');
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('ModuleRO', 'Module name', $this->Module()->Name));
            $fields->addFieldToTab('Root.Main', TextField::create('Name'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('FormType', 'Form type',
            array(
                'single'   => 'Single form',
                'multiple' => 'Multiple form'
            )));

            $fields->addFieldToTab('Root.Main', TextField::create('FormLimit')->displayIf("FormType")->isEqualTo("multiple")->end());

            $config= GridFieldConfig_RecordEditor::create('999');
            if ($this->Fields()->Count()) {
                $config->addComponent(new GridFieldSortableRows('Sort'));
            }
            $editor = GridField::create('Fields', 'Fields', $this->Fields(), $config);
            $fields->removeByName("Fields");
            $fields->addFieldToTab('Root.Main', $editor);

            $fields->addFieldToTab('Root.Main', CheckboxField::create('Archived'));
            $fields->addFieldToTab('Root.Main', HiddenField::create('Sort'));

            return $fields;
        }

        public function getStatus()
        {
            if($this->Archived == 1) return _t('GridField.Archived', 'Archived');
            return _t('GridField.Live', 'Live');
        }

        public function getMultipleFormFields() {
            $formLimit = new ArrayList();
            for ($i = 1; $i <= $this->FormLimit; $i++) {
                $formLimit->push(
                    new ArrayData([
                        'Number' => $i,
                        'VisibleFields' => $this->Fields()->filter('Archived', false)->sort('Sort')])
                );
            }
            return $formLimit;
        }

        public function getVisibleFields()
        {
            return $this->Fields()->filter('Archived', false)->sort('Sort');
        }
    }
}
