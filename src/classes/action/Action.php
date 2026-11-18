<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

abstract class Action
{
    protected string $http_method;
    protected string $hostname;
    protected string $script_name;

    public function __construct()
    {
        $this->http_method = 'GET';
        $this->hostname = 'localhost';
        $this->script_name = 'index.php';

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->http_method = $_SERVER['REQUEST_METHOD'];
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->hostname = $_SERVER['HTTP_HOST'];
        }
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $this->script_name = $_SERVER['SCRIPT_NAME'];
        }
    }

    protected function e(string $value): string
    {
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    abstract public function execute(): string;
}
