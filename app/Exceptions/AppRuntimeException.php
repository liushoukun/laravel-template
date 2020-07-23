<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: shook Liu  |  Email:24147287@qq.com  | Time: 2018/8/29/029 13:52
// +----------------------------------------------------------------------
// | TITLE: 异常类
// +----------------------------------------------------------------------

namespace App\Exceptions;

use App\Http\Controllers\Traits\Response;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


abstract class AppRuntimeException extends RuntimeException implements HttpExceptionInterface
{

    use Response;

    protected $codePrefix;
    protected $errors;
    protected $status_code;
    protected $headers;
    protected $data;

    protected $time;


    public function __construct($code = 0, $message = '', $errors = [], $status_code = 400, $headers = [], $data = [])
    {
        $code              = sprintf("%02d", $code);
        $code              = (int)($this->codePrefix . (string)$code);
        $this->status_code = $status_code;
        $this->code        = (int)($code);
        $this->message     = $this->getDefaultMessage($code, $message);
        $this->errors      = $errors;
        $this->headers     = $headers;
        $this->data        = $data;
        $this->time        = $this->getMessage();
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return microtime(true) - LARAVEL_START;
    }


    public function getDefaultMessage($code, $message = '')
    {

        if (filled($message)) {
            return $message;
        }
        return isset(self::$errorList[$code]) ? self::$errorList[$code] : '';
    }


    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->data;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function getHeaders()
    {
        return $this->headers;
    }


    public static function render(Request $request, self $exception)
    {
        return $request->expectsJson() ? self::invalidJson($exception) : self::invalid($request, $exception);
    }


    protected static function invalid(Request $request, self $exception)
    {
        return redirect($request->getRequestUri() ?? url()->previous())
            ->withErrors(new MessageBag([ 'message' => $exception->getMessage() ]));
    }

    public static function invalidJson(self $exception)
    {
        $response = self::error($exception->getMessage(), $exception->getCode(), $exception->getStatusCode(), $exception->errors, $exception->getData());
        if (app()->environment([ 'local', 'testing' ])) {
            $data['debug']['line']  = $exception->getLine();
            $data['debug']['file']  = $exception->getFile();
            $data['debug']['trace'] = $exception->getTraceAsString();
            $response               = $response->setData(array_merge($response->getData(true), $data));
        }
        $response = $response->withHeaders($exception->getHeaders());

        return $response;
    }

    public static $errorList = [

    ];
}
