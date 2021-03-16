<?php

namespace {

    use SilverStripe\Admin\ModelAdmin;

    class SubmissionAdmin extends ModelAdmin {
        private static $menu_icon_class = 'font-icon-list';
        private static $url_segment = 'submissions';
        private static $menu_title  = 'Submissions';

        private static $managed_models  = [
            Submissions::class,
            Records::class,
        ];

//        public function getExportFields()
//        {
//          //return parent::getExportFields(); // TODO: Change the autogenerated stub
//
////            $testArray = [
////                "gfdgfdgdfg" => 'Test',
////                "fdsfsf" => 'bcvbcvb'
////            ];
////
////            $submissions = Submissions::get();
////            $output = [];
////            foreach($submissions as $submission)
////            {
////                $records = $submission->Records();
////                $childOutput = [];
////                foreach ($records as $record) {
////                    $childOutput = [
////                        $record->Name => 'Test'
////                    ];
////                }
////            }
//
//            return [
//                'Name' => 'Name',
//            ];
//        }
    }
}
