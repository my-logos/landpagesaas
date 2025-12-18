# Tests Documentation

## Overview

This test suite contains comprehensive tests for the Landing Page SaaS platform, covering authentication, routing, models, and business logic.

## Test Statistics

- **Total Tests**: 32
- **Total Assertions**: 63
- **Status**: ✅ All tests passing

## Test Suites

### Feature Tests (16 tests)

#### AuthTest (7 tests)
- Tests authentication flow
- Validates login/logout functionality
- Tests inactive user blocking
- Validates credential validation

#### RoutesTest (6 tests)
- Tests route accessibility
- Validates authentication requirements
- Tests role-based access control
- Validates inactive user restrictions

#### BasicRoutesTest (3 tests)
- Tests basic route functionality
- Validates dashboard access

### Unit Tests (16 tests)

#### SubscriptionServiceTest (4 tests)
- Tests free package subscription creation
- Tests paid package subscription (pending status)
- Tests package slug support
- Tests error handling for non-existent packages

#### ModelRelationsTest (6 tests)
- Tests Eloquent relationships (User-Subscription-Package)
- Tests User model methods (isAdmin)
- Tests Package model methods (hasFeature)

#### ModelTest (4 tests)
- Tests model creation
- Tests model methods and attributes

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test --filter=AuthTest
php artisan test --filter=RoutesTest
php artisan test --filter=SubscriptionServiceTest
```

### Run Specific Test
```bash
php artisan test --filter=test_user_can_login_with_valid_credentials
```

## Test Environment

- **Database**: SQLite in-memory (for speed)
- **Environment**: `testing` (automatically set by PHPUnit)
- **reCAPTCHA**: Disabled in test environment
- **Migrations**: MySQL-specific migrations skip automatically for SQLite

## Notes

- Tests use `RefreshDatabase` trait to ensure clean state for each test
- reCAPTCHA validation is automatically disabled in test environment
- Some MySQL-specific migrations (using `SHOW COLUMNS`) skip automatically when using SQLite
- All tests are designed to work without modifying production code

## Test Coverage

### Covered Areas:
- ✅ Authentication & Authorization
- ✅ Route Protection
- ✅ Model Relationships
- ✅ Business Logic (Subscription Service)
- ✅ Package Limit Enforcement
- ✅ User Roles & Permissions

### Future Improvements:
- Add tests for Payment Controller
- Add tests for Page Generation Service
- Add tests for AI Service
- Add integration tests for full user flows
