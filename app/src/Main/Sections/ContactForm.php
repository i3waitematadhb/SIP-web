<?php

namespace {

    use SilverStripe\Control\Email\Email;
    use SilverStripe\Control\RequestHandler;
    use SilverStripe\Forms\EmailField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\Form;
    use SilverStripe\Forms\FormAction;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\RequiredFields;
    use SilverStripe\Forms\TextareaField;
    use SilverStripe\Forms\TextField;

    class ContactForm extends Section
    {
        private static $singular_name = 'Contact Form';
        private static $allowed_actions = [
            'FeedbackForm'
        ];

        private static $db = [
            'Content' => 'HTMLText'
        ];

        public function getModuleCMSFields(FieldList $fields)
        {
            $fields->addFieldToTab('Root.Main', new HTMLEditorField('Content'));
        }

        public function FeedbackForm(RequestHandler $controller = null)
        {
            $fields = FieldList::create([
                TextField::create('Name', 'Name')->setAttribute('placeholder', 'Your Name'),
                EmailField::create('Email', 'Email')->setAttribute('placeholder', 'Your Email'),
                TextareaField::create('Message', 'Message')->setAttribute('placeholder', 'Your Message')
            ]);

            $actions = FieldList::create([
                FormAction::create('submit', 'Send')->addExtraClass('button')
            ]);

            $validator = RequiredFields::create([
                'Name',
                'Email',
                'Message'
            ]);

            return new Form($controller, 'FeedbackForm', $fields, $actions, $validator);
        }

        public function submit($data, $form)
        {
            $email = new Email();

            $email->setTo('keith.gulayan@waitematadhb.govt.nz');
            $email->setFrom($data['Email']);
            $email->setSubject('Contact Message from  {$data["Name"]}');
            $messageBody = "
            <p><strong>Name:</strong> {$data['Name']}</p>
            <p><strong>Message:</strong> {$data['Message']}</p>
        ";
            $email->setBody($messageBody);
            $email->send();
            return [
                'Content' => '<p>Thank you for your feedback.</p>',
                'Form' => ''
            ];
        }
    }
}
