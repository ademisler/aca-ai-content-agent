#!/bin/bash

# Advanced PHP Test Script - Comprehensive analysis
echo "=== GELİŞMİŞ PHP DOSYALARI TEST RAPORU ===" > bug/PHPtest.md
echo "Test Tarihi: $(date)" >> bug/PHPtest.md
echo "PHP Sürümü: $(php --version | head -n1)" >> bug/PHPtest.md
echo "Test Araçları: PHPStan, PHPCS, PHPMD, PHPCPD, PHPMND" >> bug/PHPtest.md
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

echo "## 1. SYNTAX VE TEMEL KONTROLLER" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

total_files=0
syntax_errors=0
warnings=0

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        total_files=$((total_files + 1))
        echo "### $(basename "$file")" >> bug/PHPtest.md
        echo "**Dosya Yolu:** $file" >> bug/PHPtest.md
        echo "" >> bug/PHPtest.md
        
        # Basic file info
        file_size=$(stat -c%s "$file")
        line_count=$(wc -l < "$file")
        echo "**Dosya Bilgileri:**" >> bug/PHPtest.md
        echo "- Dosya boyutu: $file_size bytes" >> bug/PHPtest.md
        echo "- Satır sayısı: $line_count" >> bug/PHPtest.md
        echo "" >> bug/PHPtest.md
        
        # Syntax check
        echo "**Syntax Kontrolü:**" >> bug/PHPtest.md
        syntax_result=$(php -l "$file" 2>&1)
        if [[ $syntax_result == *"No syntax errors"* ]]; then
            echo "✅ Syntax hatası yok" >> bug/PHPtest.md
        else
            echo "❌ Syntax hatası bulundu:" >> bug/PHPtest.md
            echo '```' >> bug/PHPtest.md
            echo "$syntax_result" >> bug/PHPtest.md
            echo '```' >> bug/PHPtest.md
            syntax_errors=$((syntax_errors + 1))
        fi
        echo "" >> bug/PHPtest.md
        
        # Advanced checks
        echo "**Gelişmiş Kod Analizi:**" >> bug/PHPtest.md
        
        # Check for various patterns
        if grep -q "<?php" "$file"; then
            echo "✅ PHP açılış etiketi mevcut" >> bug/PHPtest.md
        else
            echo "⚠️  PHP açılış etiketi bulunamadı" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        if grep -q "?>" "$file"; then
            echo "⚠️  PHP kapanış etiketi bulundu (önerilmez)" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        else
            echo "✅ PHP kapanış etiketi yok (iyi pratik)" >> bug/PHPtest.md
        fi
        
        # Check for security issues
        if grep -qi "eval(" "$file"; then
            echo "❌ eval() fonksiyonu kullanımı (güvenlik riski)" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        if grep -qi "exec\|system\|shell_exec\|passthru" "$file"; then
            echo "⚠️  Sistem komutu çalıştırma fonksiyonları tespit edildi" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        if grep -qi "\$_GET\|\$_POST\|\$_REQUEST\|\$_COOKIE" "$file"; then
            echo "⚠️  Kullanıcı girişi kullanımı tespit edildi" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        # Check for deprecated functions
        if grep -qi "mysql_\|ereg\|split\|create_function" "$file"; then
            echo "❌ Deprecated fonksiyon kullanımı" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        # Check for proper error handling
        if grep -qi "try\|catch\|throw" "$file"; then
            echo "✅ Exception handling mevcut" >> bug/PHPtest.md
        else
            echo "⚠️  Exception handling bulunamadı" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        # Check for class definitions
        if grep -q "class " "$file"; then
            echo "✅ Class tanımı mevcut" >> bug/PHPtest.md
        fi
        
        # Check for namespace usage
        if grep -q "namespace " "$file"; then
            echo "✅ Namespace kullanımı mevcut" >> bug/PHPtest.md
        else
            echo "⚠️  Namespace kullanımı yok" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        # Check for type hints
        if grep -qi ": string\|: int\|: bool\|: array\|: float" "$file"; then
            echo "✅ Type hints kullanımı mevcut" >> bug/PHPtest.md
        else
            echo "⚠️  Type hints kullanımı bulunamadı" >> bug/PHPtest.md
            warnings=$((warnings + 1))
        fi
        
        echo "" >> bug/PHPtest.md
        echo "---" >> bug/PHPtest.md
        echo "" >> bug/PHPtest.md
    fi
done

echo "" >> bug/PHPtest.md
echo "## 2. PHPSTAN STATIC ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPStan analysis
echo "PHPStan analizi çalıştırılıyor..." 
phpstan_output=$(./vendor/bin/phpstan analyse ai-content-agent-plugin/ --level=max --no-progress --error-format=raw 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **PHPStan analizi tamamlandı - Hata bulunamadı**" >> bug/PHPtest.md
else
    echo "❌ **PHPStan analizi - Hatalar bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpstan_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 3. PHPCS CODE STYLE ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPCS analysis
echo "PHPCS kod stili analizi çalıştırılıyor..."
phpcs_output=$(./vendor/bin/phpcs ai-content-agent-plugin/ --standard=PSR12 --report=full 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **PHPCS analizi tamamlandı - Kod stili problemleri yok**" >> bug/PHPtest.md
else
    echo "⚠️  **PHPCS analizi - Kod stili problemleri bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpcs_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 4. PHPMD MESS DETECTION" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPMD analysis
echo "PHPMD mess detection analizi çalıştırılıyor..."
phpmd_output=$(./vendor/bin/phpmd ai-content-agent-plugin/ text cleancode,codesize,controversial,design,naming,unusedcode 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **PHPMD analizi tamamlandı - Kod kalitesi problemleri yok**" >> bug/PHPtest.md
else
    echo "⚠️  **PHPMD analizi - Kod kalitesi problemleri bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpmd_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 5. PHPCPD COPY-PASTE DETECTION" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPCPD analysis
echo "PHPCPD kopyala-yapıştır analizi çalıştırılıyor..."
phpcpd_output=$(./vendor/bin/phpcpd ai-content-agent-plugin/ 2>&1)
if [[ $phpcpd_output == *"No clones found"* ]]; then
    echo "✅ **PHPCPD analizi tamamlandı - Kopyala-yapıştır kodu bulunamadı**" >> bug/PHPtest.md
else
    echo "⚠️  **PHPCPD analizi - Kopyala-yapıştır kodlar bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpcpd_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 6. PHPMND MAGIC NUMBER DETECTION" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHPMND analysis
echo "PHPMND sihirli sayı analizi çalıştırılıyor..."
phpmnd_output=$(./vendor/bin/phpmnd ai-content-agent-plugin/ --non-zero-exit-on-violation 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **PHPMND analizi tamamlandı - Sihirli sayı problemi yok**" >> bug/PHPtest.md
else
    echo "⚠️  **PHPMND analizi - Sihirli sayılar bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$phpmnd_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 7. PHP COMPATIBILITY CHECK" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Run PHP Compatibility check
echo "PHP uyumluluk analizi çalıştırılıyor..."
compat_output=$(./vendor/bin/phpcs ai-content-agent-plugin/ --standard=PHPCompatibility --runtime-set testVersion 8.0- 2>&1)
if [ $? -eq 0 ]; then
    echo "✅ **PHP Uyumluluk analizi tamamlandı - Uyumluluk problemi yok**" >> bug/PHPtest.md
else
    echo "⚠️  **PHP Uyumluluk analizi - Uyumluluk problemleri bulundu:**" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
    echo "$compat_output" >> bug/PHPtest.md
    echo '```' >> bug/PHPtest.md
fi
echo "" >> bug/PHPtest.md

echo "## 8. SECURITY VULNERABILITY SCAN" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

echo "**Manuel Güvenlik Taraması:**" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Advanced security checks
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "### $(basename "$file") - Güvenlik Analizi" >> bug/PHPtest.md
        
        # SQL Injection risks
        if grep -qi "mysql_query\|mysqli_query\|query.*\$" "$file"; then
            echo "⚠️  SQL injection riski: Dinamik query kullanımı" >> bug/PHPtest.md
        fi
        
        # XSS risks
        if grep -qi "echo.*\$_\|print.*\$_" "$file"; then
            echo "⚠️  XSS riski: Filtrelenmemiş output" >> bug/PHPtest.md
        fi
        
        # File inclusion risks
        if grep -qi "include.*\$\|require.*\$" "$file"; then
            echo "⚠️  File inclusion riski: Dinamik include/require" >> bug/PHPtest.md
        fi
        
        # Command injection risks
        if grep -qi "exec.*\$\|system.*\$\|shell_exec.*\$" "$file"; then
            echo "❌ Command injection riski: Dinamik komut çalıştırma" >> bug/PHPtest.md
        fi
        
        # Deserialization risks
        if grep -qi "unserialize.*\$" "$file"; then
            echo "❌ Deserialization riski: Güvenilmeyen veri deserializasyonu" >> bug/PHPtest.md
        fi
        
        # File upload risks
        if grep -qi "\$_FILES" "$file"; then
            echo "⚠️  File upload riski: Dosya yükleme işlemi" >> bug/PHPtest.md
        fi
        
        echo "" >> bug/PHPtest.md
    fi
done

echo "## 9. PERFORMANCE ANALYSIS" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Performance analysis
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        file_size=$(stat -c%s "$file")
        line_count=$(wc -l < "$file")
        
        if [ $file_size -gt 100000 ]; then
            echo "⚠️  **$(basename "$file")**: Büyük dosya boyutu ($file_size bytes)" >> bug/PHPtest.md
        fi
        
        if [ $line_count -gt 1000 ]; then
            echo "⚠️  **$(basename "$file")**: Çok sayıda satır ($line_count satır)" >> bug/PHPtest.md
        fi
        
        # Check for potential performance issues
        if grep -qi "while.*true\|for.*;;.*{" "$file"; then
            echo "⚠️  **$(basename "$file")**: Sonsuz döngü riski" >> bug/PHPtest.md
        fi
        
        if grep -qi "mysql_connect\|mysql_pconnect" "$file"; then
            echo "⚠️  **$(basename "$file")**: Eski MySQL bağlantı yöntemi (performans sorunu)" >> bug/PHPtest.md
        fi
    fi
done

echo "" >> bug/PHPtest.md
echo "## 10. ÖZET VE ÖNERİLER" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

# Summary
echo "### Test Özeti" >> bug/PHPtest.md
echo "- **Toplam test edilen dosya:** $total_files" >> bug/PHPtest.md
echo "- **Syntax hatası bulunan dosya:** $syntax_errors" >> bug/PHPtest.md
echo "- **Uyarı bulunan dosya:** $warnings" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

if [ $syntax_errors -eq 0 ]; then
    echo "✅ **Tüm dosyalar syntax açısından temiz**" >> bug/PHPtest.md
else
    echo "❌ **$syntax_errors dosyada syntax hatası bulundu**" >> bug/PHPtest.md
fi

echo "" >> bug/PHPtest.md
echo "### Kritik Öneriler" >> bug/PHPtest.md
echo "1. **Acil:** Syntax hatalarını düzeltin" >> bug/PHPtest.md
echo "2. **Güvenlik:** Tüm kullanıcı girişlerini sanitize edin" >> bug/PHPtest.md
echo "3. **Kod Kalitesi:** PHPStan ve PHPCS önerilerini uygulayın" >> bug/PHPtest.md
echo "4. **Performans:** Büyük dosyaları refactor edin" >> bug/PHPtest.md
echo "5. **Modern PHP:** Type hints ve namespace kullanımını artırın" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

echo "### Test Araçları Kullanılan" >> bug/PHPtest.md
echo "- **PHPStan**: Static analysis (Level: max)" >> bug/PHPtest.md
echo "- **PHPCS**: Code style analysis (Standard: PSR12)" >> bug/PHPtest.md
echo "- **PHPMD**: Mess detection" >> bug/PHPtest.md
echo "- **PHPCPD**: Copy-paste detection" >> bug/PHPtest.md
echo "- **PHPMND**: Magic number detection" >> bug/PHPtest.md
echo "- **PHPCompatibility**: PHP version compatibility" >> bug/PHPtest.md
echo "- **Manuel güvenlik taraması**" >> bug/PHPtest.md
echo "" >> bug/PHPtest.md

echo "**Test Tamamlanma Tarihi:** $(date)" >> bug/PHPtest.md
echo "**Test Süresi:** $(date)" >> bug/PHPtest.md

echo "Gelişmiş PHP testi tamamlandı. Detaylı rapor bug/PHPtest.md dosyasına kaydedildi."