<?php
/** @noinspection PhpComposerExtensionStubsInspection, PhpDuplicateSwitchCaseBodyInspection */

namespace Swoole\Curl;

use ReflectionClass;
use Swoole;
use Swoole\Coroutine\Http\Client;
use CURLFile;
use Iterator;
use Swoole\Curl\Exception as CurlException;
use Swoole\Http\Status;

class Handler
{
    /**
     * @var Client
     */
    private $client;
    private $info = [
        'url' => '',
        'content_type' => '',
        'http_code' => 0,
        'header_size' => 0,
        'request_size' => 0,
        'filetime' => -1,
        'ssl_verify_result' => 0,
        'redirect_count' => 0,
        'total_time' => 5.3E-5,
        'namelookup_time' => 0.0,
        'connect_time' => 0.0,
        'pretransfer_time' => 0.0,
        'size_upload' => 0.0,
        'size_download' => 0.0,
        'speed_download' => 0.0,
        'speed_upload' => 0.0,
        'download_content_length' => -1.0,
        'upload_content_length' => -1.0,
        'starttransfer_time' => 0.0,
        'redirect_time' => 0.0,
        'redirect_url' => '',
        'primary_ip' => '',
        'certinfo' => [],
        'primary_port' => 0,
        'local_ip' => '',
        'local_port' => 0,
        'http_version' => 0,
        'protocol' => 0,
        'ssl_verifyresult' => 0,
        'scheme' => '',
    ];

    private $withHeaderOut = false;
    private $withFileTime = false;
    private $urlInfo;
    private $postData;
    private $infile;
    private $infileSize = PHP_INT_MAX;
    private $outputStream;
    private $proxy_type;
    private $proxy;
    private $proxy_port = 1080;
    private $proxy_username;
    private $proxy_password;
    private $clientOptions = [];
    private $followLocation = false;
    private $autoReferer = false;
    private $maxRedirs;
    private $withHeader = false;
    private $nobody = false;

    /** @var callable */
    private $headerFunction;
    /** @var callable */
    private $readFunction;
    /** @var callable */
    private $writeFunction;
    /** @var callable */
    private $progressFunction;

    public $returnTransfer = false;
    public $method = 'GET';
    public $headers = [];

    public $transfer;

    public $errCode = 0;
    public $errMsg = '';

    public function __construct(string $url = '')
    {
        if ($url) {
            $this->setUrl($url);
        }
    }

    private function create(?array $urlInfo = null): void
    {
        if ($urlInfo === null) {
            $urlInfo = $this->urlInfo;
        }
        $this->client = new Client($urlInfo['host'], $urlInfo['port'], $urlInfo['scheme'] === 'https');
    }

    private function setUrl(string $url, bool $setInfo = true): bool
    {
        if (strlen($url) === 0) {
            $this->setError(CURLE_URL_MALFORMAT, 'No URL set!');
            return false;
        }
        if (strpos($url, '://') === false) {
            $url = 'http://' . $url;
        }
        if ($setInfo) {
            $urlInfo = parse_url($url);
            if (!is_array($urlInfo)) {
                $this->setError(CURLE_URL_MALFORMAT, "URL[{$url}] using bad/illegal format");
                return false;
            }
            if (!$this->setUrlInfo($urlInfo)) {
                return false;
            }
        }
        $this->info['url'] = $url;
        return true;
    }

    private function setUrlInfo(array $urlInfo): bool
    {
        if (empty($urlInfo['scheme'])) {
            $urlInfo['scheme'] = 'http';
        }
        $scheme = $urlInfo['scheme'];
        if ($scheme !== 'http' and $scheme !== 'https') {
            $this->setError(CURLE_UNSUPPORTED_PROTOCOL, "Protocol \"{$scheme}\" not supported or disabled in libcurl");
            return false;
        }
        $host = $urlInfo['host'];
        if ($this->info['primary_port'] !== 0) {
            /* keep same with cURL, primary_port has the highest priority */
            $urlInfo['port'] = $this->info['primary_port'];
        } elseif (empty($urlInfo['port'])) {
            $urlInfo['port'] = $scheme === 'https' ? 443 : 80;
        } else {
            $urlInfo['port'] = intval($urlInfo['port']);
        }
        $port = $urlInfo['port'];
        if ($this->client) {
            $oldUrlInfo = $this->urlInfo;
            if (
                $host !== $oldUrlInfo['host'] or
                $port !== $oldUrlInfo['port'] or
                $scheme !== $oldUrlInfo['scheme']
            ) {
                /* target changed */
                $this->create($urlInfo);
            }
        }
        $this->urlInfo = $urlInfo;
        return true;
    }

    private function setPort(int $port): void
    {
        $this->info['primary_port'] = $port;
        if ($this->urlInfo['port'] !== $port) {
            $this->urlInfo['port'] = $port;
            if ($this->client) {
                /* target changed */
                $this->create();
            }
        }
    }

    public function execute()
    {
        $this->info['redirect_count'] = $this->info['starttransfer_time'] = 0;
        $this->info['redirect_url'] = '';
        $timeBegin = microtime(true);
        /**
         * Socket
         */
        if (!$this->urlInfo) {
            $this->setError(CURLE_URL_MALFORMAT, 'No URL set or URL using bad/illegal format');
            return false;
        }
        if (!$this->client) {
            $this->create();
        }
        $client = $this->client;
        do {
            /**
             * Http Proxy
             */
            if ($this->proxy) {
                $proxy = explode(':', $this->proxy);
                $proxy_port = $proxy[1] ?? $this->proxy_port;
                $proxy = $proxy[0];
                if (!filter_var($proxy, FILTER_VALIDATE_IP)) {
                    $ip = Swoole\Coroutine::gethostbyname($proxy, AF_INET, $this->clientOptions['connect_timeout'] ?? -1);
                    if (!$ip) {
                        $this->setError(CURLE_COULDNT_RESOLVE_PROXY, 'Could not resolve proxy: ' . $proxy);
                        return false;
                    } else {
                        $this->proxy = $proxy = $ip;
                    }
                }
                switch ($this->proxy_type) {
                    case CURLPROXY_HTTP:
                        $proxy_options = [
                            'http_proxy_host' => $proxy,
                            'http_proxy_port' => $proxy_port,
                            'http_proxy_username' => $this->proxy_username,
                            'http_proxy_password' => $this->proxy_password
                        ];
                        break;
                    case CURLPROXY_SOCKS5:
                        $proxy_options = [
                            'socks5_host' => $proxy,
                            'socks5_port' => $proxy_port,
                            'socks5_username' => $this->proxy_username,
                            'socks5_password' => $this->proxy_password
                        ];
                        break;
                    default:
                        throw new CurlException("Unexpected proxy type [{$this->proxy_type}]");
                }
            }
            /**
             * Client Options
             */
            $client->set(
                $this->clientOptions +
                ($proxy_options ?? [])
            );
            /**
             * Method
             */
            $client->setMethod($this->method);
            /**
             * Infile
             */
            if ($this->infile) {
                $data = '';
                while (true) {
                    $nLength = $this->infileSize - strlen($data);
                    if ($nLength === 0) {
                        break;
                    }
                    if (feof($this->infile)) {
                        break;
                    }
                    $data .= fread($this->infile, $nLength);
                }
                $client->setData($data);
                $this->infile = null;
                $this->infileSize = PHP_INT_MAX;
            }
            /**
             * Upload File
             */
            if ($this->postData and is_array($this->postData)) {
                foreach ($this->postData as $k => $v) {
                    if ($v instanceof CURLFile) {
                        $client->addFile($v->getFilename(), $k, $v->getMimeType() ?: 'application/octet-stream', $v->getPostFilename());
                        unset($this->postData[$k]);
                    }
                }
            }
            /**
             * Post Data
             */
            if ($this->postData) {
                if (is_string($this->postData) and empty($this->headers['Content-Type'])) {
                    $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
                }
                $client->setData($this->postData);
                $this->postData = [];
            }
            /**
             * Http Headers
             */
            $this->headers['Host'] = $this->urlInfo['host'] . (isset($this->urlInfo['port']) ? (':' . $this->urlInfo['port']) : ''); /* TODO: remove it (built-in support) */
            $client->setHeaders($this->headers);
            /**
             * Execute
             */
            $executeResult = $client->execute($this->getUrl());
            if (!$executeResult) {
                $errCode = $client->errCode;
                if ($errCode == SWOOLE_ERROR_DNSLOOKUP_RESOLVE_FAILED or $errCode == SWOOLE_ERROR_DNSLOOKUP_RESOLVE_TIMEOUT) {
                    $this->setError(CURLE_COULDNT_RESOLVE_HOST, 'Could not resolve host: ' . $client->host);
                }
                $this->info['total_time'] = microtime(true) - $timeBegin;
                return false;
            }
            if ($client->statusCode >= 300 and $client->statusCode < 400 and isset($client->headers['location'])) {
                $redirectParsedUrl = $this->getRedirectUrl($client->headers['location']);
                $redirectUrl = static::unparseUrl($redirectParsedUrl);
                if ($this->followLocation and (null === $this->maxRedirs or $this->info['redirect_count'] < $this->maxRedirs)) {
                    if ($this->info['redirect_count'] === 0) {
                        $this->info['starttransfer_time'] = microtime(true) - $timeBegin;
                        $redirectBeginTime = microtime(true);
                    }
                    // force GET
                    if (in_array($client->statusCode, [Status::MOVED_PERMANENTLY, Status::FOUND, Status::SEE_OTHER])) {
                        $this->method = 'GET';
                    }
                    if ($this->autoReferer) {
                        $this->headers['Referer'] = $this->info['url'];
                    }
                    $this->setUrl($redirectUrl, false);
                    $this->setUrlInfo($redirectParsedUrl);
                    $this->info['redirect_count']++;
                } else {
                    $this->info['redirect_url'] = $redirectUrl;
                    break;
                }
            } else {
                break;
            }
        } while (true);
        $this->info['total_time'] = microtime(true) - $timeBegin;
        $this->info['http_code'] = $client->statusCode;
        $this->info['content_type'] = $client->headers['content-type'] ?? '';
        $this->info['size_download'] = $this->info['download_content_length'] = strlen($client->body);;
        $this->info['speed_download'] = 1 / $this->info['total_time'] * $this->info['size_download'];
        if (isset($redirectBeginTime)) {
            $this->info['redirect_time'] = microtime(true) - $redirectBeginTime;
        }

        $headerContent = '';
        if ($client->headers) {
            $cb = $this->headerFunction;
            if ($client->statusCode > 0) {
                $row = "HTTP/1.1 {$client->statusCode} " . Status::getReasonPhrase($client->statusCode) . "\r\n";
                if ($cb) {
                    $cb($this, $row);
                }
                $headerContent .= $row;
            }
            foreach ($client->headers as $k => $v) {
                $row = "{$k}: {$v}\r\n";
                if ($cb) {
                    $cb($this, $row);
                }
                $headerContent .= $row;
            }
            $headerContent .= "\r\n";
            $this->info['header_size'] = strlen($headerContent);
            if ($cb) {
                $cb($this, '');
            }
        } else {
            $this->info['header_size'] = 0;
        }

        if ($client->body and $this->readFunction) {
            $cb = $this->readFunction;
            $cb($this, $this->outputStream, strlen($client->body));
        }

        if ($this->withHeader) {
            $transfer = $headerContent . $client->body;
        } else {
            $transfer = $client->body;
        }

        if ($this->withHeaderOut) {
            $headerOutContent = $client->getHeaderOut();
            $this->info['request_header'] = $headerOutContent ? $headerOutContent . "\r\n\r\n" : '';
        }
        if ($this->withFileTime) {
            if (isset($client->headers['last-modified'])) {
                $this->info['filetime'] = strtotime($client->headers['last-modified']);
            } else {
                $this->info['filetime'] = -1;
            }
        }

        if ($this->returnTransfer) {
            return $this->transfer = $transfer;
        } else {
            if ($this->outputStream) {
                return fwrite($this->outputStream, $transfer) === strlen($transfer);
            } else {
                echo $transfer;
            }
            return true;
        }
    }

    public function close(): void
    {
        $this->client = null;
    }

    private function setError($code, $msg = ''): void
    {
        $this->errCode = $code;
        $this->errMsg = $msg ? $msg : curl_strerror($code);
    }

    private function getUrl(): string
    {
        if (empty($this->urlInfo['path'])) {
            $url = '/';
        } else {
            $url = $this->urlInfo['path'];
        }
        if (!empty($this->urlInfo['query'])) {
            $url .= '?' . $this->urlInfo['query'];
        }
        if (!empty($this->urlInfo['fragment'])) {
            $url .= '#' . $this->urlInfo['fragment'];
        }
        return $url;
    }

    /**
     * @param int $opt
     * @param mixed $value
     * @return bool
     * @throws Swoole\Curl\Exception
     */
    public function setOption(int $opt, $value): bool
    {
        switch ($opt) {
            // case CURLOPT_STDERR:
            // case CURLOPT_WRITEHEADER:
            case CURLOPT_FILE:
            case CURLOPT_INFILE:
                if (!is_resource($value)) {
                    trigger_error(E_USER_WARNING, 'swoole_curl_setopt(): supplied argument is not a valid File-Handle resource');
                    return false;
                }
                break;
        }

        switch ($opt) {
            /**
             * Basic
             */
            case CURLOPT_URL:
                return $this->setUrl($value);
            case CURLOPT_PORT:
                $this->setPort($value);
                break;
            case CURLOPT_RETURNTRANSFER:
                $this->returnTransfer = $value;
                $this->transfer = '';
                break;
            case CURLOPT_ENCODING:
                if (empty($value)) {
                    if (defined('SWOOLE_HAVE_ZLIB')) {
                        $value = 'gzip, deflate';
                    }
                    if (defined('SWOOLE_HAVE_BROTLI')) {
                        if (!empty($value)) {
                            $value = 'br, ' . $value;
                        } else {
                            $value = 'br';
                        }
                    }
                    if (empty($value)) {
                        break;
                    }
                }
                $this->headers['Accept-Encoding'] = $value;
                break;
            case CURLOPT_PROXYTYPE:
                if ($value !== CURLPROXY_HTTP and $value !== CURLPROXY_SOCKS5) {
                    throw new Swoole\Curl\Exception(
                        'swoole_curl_setopt(): Only support following CURLOPT_PROXYTYPE values: CURLPROXY_HTTP, CURLPROXY_SOCKS5'
                    );
                }
                $this->proxy_type = $value;
                break;
            case CURLOPT_PROXY:
                $this->proxy = $value;
                break;
            case CURLOPT_PROXYPORT:
                $this->proxy_port = $value;
                break;
            case CURLOPT_PROXYUSERNAME:
                $this->proxy_username = $value;
                break;
            case CURLOPT_PROXYPASSWORD:
                $this->proxy_password = $value;
                break;
            case CURLOPT_PROXYUSERPWD:
                $user_pwd = explode(':', $value);
                $this->proxy_username = urldecode($user_pwd[0]);
                $this->proxy_password = urldecode($user_pwd[1] ?? null);
                break;
            case CURLOPT_PROXYAUTH:
                /* ignored temporarily */
                break;
            case CURLOPT_NOBODY:
                $this->nobody = boolval($value);
                $this->method = 'HEAD';
                break;
            case CURLOPT_IPRESOLVE:
                if ($value !== CURL_IPRESOLVE_WHATEVER and $value !== CURL_IPRESOLVE_V4) {
                    throw new Swoole\Curl\Exception(
                        'swoole_curl_setopt(): Only support following CURLOPT_IPRESOLVE values: CURL_IPRESOLVE_WHATEVER, CURL_IPRESOLVE_V4'
                    );
                }
                break;
            /**
             * Ignore options
             */
            case CURLOPT_VERBOSE:
                // trigger_error(E_USER_WARNING, 'swoole_curl_setopt(): CURLOPT_VERBOSE is not supported');
            case CURLOPT_SSLVERSION:
            case CURLOPT_NOSIGNAL:
            case CURLOPT_FRESH_CONNECT:
                /**
                 * From PHP 5.1.3, this option has no effect: the raw output will always be returned when CURLOPT_RETURNTRANSFER is used.
                 */
            case CURLOPT_BINARYTRANSFER: /* TODO */
            case CURLOPT_DNS_USE_GLOBAL_CACHE:
            case CURLOPT_DNS_CACHE_TIMEOUT:
            case CURLOPT_STDERR:
            case CURLOPT_WRITEHEADER:
            case CURLOPT_BUFFERSIZE:
                break;
            /**
             * SSL
             */
            case CURLOPT_SSL_VERIFYHOST:
                break;
            case CURLOPT_SSL_VERIFYPEER:
                $this->clientOptions['ssl_verify_peer'] = $value;
                break;
            /**
             * Http Post
             */
            case CURLOPT_POST:
                $this->method = 'POST';
                break;
            case CURLOPT_POSTFIELDS:
                $this->postData = $value;
                $this->method = 'POST';
                break;
            /**
             * Upload
             */
            case CURLOPT_SAFE_UPLOAD:
                if (!$value) {
                    trigger_error('swoole_curl_setopt(): Disabling safe uploads is no longer supported', E_USER_WARNING);
                    return false;
                }
                break;
            /**
             * Http Header
             */
            case CURLOPT_HTTPHEADER:
                if (!is_array($value) and !($value instanceof Iterator)) {
                    trigger_error('swoole_curl_setopt(): You must pass either an object or an array with the CURLOPT_HTTPHEADER argument', E_USER_WARNING);
                    return false;
                }
                foreach ($value as $header) {
                    list($k, $v) = explode(':', $header);
                    $v = trim($v);
                    if ($v) {
                        $this->headers[$k] = $v;
                    }
                }
                break;
            case CURLOPT_REFERER:
                $this->headers['Referer'] = $value;
                break;
            case CURLINFO_HEADER_OUT:
                $this->withHeaderOut = boolval($value);
                break;
            case CURLOPT_FILETIME:
                $this->withFileTime = boolval($value);
                break;
            case CURLOPT_USERAGENT:
                $this->headers['User-Agent'] = $value;
                break;
            case CURLOPT_CUSTOMREQUEST:
                $this->method = (string)$value;
                break;
            case CURLOPT_PROTOCOLS:
                if ($value > 3) {
                    throw new CurlException("swoole_curl_setopt(): CURLOPT_PROTOCOLS[{$value}] is not supported");
                }
                break;
            case CURLOPT_HTTP_VERSION:
                if ($value != CURL_HTTP_VERSION_1_1) {
                    trigger_error("swoole_curl_setopt(): CURLOPT_HTTP_VERSION[{$value}] is not supported", E_USER_WARNING);
                    return false;
                }
                break;
            /**
             * Http Cookie
             */
            case CURLOPT_COOKIE:
                $this->headers['Cookie'] = $value;
                break;
            case CURLOPT_CONNECTTIMEOUT:
                $this->clientOptions['connect_timeout'] = $value;
                break;
            case CURLOPT_CONNECTTIMEOUT_MS:
                $this->clientOptions['connect_timeout'] = $value / 1000;
                break;
            case CURLOPT_TIMEOUT:
                $this->clientOptions['timeout'] = $value;
                break;
            case CURLOPT_TIMEOUT_MS:
                $this->clientOptions['timeout'] = $value / 1000;
                break;
            case CURLOPT_FILE:
                $this->outputStream = $value;
                break;
            case CURLOPT_HEADER:
                $this->withHeader = $value;
                break;
            case CURLOPT_HEADERFUNCTION:
                $this->headerFunction = $value;
                break;
            case CURLOPT_READFUNCTION:
                $this->readFunction = $value;
                break;
            case CURLOPT_WRITEFUNCTION:
                $this->writeFunction = $value;
                break;
            case CURLOPT_PROGRESSFUNCTION:
                $this->progressFunction = $value;
                break;
            case CURLOPT_USERPWD:
                $this->headers['Authorization'] = 'Basic ' . base64_encode($value);
                break;
            case CURLOPT_FOLLOWLOCATION:
                $this->followLocation = $value;
                break;
            case CURLOPT_AUTOREFERER:
                $this->autoReferer = $value;
                break;
            case CURLOPT_MAXREDIRS:
                $this->maxRedirs = $value;
                break;
            case CURLOPT_PUT:
                $this->method = 'PUT';
                break;
            case CURLOPT_INFILE:
                $this->infile = $value;
                break;
            case CURLOPT_INFILESIZE:
                $this->infileSize = $value;
                break;
            default:
                throw new Swoole\Curl\Exception("swoole_curl_setopt(): option[{$opt}] is not supported");
        }
        return true;
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function reset(): void
    {
        foreach ((new ReflectionClass(static::class))->getDefaultProperties() as $name => $value) {
            $this->$name = $value;
        }
    }

    private static function unparseUrl(array $parsedUrl): string
    {
        $scheme = ($parsedUrl['scheme'] ?? 'http') . '://';
        $host = $parsedUrl['host'] ?? '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = $parsedUrl['user'] ?? '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user or $pass) ? "$pass@" : '';
        $path = $parsedUrl['path'] ?? '';
        $query = (isset($parsedUrl['query']) and '' !== $parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    private function getRedirectUrl(string $location): array
    {
        $uri = parse_url($location);
        if (isset($uri['host'])) {
            $redirectUri = $uri;
        } else {
            if (!isset($location[0])) {
                return [];
            }
            $redirectUri = $this->urlInfo;
            $redirectUri['query'] = '';
            if ('/' === $location[0]) {
                $redirectUri['path'] = $location;
            } else {
                $path = dirname($redirectUri['path'] ?? '');
                if ('.' === $path) {
                    $path = '/';
                }
                if (isset($location[1]) and './' === substr($location, 0, 2)) {
                    $location = substr($location, 2);
                }
                $redirectUri['path'] = $path . $location;
            }
            foreach ($uri as $k => $v) {
                if (!in_array($k, ['path', 'query'])) {
                    $redirectUri[$k] = $v;
                }
            }
        }
        return $redirectUri;
    }
}