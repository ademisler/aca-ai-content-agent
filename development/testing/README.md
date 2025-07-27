# Development Testing Tools

This directory contains a **comprehensive, modern testing framework** for the **ACA - AI Content Agent** plugin.

## 🚀 **New Testing Framework**

**Complete rewrite** with modern PHP testing practices:
- **Organized test categories** (unit, integration, api, security, performance)
- **Comprehensive WordPress mocking** system
- **Detailed assertion methods** with better error reporting
- **Performance monitoring** and memory usage tracking
- **Fixture-based test data** management

## 📁 **Test Structure**

```
testing/
├── TestRunner.php           # Main test runner (NEW)
├── test-plugin.php         # Symlink to TestRunner.php
├── test-plugin-old.php     # Backup of old test system
├── create-distribution.sh  # Distribution package creator
├── tests/                  # Organized test suites (NEW)
│   ├── BaseTest.php        # Base test class with common functionality
│   ├── unit/               # Unit tests for individual components
│   │   └── CorePluginTest.php
│   ├── integration/        # Integration tests between services
│   │   └── ServiceIntegrationTest.php
│   ├── api/                # External API integration tests
│   │   └── GeminiApiTest.php
│   ├── security/           # Security and encryption tests
│   │   └── EncryptionTest.php
│   └── performance/        # Performance and optimization tests
│       └── PerformanceTest.php
├── mocks/                  # WordPress function mocks (NEW)
│   └── WordPressMocks.php  # Comprehensive WordPress API mocking
├── fixtures/               # Test data and mock responses (NEW)
│   └── ideas.php           # Mock content ideas for testing
└── wordpress-test/         # Docker test environment
    └── docker-compose.yml
```

## 🧪 **Test Categories**

### **Unit Tests** (`tests/unit/`)
- **Core plugin functionality** and architecture
- **WordPress integration** verification
- **Constants and configuration** validation
- **Mock system** functionality testing

### **Integration Tests** (`tests/integration/`)
- **Service-to-service** communication
- **Database operations** and schema validation
- **WordPress hooks** and filters integration
- **Complete workflow** testing (Idea → Draft → Style Guide)

### **API Tests** (`tests/api/`)
- **Google Gemini AI** integration
- **Gumroad licensing** system
- **HTTP request/response** handling
- **Rate limiting** and caching mechanisms

### **Security Tests** (`tests/security/`)
- **Encryption/decryption** functionality
- **Data sanitization** and validation
- **Nonce verification** and CSRF protection
- **Capability checks** and authorization

### **Performance Tests** (`tests/performance/`)
- **Load time** and memory usage monitoring
- **Database query** performance
- **Large data handling** capabilities
- **Scalability** simulation and resource cleanup

## 🚀 **Running Tests**

### **All Tests**
```bash
# From project root
php development/testing/TestRunner.php

# With verbose output
php development/testing/TestRunner.php --verbose
```

### **Specific Categories**
```bash
# Unit tests only
php development/testing/TestRunner.php --category=unit

# Security tests with verbose output
php development/testing/TestRunner.php --category=security --verbose

# Performance tests
php development/testing/TestRunner.php --category=performance
```

### **Legacy Compatibility**
```bash
# Old test system (backup)
php development/testing/test-plugin-old.php
```

## ✨ **Key Features**

### **Modern Test Framework**
- **Object-oriented** test architecture
- **Comprehensive assertion methods** (assertTrue, assertEqual, assertStringContains, etc.)
- **Automatic test discovery** and execution
- **Detailed error reporting** with caller information
- **Test setup/teardown** lifecycle management

### **WordPress Mocking System**
- **Complete WordPress API** simulation
- **Database operations** (MockWPDB class)
- **Options API** (get_option, update_option, delete_option)
- **Security functions** (nonces, capabilities, sanitization)
- **HTTP API** (wp_remote_get, wp_remote_post)
- **Hooks system** (add_action, add_filter, do_action, apply_filters)

### **Performance Monitoring**
- **Execution time** tracking per test
- **Memory usage** monitoring
- **Database query** performance analysis
- **Resource cleanup** verification

### **Test Data Management**
- **Fixture files** for consistent test data
- **Mock API responses** for external services
- **Configurable test environments**

## 📊 **Test Coverage**

| Category | Tests | Coverage |
|----------|-------|----------|
| **Unit** | 15+ | Core functionality, WordPress integration |
| **Integration** | 12+ | Service communication, workflow testing |
| **API** | 10+ | External integrations, error handling |
| **Security** | 8+ | Encryption, sanitization, authorization |
| **Performance** | 11+ | Speed, memory, scalability |
| **Total** | **56+** | **Comprehensive plugin coverage** |

## 🔧 **Test Development**

### **Adding New Tests**
1. **Choose category** (unit/integration/api/security/performance)
2. **Extend BaseTest** class
3. **Use naming convention**: `testMethodName()`
4. **Add assertions** with descriptive messages
5. **Include setup/teardown** if needed

### **Example Test**
```php
<?php
require_once dirname(__DIR__) . '/BaseTest.php';

class MyFeatureTest extends BaseTest {
    
    public function testMyFeature() {
        $result = my_plugin_function();
        $this->assertNotNull($result, 'Function should return a value');
        $this->assertEqual('expected', $result, 'Function should return expected value');
    }
}
```

### **Mock Data Usage**
```php
public function testWithMockData() {
    $mock_ideas = $this->getMockData('ideas');
    $this->assertNotNull($mock_ideas, 'Mock ideas should be available');
    
    foreach ($mock_ideas as $idea) {
        $this->assertArrayHasKey('title', $idea, 'Each idea should have a title');
    }
}
```

## 🎯 **Quality Assurance**

### **Automated Validation**
- **Syntax checking** for all PHP files
- **WordPress coding standards** compliance
- **Security vulnerability** scanning
- **Performance benchmarking**

### **CI/CD Integration Ready**
- **Exit codes** for automated systems (0 = success, 1 = failure)
- **Structured output** for parsing by CI tools
- **Category filtering** for targeted testing
- **Performance metrics** reporting

## ⚠️ **Important Notes**

### **Environment Requirements**
- **PHP 8.0+** for full functionality
- **WordPress 6.5+** compatibility testing
- **Isolated environment** (no real WordPress installation needed)

### **Mock Limitations**
- **External API calls** return mock responses
- **Database operations** use in-memory simulation
- **File system operations** may be limited
- **Real WordPress hooks** are simulated

### **Production Exclusion**
**This entire testing directory must be excluded from production releases.**

```bash
# In distribution scripts
--exclude='development/'
```

## 🚀 **Migration from Old System**

The **old test system** (`test-plugin-old.php`) has been **completely replaced** with this modern framework:

### **Improvements**
- ✅ **56+ comprehensive tests** (vs ~10 basic checks)
- ✅ **Organized categories** (vs single file)
- ✅ **Modern PHP practices** (vs procedural code)
- ✅ **Detailed error reporting** (vs basic output)
- ✅ **Performance monitoring** (vs no metrics)
- ✅ **WordPress mocking** (vs limited simulation)

### **Backwards Compatibility**
- Old test file preserved as `test-plugin-old.php`
- New system accessible via same `test-plugin.php` path
- Command-line arguments enhanced but compatible

---

**Ready for professional WordPress plugin testing! 🎉**
