<?php

namespace {

    use SilverStripe\Security\Security;

    class ModulePageController extends PageController
    {
        public function getCurrentUser()
        {
            return Security::getCurrentUser();
        }

        public function PopulateTable($moduleID)
        {
            $member = $this->getCurrentUser();
            $submission = $member->Submissions()->filter([
                'ModuleID' => $moduleID,
                'Archived' => false
            ]);
            return $submission;
        }
    }
}
