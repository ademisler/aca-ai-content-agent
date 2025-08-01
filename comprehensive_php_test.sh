#!/bin/bash

# Comprehensive PHP Test Script - Enhanced analysis
echo "=== KAPSAMLI PHP DOSYALARI TEST RAPORU ===" > bug/PHPtest.md
echo "Test Tarihi: $(date)" >> bug/PHPtest.md
echo "PHP Sürümü: $(php --version | head -n1)" >> bug/PHPtest.md
echo "Test Araçları: PHPStan, PHPCS, PHPMD, Manuel Güvenlik Taraması" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# PHP files to test
files=(
    "/workspace/ai-content-agent-plugin/install-dependencies.php"
    "/workspace/ai-content-agent-plugin/vendor/autoload.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-activator.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-migration-manager.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-google-search-console.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-cron.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-rest-api.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-deactivator.php"
    "/workspace/ai-content-agent-plugin/includes/class-aca-content-freshness.php"
    "/workspace/ai-content-agent-plugin/uninstall.php"
    "/workspace/ai-content-agent-plugin/ai-content-agent.php"
)

# Additional files to analyze
additional_files=(
    "/workspace/ai-content-agent-plugin/package.json"
    "/workspace/ai-content-agent-plugin/composer.json"
    "/workspace/ai-content-agent-plugin/vite.config.ts"
)

echo "## 1. SYNTAX VE TEMEL KONTROLLER" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

total_files=0
syntax_errors=0
critical_errors=0
warnings=0
security_issues=0

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        total_files=$((total_files + 1))
        echo "### $(basename "$file")" >> bug/PHPtest.md
        echo "**Dosya:** $file" >> bug/PHPtest.md
        
        # Basic file info
        file_size=$(stat -c%s "$file")
        line_count=$(wc -l < "$file")
        echo "- Boyut: $file_size bytes | Satır: $line_count" >> bug/PHPtest.md
        
        # Syntax check
        syntax_result=$(php -l "$file" 2>&1)
        if [[ $syntax_result == *"No syntax errors"* ]]; then
            echo "✅ Syntax OK" >> bug/PHPtest.md
        else
            echo "❌ **SYNTAX ERROR**: $(echo "$syntax_result" | grep -o "line [0-9]*")" >> bug/PHPtest.md
            syntax_errors=$((syntax_errors + 1))
            critical_errors=$((critical_errors + 1))
        fi
        
        # Security analysis
        security_count=0
        
        # Check for dangerous functions
        if grep -qi "eval\|exec\|system\|shell_exec\|passthru\|file_get_contents.*http" "$file"; then
            echo "🔴 **Tehlikeli fonksiyon kullanımı**" >> bug/PHPtest.md
            security_count=$((security_count + 1))
        fi
        
        # Check for user input without sanitization
        if grep -qi "\$_GET\|\$_POST\|\$_REQUEST" "$file" && ! grep -qi "sanitize\|wp_verify_nonce\|esc_" "$file"; then
            echo "🟡 **Sanitizasyon eksik olabilir**" >> bug/PHPtest.md
            security_count=$((security_count + 1))
        fi
        
        # Check for SQL injection risks
        if grep -qi "query.*\$\|mysql_query\|mysqli_query.*\$" "$file"; then
            echo "🔴 **SQL injection riski**" >> bug/PHPtest.md
            security_count=$((security_count + 1))
        fi
        
        # Check for file inclusion risks
        if grep -qi "include.*\$\|require.*\$" "$file"; then
            echo "🟡 **Dinamik include/require**" >> bug/PHPtest.md
            security_count=$((security_count + 1))
        fi
        
        # Check for deprecated functions
        if grep -qi "mysql_\|ereg\|split\|create_function" "$file"; then
            echo "🟠 **Deprecated fonksiyon**" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        # Check for proper practices
        if ! grep -q "namespace " "$file" && grep -q "class " "$file"; then
            echo "🟡 **Namespace eksik**" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        if ! grep -qi "try\|catch\|throw" "$file"; then
            echo "🟡 **Exception handling yok**" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        if [ $security_count -gt 0 ]; then
            security_issues=$((security_issues + 1))
        fi
        
        echo "" >> bug/PHPtest.md
    fi
done

echo "## 2. PHPSTAN STATIC ANALYSIS (ÖZET)" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPStan analysis - only show summary
echo "PHPStan analizi çalıştırılıyor..." 
phpstan_output=$(./vendor/bin/phpstan analyse ai-content-agent-plugin/ --level=5 --no-progress 2>&1)
phpstan_errors=$(echo "$phpstan_output" | grep -c "ERROR" || echo "0")
phpstan_warnings=$(echo "$phpstan_output" | grep -c "WARNING" || echo "0")

echo "**PHPStan Sonuçları:**" >> bug/PHPtest.md
echo "- Hatalar: $phpstan_errors" >> bug/PHPtest.md
echo "- Uyarılar: $phpstan_warnings" >> bug/PHPtest.md

if [ $phpstan_errors -gt 0 ]; then
    echo "" >> bug/PHPtest.md
    echo "**Başlıca Hatalar:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpstan_output" | head -20 >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 3. PHPCS CODE STYLE (ÖZET)" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPCS analysis - only show summary  
echo "PHPCS kod stili analizi çalıştırılıyor..."
phpcs_output=$(./vendor/bin/phpcs ai-content-agent-plugin/ --standard=PSR12 --report=summary 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **Kod stili temiz**" >> bug/PHPtest.md
else
    echo "🟡 **Kod stili problemleri var**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpcs_output" | head -15 >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 4. SECURITY DEEP SCAN" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Deep security analysis
echo "**Detaylı Güvenlik Taraması:**" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Check for hardcoded credentials
cred_files=$(grep -r -i "password\|secret\|key.*=" ai-content-agent-plugin/ --include="*.php" | head -5)
if [ ! -z "$cred_files" ]; then
    echo "🔴 **Potansiyel hardcoded credentials:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$cred_files" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi

# Check for debug information
debug_files=$(grep -r -i "var_dump\|print_r\|error_reporting\|ini_set.*display" ai-content-agent-plugin/ --include="*.php" | head -5)
if [ ! -z "$debug_files" ]; then
    echo "🟡 **Debug bilgileri:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$debug_files" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi

# Check for file operations
file_ops=$(grep -r -i "fopen\|file_put_contents\|unlink\|chmod" ai-content-agent-plugin/ --include="*.php" | head -5)
if [ ! -z "$file_ops" ]; then
    echo "🟡 **Dosya işlemleri:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$file_ops" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi

echo "" >> bug/PHPtest.md

echo "## 5. PERFORMANCE ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Performance analysis
large_files=0
complex_files=0

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        file_size=$(stat -c%s "$file")
        line_count=$(wc -l < "$file")
        
        if [ $file_size -gt 50000 ]; then
            echo "⚠️ **$(basename "$file")**: Büyük dosya ($file_size bytes)" >> bug/PHPtest.md
            large_files=$((large_files + 1))
        fi
        
        if [ $line_count -gt 500 ]; then
            echo "⚠️ **$(basename "$file")**: Karmaşık dosya ($line_count satır)" >> bug/PHPtest.md
            complex_files=$((complex_files + 1))
        fi
        
        # Check for potential performance issues
        if grep -qi "while.*true\|for.*;;.*{" "$file"; then
            echo "🔴 **$(basename "$file")**: Sonsuz döngü riski" >> bug/PHPtest.md
        fi
        
        # Check for inefficient patterns
        if grep -qi "mysql_connect\|mysql_pconnect" "$file"; then
            echo "🟠 **$(basename "$file")**: Eski MySQL API" >> bug/PHPtest.md
        fi
        
        # Check for memory intensive operations
        if grep -qi "file_get_contents.*http\|curl_exec" "$file"; then
            echo "🟡 **$(basename "$file")**: HTTP istekleri (timeout kontrolü gerekli)" >> bug/PHPtest.md
        fi
    fi
done

echo "" >> bug/PHPtest.md

echo "## 6. CONFIGURATION FILES ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Analyze additional files
for file in "${additional_files[@]}"; do
    if [ -f "$file" ]; then
        echo "### $(basename "$file")" >> bug/PHPtest.md
        
        case "$file" in
            *.json)
                # Check for security issues in JSON
                if grep -qi "password\|secret\|key.*:" "$file"; then
                    echo "🔴 **Potansiyel credentials**" >> bug/PHPtest.md
                fi
                if grep -qi "http://\|localhost" "$file"; then
                    echo "🟡 **Development URLs**" >> bug/PHPtest.md
                fi
                ;;
            *.ts|*.js)
                # Check TypeScript/JavaScript config
                if grep -qi "localhost\|127.0.0.1" "$file"; then
                    echo "🟡 **Local development config**" >> bug/PHPtest.md
                fi
                ;;
        esac
        echo "" >> bug/PHPtest.md
    fi
done

echo "## 7. DEPENDENCY ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Check composer dependencies
if [ -f "/workspace/ai-content-agent-plugin/composer.json" ]; then
    echo "**Composer Dependencies:**" >> bug/PHPtest.md
    composer_deps=$(cat /workspace/ai-content-agent-plugin/composer.json | grep -A 10 '"require"' | head -10)
    echo '```json' >> bug/PHPtest.md
    echo "$composer_deps" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi

# Check for outdated packages (if composer.lock exists)
if [ -f "/workspace/ai-content-agent-plugin/composer.lock" ]; then
    echo "🟡 **Dependency güncelliği kontrol edilmeli**" >> bug/PHPtest.md
fi

echo "" >> bug/PHPtest.md

echo "## 8. WORDPRESS SPECIFIC CHECKS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# WordPress specific security checks
wp_issues=0

# Check for proper WordPress hooks
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        if grep -q "add_action\|add_filter" "$file" && ! grep -q "wp_verify_nonce\|current_user_can" "$file"; then
            echo "🟡 **$(basename "$file")**: WordPress hook güvenlik kontrolü eksik" >> bug/PHPtest.md
            wp_issues=$((wp_issues + 1))
        fi
        
        # Check for direct database access
        if grep -qi "\$wpdb.*query\|\$wpdb.*prepare" "$file"; then
            echo "🟡 **$(basename "$file")**: Direkt veritabanı erişimi" >> bug/PHPtest.md
        fi
        
        # Check for proper sanitization
        if grep -qi "wp_insert_post\|wp_update_post" "$file" && ! grep -qi "sanitize_" "$file"; then
            echo "🔴 **$(basename "$file")**: Post işlemlerinde sanitizasyon eksik" >> bug/PHPtest.md
            wp_issues=$((wp_issues + 1))
        fi
    fi
done

echo "" >> bug/PHPtest.md

echo "## 9. FINAL SUMMARY" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Calculate risk score
risk_score=$((critical_errors * 10 + security_issues * 5 + warnings * 1))

echo "### 📊 Test İstatistikleri" >> bug/PHPtest.md
echo "- **Toplam dosya:** $total_files" >> bug/PHPtest.md
echo "- **Syntax hatası:** $syntax_errors" >> bug/PHPtest.md
echo "- **Kritik hatalar:** $critical_errors" >> bug/PHPtest.md
echo "- **Güvenlik riski bulunan dosya:** $security_issues" >> bug/PHPtest.md
echo "- **Uyarı bulunan dosya:** $warnings" >> bug/PHPtest.md
echo "- **Büyük dosya:** $large_files" >> bug/PHPtest.md
echo "- **WordPress güvenlik sorunu:** $wp_issues" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

echo "### 🎯 Risk Skoru: $risk_score" >> bug/PHPtest.md
if [ $risk_score -gt 50 ]; then
    echo "🔴 **YÜKSEKRİSK** - Acil müdahale gerekli" >> bug/PHPtest.md
elif [ $risk_score -gt 20 ]; then
    echo "🟡 **ORTA RİSK** - İyileştirmeler gerekli" >> bug/PHPtest.md
else
    echo "🟢 **DÜŞÜK RİSK** - Genel olarak temiz" >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "### 🚨 Acil Düzeltilmesi Gerekenler" >> bug/PHPtest.md
if [ $syntax_errors -gt 0 ]; then
    echo "1. **Syntax hatalarını düzeltin** (Kod çalışmayacak)" >> bug/PHPtest.md
fi
if [ $security_issues -gt 0 ]; then
    echo "2. **Güvenlik açıklarını kapatın** (Hack riski)" >> bug/PHPtest.md
fi
if [ $wp_issues -gt 0 ]; then
    echo "3. **WordPress güvenlik standartlarını uygulayın**" >> bug/PHPtest.md
fi

echo "" >> bug/PHPtest.md
echo "### 💡 Öneriler" >> bug/PHPtest.md
echo "1. Tüm kullanıcı girişlerini sanitize edin" >> bug/PHPtest.md
echo "2. Exception handling ekleyin" >> bug/PHPtest.md
echo "3. Namespace kullanımını yaygınlaştırın" >> bug/PHPtest.md
echo "4. Type hints kullanın" >> bug/PHPtest.md
echo "5. WordPress coding standards uygulayın" >> bug/PHPtest.md
echo "6. Regular security audit yapın" >> bug/PHPtest.md

echo "" >> bug/PHPtest.md
echo "---" >> bug/PHPtest.md
echo "**Test Tamamlanma:** $(date)" >> bug/PHPtest.md
echo "**Test Süresi:** $(date)" >> bug/PHPtest.md

echo "Kapsamlı PHP testi tamamlandı. Kompakt rapor bug/PHPtest.md dosyasına kaydedildi."