<?php

namespace Nicolasps\UsersAPI\Meta;

class Request
{
    private string $json = '';

    private string $xml = '';

    private mixed $data;

    public function __construct()
    {
        $jsonInput = json_decode(file_get_contents('php://input'));
        if (! empty($jsonInput)) {
            $this->json = file_get_contents('php://input');
        }

        $xmlInput = xml_decode(file_get_contents('php://input'));
        if (! empty($xmlInput)) {
            $this->xml = file_get_contents('php://input');
        }

        $this->data = (object) $_REQUEST;
    }

    public function json(): string
    {
        return $this->json;
    }

    public function xml(): string
    {
        return $this->xml;
    }

    public function all(): object
    {
        return $this->data;
    }

    public function set(string $key = '', mixed $value): void
    {
        $this->data->$key = $value;
    }
}