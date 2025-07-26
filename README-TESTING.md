# ACA AI Content Agent - Test Altyapısı

Bu dokümantasyon, ACA AI Content Agent eklentisinin test altyapısını ve nasıl kullanılacağını açıklar.

## 🧪 Test Scripti

### Genel Bakış
`test-plugin.php` dosyası, eklentinin ana fonksiyonlarını ve servislerini tam izole bir test ortamında, gerçek bir WordPress kurulumu olmadan test etmek için tasarlanmıştır.

### Özellikler
- ✅ Tüm WordPress fonksiyonları, sabitleri ve servisleri mock'lanır
- ✅ API, veritabanı ve harici servis çağrıları güvenli şekilde simüle edilir
- ✅ Geliştirici/test modu ile gerçek API anahtarları veya ağ bağlantısı gerekmez
- ✅ Eklenti kodunun hatasız ve sürdürülebilir olmasını sağlar

### Kullanım

```bash
# Test scriptini çalıştır
php test-plugin.php
```

### Beklenen Çıktı
```
=== ACA AI Content Agent - Plugin Test Suite ===

--- Testing Encryption Utility ---
✓ PASS: Encryption should not return plain text
✓ PASS: Decryption should return original data
✓ PASS: Encrypted data should not be empty

[... diğer testler ...]

=== Test Summary ===
Total Tests: 24
Passed: 24
Failed: 0
Success Rate: 100%
```

## 🔧 Geliştirici/Test Modu

### Güvenlik Uyarısı
⚠️ **ÖNEMLİ**: Geliştirici modu sadece test ortamında kullanılmalıdır. Prod ortamında asla aktif edilmemelidir.

### Nasıl Çalışır
- `ACA_AI_CONTENT_AGENT_DEV_MODE` sabiti `true` olarak ayarlanır
- Tüm API çağrıları mock yanıtlarla değiştirilir
- Gerçek ağ bağlantısı veya API anahtarları gerekmez

## 📝 Yeni Mock Ekleme

### WordPress Fonksiyonu Ekleme
Eksik bir WordPress fonksiyonu için:

```php
if (!function_exists('wp_some_function')) {
    function wp_some_function($args) {
        // Mock implementation
        return 'mock_value';
    }
}
```

### Servis Metodu Ekleme
Eksik bir servis metodu için:

```php
if (!function_exists('My_Service_some_method')) {
    function My_Service_some_method($args) {
        // Mock implementation
        return true;
    }
}
```

### Mock Kategorileri
Test scriptinde mock'lar şu kategorilerde organize edilmiştir:

1. **Core WordPress functions** - Temel WordPress fonksiyonları
2. **AJAX and JSON functions** - AJAX ve JSON işlemleri
3. **Options and transients** - WordPress seçenekleri ve geçici veriler
4. **Error handling and validation** - Hata yönetimi ve doğrulama
5. **Hooks and filters** - WordPress kancaları ve filtreleri
6. **HTTP and API functions** - HTTP ve API çağrıları
7. **Data sanitization** - Veri temizleme
8. **Time and date functions** - Zaman ve tarih fonksiyonları
9. **User and permission functions** - Kullanıcı ve yetki fonksiyonları
10. **Security and nonce functions** - Güvenlik ve nonce fonksiyonları
11. **Post and content functions** - İçerik ve yazı fonksiyonları

## 🧪 Test Sonuçları

### Başarılı Test
Tüm testler geçerse, eklenti kodunuz:
- İzole ortamda tamamen sorunsuz çalışıyor
- Gerçek WordPress ortamında da daha sağlam ve hatasız çalışacak
- Sürdürülebilir ve geliştirilebilir durumda

### Başarısız Test
Testler başarısız olursa:
1. Hata mesajını kontrol edin
2. Eksik WordPress fonksiyonunu tespit edin
3. Uygun mock'u ekleyin
4. Testi tekrar çalıştırın

## 🔍 Test Edilen Bileşenler

### Temel Servisler
- ✅ **Encryption Utility** - Şifreleme ve şifre çözme
- ✅ **Helper Functions** - Yardımcı fonksiyonlar
- ✅ **Log Service** - Loglama servisi
- ✅ **Cache Service** - Önbellek servisi

### API Servisleri
- ✅ **Gemini API** - Google Gemini AI entegrasyonu
- ✅ **Gumroad API** - Lisans doğrulama

### İçerik Servisleri
- ✅ **Idea Service** - İçerik fikri üretimi
- ✅ **Draft Service** - Taslak oluşturma
- ✅ **Style Guide Service** - Stil rehberi

## 🚀 Geliştirme İpuçları

### Yeni Özellik Ekleme
1. Yeni özelliği kodlayın
2. Test scriptini çalıştırın
3. Eksik mock'ları ekleyin
4. Tüm testlerin geçtiğinden emin olun

### Hata Ayıklama
1. Test çıktısını dikkatle inceleyin
2. Hata mesajlarını analiz edin
3. Eksik bağımlılıkları tespit edin
4. Uygun mock'ları ekleyin

### Performans
- Test scripti hızlı çalışır (genellikle < 5 saniye)
- Gerçek ağ çağrıları yapılmaz
- Tüm işlemler yerel olarak simüle edilir

## 📋 Kontrol Listesi

Test scriptini çalıştırmadan önce:
- [ ] `ACA_AI_CONTENT_AGENT_DEV_MODE` aktif
- [ ] Tüm gerekli dosyalar mevcut
- [ ] PHP 7.4+ kullanılıyor
- [ ] Composer bağımlılıkları yüklü

Test sonrası:
- [ ] Tüm testler geçiyor (%100 başarı)
- [ ] Hata veya uyarı yok
- [ ] Mock'lar doğru çalışıyor
- [ ] Kod değişiklikleri test edildi

## 🤝 Katkıda Bulunma

1. Yeni özellik eklerken test scriptini güncelleyin
2. Eksik mock'ları uygun kategorilere ekleyin
3. Test sonuçlarını dokümante edin
4. Güvenlik uyarılarını dikkate alın

---

**Not**: Bu test altyapısı, eklentinin kalitesini ve sürdürülebilirliğini artırmak için tasarlanmıştır. Düzenli olarak çalıştırılması önerilir. 