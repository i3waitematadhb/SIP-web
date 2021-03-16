<?php

namespace {

    use SilverStripe\Control\Controller;
    use SilverStripe\Control\HTTPResponse;
    use SilverStripe\Core\Config\Config;
    use SilverStripe\Core\Convert;

    abstract class AbstractApiController extends Controller
    {
        private static $allowed_actions = [
            'index'
        ];

        /** @var bool If the API has encountered an error, this should be set to true, setting {$this->setLastError()} will toggle this automatically */
        protected $hasError = false;

        /** @var string The last error message the API encountered should be stored here with {$this->setLastError()} */
        protected $lastError;

        /** @var string The last error message the API encountered should be stored here with {$this->setElementError()} */
        protected $elementError;

        /** @var int The status code which will be sent with the response */
        protected $statusCode = 200;

        /** @var array Allows you to maintain consistency by lazily calling $this->jsonOutput without parameters when */
        private static $default_output = [
            'success' => true
        ];

        /**
         * Index catcher, nothing to do here: 405 Method Not Allowed
         *
         * @return HTTPResponse
         */
        public function index()
        {
            $this->setStatusCode(405)->setLastError('No endpoint specified. Please refer to the documentation provided to you');

            return $this->jsonOutput();
        }

        /**
         * @param array|null $data
         *
         * @return HTTPResponse
         */
        public function jsonOutput($data = null)
        {
            $response = new HTTPResponse();
            $response->addHeader('Content-Type', 'application/json');
            $response->setStatusCode($this->getStatusCode());

            if ($this->isHasError()) {
                return $response->setBody(Convert::array2json([
                    'error'   => true,
                    'message' => $this->getLastError(),
                    'element' => $this->getElementError()
                ]));
            }

            return $response->setBody(Convert::array2json([
                'data'  => ($data) ? $data : Config::inst()->get(static::class, 'default_output'),
                'error' => false
            ]));
        }

        /**
         * @param bool $bool
         *
         * @return $this
         */
        public function setHasError($bool)
        {
            $this->hasError = $bool;
            return $this;
        }

        /**
         * @return bool
         */
        public function isHasError()
        {
            return $this->hasError;
        }

        /**
         * @return mixed
         */
        public function getLastError()
        {
            return $this->lastError;
        }

        /**
         * @param string $lastError
         *
         * @return $this
         */
        public function setLastError($lastError)
        {
            $this->setHasError(true);
            $this->lastError = $lastError;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getElementError()
        {
            return $this->elementError;
        }

        /**
         * @param string $elementError
         *
         * @return $this
         */
        public function setElementError($elementError)
        {
            $this->setHasError(true);
            $this->elementError = $elementError;

            return $this;
        }

        /**
         * @return int
         */
        public function getStatusCode()
        {
            return $this->statusCode;
        }

        /**
         * @param int $statusCode
         *
         * @return $this
         */
        public function setStatusCode($statusCode)
        {
            $this->statusCode = $statusCode;

            return $this;
        }
    }
}
