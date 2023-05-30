<?php

namespace Nicolasps\UsersAPI\Meta;

enum HTTPResponseCodes: int
{
    case Ok = 200;
    case NotFound = 404;
    case BadRequest = 400;
    case NoContent = 204;
    case ServerError = 500;
    case Unauthorized = 401;
    case MethodNotAllowed = 405;

    public static function getHeaderMessage(self $httpResponseCode): string
    {
        return match ($httpResponseCode) {
            HTTPResponseCodes::Ok => "HTTP/1.0 200 OK",
            HTTPResponseCodes::NotFound => "HTTP/1.0 404 Not Found",
            HTTPResponseCodes::BadRequest => "HTTP/1.0 400 Bad Request",
            HTTPResponseCodes::NoContent => "HTTP/1.0 204 No Content",
            HTTPResponseCodes::ServerError => "HTTP/1.0 500 Server Error",
            HTTPResponseCodes::Unauthorized => "HTTP/1.0 401 Unauthorized",
            HTTPResponseCodes::MethodNotAllowed => "HTTP/1.0 405 Method Not Allowed",
        };
    }
}
