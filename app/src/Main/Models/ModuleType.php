<?php

namespace {

    use SilverStripe\ORM\DataObject;

    class ModuleType extends DataObject
    {
        private static $table_name = 'ModuleType';

        private static $db = [
            'Name'     => 'Varchar',
            'Archived' => 'Boolean',
        ];

        private static $summary_fields = [
            'Name'   => 'Module type',
            'Status'
        ];

        public function getStatus()
        {
            if($this->Archived == 1) return _t('GridField.Archived', 'Archived');
            return _t('GridField.Live', 'Live');
        }
    }
}
