<?php

namespace Spwa\Http;


class HttpResponse
{
    /**
     * @param int $code
     * @param array $headers
     * @param (string|():string) $payload
     */
    public function __construct(private int $code, private array $headers, private $payload)
    {
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    function send(): void
    {
        http_response_code($this->code);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        if (is_string($this->payload)) {
            echo $this->payload;
            return;
        }

        if (is_callable($this->payload))
            echo ($this->payload)();
    }

    public static function json(array $data): HttpResponse
    {
//        $headers = HttpHeaders::create()
//            ->contentType("application/json");
        return new HttpResponse(200, ["Content-Type" => "application/json"], fn() => json_encode($data));
    }

    public static function html(string|callable $html): HttpResponse
    {
        return new HttpResponse(200, ["Content-Type" => "text/html"], is_string($html) ? fn() => $html : $html);
    }

    public static function notFound(): HttpResponse
    {
        return new HttpResponse(404, ["Content-Type" => "text/html"], fn() => "<h1>Not Found</h1>");
    }

    public static function file(string $path, string $contentType): HttpResponse
    {
        return new HttpResponse(200, ["Content-Type" => $contentType], fn() => file_get_contents($path));
    }

    public static function error(string $message): HttpResponse
    {
        return new HttpResponse(500, ["Content-Type" => "text/html"], fn() => "<h1>$message</h1>");
    }

    public static function redirect(string $location): HttpResponse
    {
        return new HttpResponse(302, ["Location" => $location], "");
    }


}

