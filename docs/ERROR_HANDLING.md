# Laravel API Error Handling

## Overview

This module provides centralized exception handling for Laravel 12 APIs. It standardizes JSON error responses, supports custom business exceptions, and prevents unexpected internal error details from being exposed to API clients.

## Standard Error Response

All handled API errors follow this structure:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": []
  }
}
```

## Supported Error Types

| HTTP Status | Error Code | Description |
|---|---|---|
| 400–499 | HTTP_ERROR | Other handled HTTP exceptions |
| 401 | UNAUTHENTICATED | Authentication required |
| 404 | RESOURCE_NOT_FOUND | Requested resource does not exist |
| 409 | BUSINESS_RULE_VIOLATION | Default business rule violation |
| 422 | VALIDATION_ERROR | Invalid request data |
| 500 | INTERNAL_SERVER_ERROR | Unexpected server error |

Custom business exceptions can supply a more specific error code, such as `INSUFFICIENT_STOCK`.

## Core Components

- `app/Support/ApiErrorResponse.php` — Generates standardized JSON responses.
- `app/Exceptions/BusinessException.php` — Represents application-specific business rule violations.
- `bootstrap/app.php` — Registers centralized exception handling for API routes.
- `tests/Feature/ApiErrorHandlingTest.php` — Verifies the error response contract.

## Throwing a Business Exception

```php
use App\Exceptions\BusinessException;

throw new BusinessException(
    message: 'Insufficient inventory',
    errorCode: 'INSUFFICIENT_STOCK',
    httpStatus: 409
);
```

## Integration

1. Copy `ApiErrorResponse.php` into `app/Support/`.
2. Copy `BusinessException.php` into `app/Exceptions/`.
3. Register the exception rendering logic in `bootstrap/app.php`.
4. Apply the standardized handler to API routes.
5. Add automated tests for the expected error responses.

## Logging and Security

Unexpected exceptions return a generic client-facing message. Laravel's exception logging records diagnostic information, including the original exception and stack trace.

For production:

```dotenv
APP_DEBUG=false
LOG_LEVEL=error
```

Do not include passwords, authentication tokens, or other sensitive values in exception messages or custom logging context. Review logging configuration and exception reporting before deployment.

## Testing

Run all automated tests:

```bash
php artisan test
```

Run the API error-handling tests:

```bash
php artisan test --filter=ApiErrorHandlingTest
```

The tests cover validation, authentication, missing resources, business exceptions, and unexpected server errors.
