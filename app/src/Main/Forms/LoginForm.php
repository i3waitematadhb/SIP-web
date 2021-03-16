<?php

namespace {

    use SilverStripe\Control\RequestHandler;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\Form;
    use SilverStripe\Forms\FormAction;
    use SilverStripe\Forms\LiteralField;
    use SilverStripe\Forms\PasswordField;
    use SilverStripe\Forms\RequiredFields;
    use SilverStripe\Forms\TextField;

    class LoginForm extends Form
    {
        public function __construct(RequestHandler $controller = null, $name = self::DEFAULT_NAME)
        {

            $fields = FieldList::create([
               // LiteralField::create('googleLogin','<div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>'),
                TextField::create('Email', 'Email')->setDescription('<i class="fal fa-envelope"></i>')->setAttribute('placeholder', 'Email')->addExtraClass(' theme-text'),
                PasswordField::create('Password', 'Password')->setDescription('<i class="fal fa-lock-alt"></i>')->setAttribute('placeholder', 'Password')->addExtraClass(' theme-text')
            ]);

            $actions = FieldList::create([
                FormAction::create('doLogin', 'Sign In')->addExtraClass('theme-button-alt'),
                LiteralField::create(
                    'forgotPassword',
                    '<a href="#" class="text-white w-100 d-block text-center button--text m-3">'
                    . _t('SilverStripe\\Security\\Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a>'
                )
            ]);

            $validator = RequiredFields::create([
                'Email',
                'Password'
            ]);

            parent::__construct($controller, $name, $fields, $actions, $validator);
        }
    }
}
