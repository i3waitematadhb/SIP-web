<?php

namespace {

    use SilverStripe\Control\HTTPRequest;
    use SilverStripe\Core\Manifest\Module;
    use SilverStripe\Security\Member;
    use SilverStripe\Security\Security;

    class AjaxController extends AbstractApiController
    {
        private static $allowed_actions = [
            'populateDashboard',
            'populateDashboardItem',
            'populateDashboardByPeriod',
            'populateTableRowDetails',
            'submitModuleForm',
            'checkModuleFormDateExist',
            'addEntryToExistingSubmission',
            'getmemberType',
            'getperiod',
            'getmodule',
        ];

        public function populateDashboard(HTTPRequest $request)
        {
            $output = [];
            if (!$member = Security::getCurrentUser()) {
                $this->setLastError('Missing member from request');
                return $this->jsonOutput();
            }

            if (!$moduleID = $request->postVar('moduleID')) {
                $this->setLastError('Missing module id from request');
                return $this->jsonOutput();
            }

            if (!$moduleTypeID = $request->postVar('moduleTypeID')) {
                $this->setLastError('Missing module type id from request');
                return $this->jsonOutput();
            }

            //$memberType = MemberType::get()->byID($member->MemberTypeID);
            $formFields = [];
            $moduleType = ModuleType::get()->byID($moduleTypeID); //Check if Clinical module or Prescribing indicator
            $forms = FieldTypeHolder::get()->filter(['ModuleID' => $moduleID, 'Archived' => false]);
            foreach ($forms as $form) {
                $fields = $form->Fields()->filter(['ShowInDashboard' => true, 'Archived' => false]);
                foreach ($fields as $fieldItem) {
                    $formFields[] = [
                        'id'   => $fieldItem->ID,
                        'name' => $fieldItem->Name
                    ];
                }
                $output = $formFields;
            }

            return $this->jsonOutput($output);
        }

        public function populateDashboardItem(HTTPRequest $request)
        {
            $output = [];
            if (!$member = Security::getCurrentUser()) {
                $this->setLastError('Missing member from request');
                return $this->jsonOutput();
            }

            if (!$id = $request->postVar('dashboardItemId')) {
                $this->setLastError('Missing item id from request');
                return $this->jsonOutput();
            }

            if (!$moduleID = $request->postVar('moduleID')) {
                $this->setLastError('Missing module id from request');
                return $this->jsonOutput();
            }

            if (!$moduleTypeID = $request->postVar('moduleTypeID')) {
                $this->setLastError('Missing module type id from request');
                return $this->jsonOutput();
            }

            $memberType = MemberType::get()->byID($member->MemberTypeID);
            $moduleType = ModuleType::get()->byID($moduleTypeID);
            $submissions = $member->Submissions()->filter(['ModuleID' => $moduleID, 'Archived' => false]);
            if ($moduleType->Name === "Clinical Module") {
                $submissionOutput = [];
                foreach ($submissions as $submission) {
                    $records = $submission->Records()->filter('Archived', false);
                    foreach ($records as $recordIndex => $record) {
                        $responses = $record->Responses()->filter('Archived', false);
                        foreach ($responses as $response) {
                            if ($response->Identifier == $id) {
                                $submissionOutput[$submission->Date][] = $response->Response;
                            }
                        }
                    }
                }
                $output[] = [$submissionOutput, $moduleType->Name];
            } else {
                $submissionOutput = [];
                foreach ($submissions as $submission) {
                    $records = $submission->Records()->filter('Archived', false);
                    foreach ($records as $recordIndex => $record) {
                        $responses = $record->Responses()->filter('Archived', false);
                        foreach ($responses as $response) {
                            if ($response->Identifier == $id) {
                                $subResponses = $response->SubResponses()->filter('Archived', false);
                                $subResponseArray = [];
                                foreach ($subResponses as $subResponse) {
                                    $subResponseArray[$subResponse->Name] = intval($subResponse->SubResponse);
                                }
                                $submissionOutput[$submission->Date][] = $subResponseArray;
                            }
                        }
                    }
                }
                $output[] = [$submissionOutput, $moduleType->Name];
            }
            return $this->jsonOutput($output);
        }

        public function populateDashboardByPeriod(HTTPRequest $request)
        {
            $output = [];
            if (!$id = $request->postVar('id')) {
                $this->setLastError('Missing id from request');
                return $this->jsonOutput();
            }

            if (!$periodID = $request->postVar('periodID')) {
                $this->setLastError('Missing period id from request');
                return $this->jsonOutput();
            }

            $history = History::get()->filter('Archived', false)->byID($id);

            if (!$history) {
                $this->setLastError('No history in this selected period');
                return $this->jsonOutput();
            }

            $clinicalModuleIcon = Modules::get()->filter('Archived', false)->byID($history->ClinicalModuleID);
            $prescribingIndicatorIcon = Modules::get()->filter('Archived', false)->byID($history->PrescribingIndicatorID);

            if ($prescribingIndicatorIcon) {
                $output = [
                    'id' => $history->ID,
                    'clinicalmoduleid'   => $history->ClinicalModuleID,
                    'clinicalmodule'     => $history->ClinicalModule,
                    'clinicalmoduleIcon' => $clinicalModuleIcon->Icon,
                    'prescribingindicatorid'   => $history->PrescribingIndicatorID,
                    'prescribingindicator'     => $history->PrescribingIndicator,
                    'prescribingindicatorIcon' => $prescribingIndicatorIcon->Icon,
                ];
            } else {
                $output = [
                    'id' => $history->ID,
                    'clinicalmoduleid'   => $history->ClinicalModuleID,
                    'clinicalmodule'     => $history->ClinicalModule,
                    'clinicalmoduleIcon' => $clinicalModuleIcon->Icon,
                ];
            }

            return $this->jsonOutput($output);
        }

        public function populateTableRowDetails(HTTPRequest $request)
        {
            if(!$id = $request->postVar('id')) {
                $this->setLastError('Missing id from request');
                return $this->jsonOutput();
            }

            $records = Records::get()->filter(['SubmissionID' => $id, 'Archived' => false]);
            $output = [];

            /** @var Records $record */
            foreach ($records as $record) {
                $responses = $record->Responses()->filter('Archived', false);
                $childOutput = [
                    'id'   => $record->ID,
                    'name' => $record->Name,
                ];
                /** @var Responses $response */
                foreach ($responses as $response) {
                    $subResponses = $response->SubResponses()->filter('Archived', false);
                    if (count($subResponses)) {
                        $subResponseOutput = [];
                        foreach ($subResponses as $subResponse) {
                            $subResponseOutput[] = [
                                'id' => $subResponse->ID,
                                'name' => $subResponse->Name,
                                'subresponse' => $subResponse->SubResponse
                            ];
                        }
                        $childOutput['responses'][] = [
                            'id'       => $response->ID,
                            'label'    => $response->Name,
                            'response' => $subResponseOutput
                        ];
                    } else {
                        $childOutput['responses'][] = [
                            'id'       => $response->ID,
                            'label'    => $response->Name,
                            'response' => $response->Response
                        ];
                    }
                }
                $output[] = $childOutput;
            }

            return $this->jsonOutput($output);
        }

        public function submitModuleForm(HTTPRequest $request)
        {
            if (!$member = Security::getCurrentUser()) {
                $this->setLastError('Missing member from request');
                return $this->jsonOutput();
            }

            if (!$formModuleID = $request->postVar('formModuleID')) {
                $this->setLastError('Missing module id from request');
                return $this->jsonOutput();
            }

            if (!$PracticeID = $request->postVar('practiceID')) {
                $this->setLastError('Missing practice id from request');
                return $this->jsonOutput();
            }

            if (!$PHO = $request->postVar('pho')) {
                $this->setLastError('Missing pho from request');
                return $this->jsonOutput();
            }

            if (!$hasOneFormValuesObj = $request->postVar('hasOneFormValues')) {
                $this->setLastError('Missing single form values from request');
                return $this->jsonOutput();
            }

            if (!$hasManyFormValuesObj = $request->postVar('hasManyFormValues')) {
                $this->setLastError('Missing single form values from request');
                return $this->jsonOutput();
            }

            $hasOneFormValues  = json_decode($hasOneFormValuesObj);
            $hasManyFormValues = json_decode($hasManyFormValuesObj);

            $memberSubmissions = $member->Submissions()->filter(['ModuleID' => $formModuleID]);

            $submit = new Submissions();
            $submit->Name    = $member->FirstName ;
            $submit->Pho     = $PHO;
            $submit->PracticeID = $PracticeID;
            $submit->Contact = $hasOneFormValues[1];
            $submit->ModuleID= $formModuleID;
            foreach ($hasManyFormValues as $index => $hasManyFormValue)
            {
                foreach($hasManyFormValue as $fieldItem) {
                    $label = $fieldItem[1];
                    if (strpos($label, 'Dispensing Date') !== false) {
                        $splitDateByForwardSlash = explode('/', $fieldItem[2]);
                        $dispensingDateFixed     = '01/' . $splitDateByForwardSlash[1] . '/' . $splitDateByForwardSlash[2];
                        if (count($memberSubmissions)) {
                            foreach ($memberSubmissions as $memberSubmission) {
                                if ($memberSubmission->Date != $dispensingDateFixed) {
                                    $submit->Date = $dispensingDateFixed;
                                } else {
                                    $this->setLastError('Selected dispensing date already exist');
                                    return $this->jsonOutput();
                                }
                            }
                        } else {
                            $submit->Date = $dispensingDateFixed;
                        }
                    }
                }
            }

            $submissionID = $submit->write();
            $memberSubmissions->add($submissionID);

            if ($submissionID) {
                foreach ($hasManyFormValues as $index => $fieldItem) {
                    $record = new Records();
                    for ($i = 0; $i < count($fieldItem); $i++) {
                        $responseItem = $fieldItem[$i][2];
                        if (!is_array($responseItem)) {
                            $record->Name = 'Patient ' . $index;
                        } else {
                            $record->Name = $index;
                        }
                    }
                    $record->Submission = $submissionID;
                    $recordID = $record->write();
                    if ($recordID) {
                        for ($i = 0; $i < count($fieldItem); $i++) {
                            $responseItem = $fieldItem[$i][2];
                            $response = new Responses();
                            $response->Name = $fieldItem[$i][1];
                            $response->Identifier = $fieldItem[$i][0];
                            if (!is_array($responseItem)) {
                                $response->Response = $fieldItem[$i][2];
                            }
                            $response->Record = $recordID;
                            $responseID = $response->write();
                            if (is_array($responseItem)) {
                                for ($sr = 0; $sr < count($responseItem); $sr++) {
                                    $subResponses = new SubResponses();
                                    $subResponses->Name = $responseItem[$sr][0];
                                    $subResponses->SubResponse = $responseItem[$sr][1];
                                    $subResponses->ResponsesID = $responseID;
                                    $subResponses->write();
                                }
                            }
                        }
                    } else {
                        $this->setLastError('No record was submitted!');
                        return $this->jsonOutput();
                    }
                }
            } else {
                $this->setLastError('No submission found!');
                return $this->jsonOutput();
            }
            return $this->jsonOutput();
        }

        function checkModuleFormDateExist(HTTPRequest $request)
        {
            if (!$member = Security::getCurrentUser()) {
                $this->setLastError('Missing member from request');
                return $this->jsonOutput();
            }

            if (!$moduleID = $request->postVar('moduleID')) {
                $this->setLastError('Missing module id from request');
                return $this->jsonOutput();
            }

            if (!$month = $request->postVar('month')) {
                $this->setLastError('Missing month from request');
                return $this->jsonOutput();
            }

            $memberSubmissions = $member->Submissions()->filter(['ModuleID' => $moduleID, 'Archived' => false]);

            if (count($memberSubmissions)) {
                foreach ($memberSubmissions as $submission) {
                    $submissionDate = $submission->Date;
                    $submissionDateMonth = explode('/', $submissionDate)[1];
                    if ($submissionDateMonth === $month) {
                        $this->setLastError('Selected dispensing date is already exist.');
                        $this->jsonOutput();
                    }
                }
            }
            return $this->jsonOutput();
        }

        function addEntryToExistingSubmission(HTTPRequest $request)
        {
            $output = [];
            if (!$member = Security::getCurrentUser()) {
                $this->setLastError('Missing member from request');
                return $this->jsonOutput();
            }

            if (!$id = $request->postVar('id')) {
                $this->setLastError('Missing id from request');
            }

            if (!$moduleID = $request->postVar('moduleID')) {
                $this->setLastError('Missing module id from request');
            }

            $moduleContent = [];
            $modules = Modules::get()->byID($moduleID);
            foreach ($modules as $module) {
                $period = $module->Period();
                $moduleContent = [
                    'name' => $module->Name,
                    'type' => $module->ModuleType,
                    'start'=> $period->StartDate,
                    'end'  => $period->EndDate
                ];
            }

            $records = Records::get()->filter(['SubmissionID' => $id, 'Archived' => false]);
            $recordCount = count($records);

            $forms = FieldTypeHolder::get()->filter(['ModuleID' => $moduleID, 'Archived' => false]);

            if ($recordCount <! 10) {
                $this->setLastError('Entries are already filled.');
            }

            foreach ($forms as $form) {
                $fields = $form->Fields()->filter('Archived', false);
                $childOutput = [
                    'id' => $form->ID,
                    'formType' => $form->FormType
                ];
                foreach ($fields as $field) {
                    $extraItems = [];

                    /**
                     *  Dropdown
                     */
                    $fieldDropdownItems = $field->DropdownFieldItems()->filter('Archived', false);
                    if (count($fieldDropdownItems)) {
                        foreach ($fieldDropdownItems as $fieldDropdownItem) {
                            $extraItems[] = [
                                'id'   => $fieldDropdownItem->ID,
                                'name' => $fieldDropdownItem->Name
                            ];
                        }
                    }

                    /**
                     *  Checkbox
                     */
                    $fieldCheckboxItems = $field->CheckboxFieldItems()->filter('Archived', false);
                    if (count($fieldCheckboxItems)) {
                        foreach ($fieldCheckboxItems as $fieldCheckboxItem) {
                            $extraItems[] = [
                                'id'   => $fieldCheckboxItem->ID,
                                'name' => $fieldCheckboxItem->Name
                            ];
                        }
                    }

                    /**
                     *  Radio
                     */
                    $fieldRadioItems = $field->RadioFieldItems()->filter('Archived', false);
                    if (count($fieldRadioItems)) {
                        foreach ($fieldRadioItems as $fieldRadioItem) {
                            $extraItems[] = [
                                'id'   => $fieldRadioItem->ID,
                                'name' => $fieldRadioItem->Name
                            ];
                        }
                    }

                    /**
                     * MultiTextbox
                     */
                    $fieldMultiTextboxItems = $field->MultiTextboxItems()->filter('Archived', false);
                    if (count($fieldMultiTextboxItems)) {
                        foreach($fieldMultiTextboxItems as $fieldMultiTextboxItem) {
                            $extraItems[] = [
                                'id' => $fieldMultiTextboxItem->ID,
                                'name' => $fieldMultiTextboxItem->Name
                            ];
                        }
                    }

                    $childOutput['fields'][] = [
                        'id'   => $field->ID,
                        'name' => $field->Name,
                        'label'=> $field->Label,
                        'type' => $field->Type,
                        'content' => $field->Content,
                        'extras'  => $extraItems
                    ];
                }
                $output[] = $childOutput;
            }

            $count = [
                'record_count' => $recordCount
            ];
            return $this->jsonOutput([$output,$moduleContent, $count]);
        }

        function getmemberType(HTTPRequest $request)
        {
            $output = [];
            if (!$id = $request->postVar('id')) {
                $this->setLastError('Missing member type id from request');
                return $this->jsonOutput();
            }

            $periods = ModulePeriod::get()->filter(['MemberTypeID' => $id ,'Archived' => false]);
            foreach($periods as $period) {
                $output[] = [
                    'id'   => $period->ID,
                    'name' => $period->Name,
                    'start'=> $period->StartDate,
                    'end'  => $period->EndDate
                ];
            }
            return $this->jsonOutput($output);
        }

        function getperiod(HTTPRequest $request)
        {
            $output = [];
            if (!$id = $request->postVar('id')) {
                $this->setLastError('Missing period id from request');
                return $this->jsonOutput();
            }

            $modules = Modules::get()->filter(['PeriodID' => $id, 'Archived' => false]);
            foreach ($modules as $module) {
                $output[] = [
                    'id'  => $module->ID,
                    'name'=> $module->Name,
                ];
            }
            return $this->jsonOutput($output);
        }

        function getmodule(HTTPRequest $request)
        {
            $output = [];
            $sortedNames = []; $sortedResponses = [];
            $columns = [];

            if (!$id = $request->postVar('id')) {
                $this->setLastError('Missing module id from request');
                return $this->jsonOutput();
            }

            $module = Modules::get()->byID($id);
            $forms  = FieldTypeHolder::get()->filter(['ModuleID' => $id, 'Archived' => false]);
            $submissions = Submissions::get()->filter(['ModuleID' => $id, 'Archived' => false]);

            foreach ($forms as $form) {
                if ($form->FormType === 'multiple') {
                    $fields = $form->Fields();
                    foreach ($fields as $field) {
                        $sortedNames[] = $field->Name;
                    }
                }
            }

            if (count($sortedNames)) { //If $sortedResponses is not empty.
                foreach ($submissions as $submission) {
                    $records  = $submission->Records()->filter('Archived', false);
                    foreach ($records as $record) {
                        $responses = $record->Responses()->filter('Archived', false);
                        $ctr = 1;
                        foreach ($responses as $response) {
                            $index = array_search($response->Name, $sortedNames);
                            if (strpos($response->Name, 'Dispensing') === false && strpos($response->Name, 'Ethnicity') === false) {
                                $codeName = $module->CodeName . '_' . $ctr;
                                $sortedResponses[$index] = [
                                    $codeName => $response->Response
                                ];
                                $columns[$ctr + 2] = [
                                    'data' => $codeName,
                                    'name' => $codeName
                                ];
                                $ctr = $ctr + 1;
                            } else {
                                if (strpos($response->Name, 'Date')) {
                                    $columns[0] = [
                                        'data' => 'Review_Date',
                                        'name' => 'Review_Date'
                                    ];
                                } else {
                                    $columns[1] = [
                                        'data' => 'Ethnicity',
                                        'name' => 'Ethnicity'
                                    ];
                                }
                                $sortedResponses[$index] = $response->Response;
                            }
                        }
                        $output[] = [
                            'Module'     => $module->Name,
                            'PracticeID' => $submission->PracticeID,
                            'PHO'        => $submission->Pho,
                            'Review_Date'=> $submission->Date,
                            'responses'  => $sortedResponses
                        ];
                    }
                }
            }

            return $this->jsonOutput([$output, $columns]);
        }
    }
}
