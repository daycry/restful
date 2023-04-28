<?php

declare(strict_types=1);

namespace Daycry\RestFul\Libraries;

use CodeIgniter\HTTP\IncomingRequest as BaseIncomingRequest;
use Config\App;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Daycry\RestFul\Formats\InputFormat;

class IncomingRequest extends BaseIncomingRequest
{
    /**
     * Constructor
     *
     * @param App         $config
     * @param string|null $body
     */
    public function __construct($config, ?URI $uri = null, $body = 'php://input', ?UserAgent $userAgent = null)
    {
        parent::__construct($config, $uri, $body, $userAgent);
    }

    public function getAllParams(): array
    {
        $inputFormat = InputFormat::check($this);

        $headers = array_map(
            function ($header) {
                return $header->getValueLine();
            },
            $this->headers()
        );

        if ($inputFormat == 'application/json') {
            $content = $this->getJSON();
        } else {
            // @codeCoverageIgnoreStart
            $content = $this->getRawInput();
            // @codeCoverageIgnoreEnd
        }

        return array_merge($this->getGetPost(), $headers, ['body' => $content]);
    }
}
