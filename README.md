### **Proje Uygulama Belgesi: ACA – AI Content Agent**

**Versiyon:** 1.0
**Tarih:** 23.07.2025
**Proje Sahibi:** Adem Isler (geliştiricinin kişisel websitesi: ademisler.com)

---

### **Bölüm 1: Proje Vizyonu ve Temel Felsefe**

#### **1.1. Proje Adı**
ACA – AI Content Agent

#### **1.2. Vizyon**
WordPress siteleri için, sitenin mevcut içerik tonunu ve stilini öğrenerek; SEO uyumlu, yüksek kaliteli, güvenilir ve tutarlı yeni içerikleri otonom bir şekilde üreten; bir "dijital içerik stratejisti ve editörü" olarak görev yapan akıllı bir eklenti oluşturmak.

#### **1.3. Ana Felsefe: "Kullanıcı Kontrolünde Otomasyon"**
ACA, kontrolü kullanıcıdan alan bir "kara kutu" olmayacaktır. Her adımda şeffaflık sağlayacak, kullanıcıya müdahale etme ve son kararı verme yetkisi tanıyacaktır. ACA, bir yazarın yerini almak için değil, ona zaman kazandıran ve yaratıcılığını tetikleyen bir süper asistan olmak için tasarlanmıştır.

#### **1.4. Hedef Kitle**
Blog yazarları, içerik üreticileri, KOBİ ve kurumsal web sitesi sahipleri, dijital pazarlama ve SEO ajansları, e-ticaret sitelerinin içerik yöneticileri.

---

### **Bölüm 2: Temel Yapılandırma ve Kurulum (Yönetici Paneli)**

Kullanıcının ACA'yı kendi sitesine göre özelleştirdiği ilk ve en önemli adımdır.

#### **2.1. API ve Bağlantı Ayarları**
*   **Google Gemini API Anahtarı:** Güvenli bir metin giriş alanı.
*   **Bağlantı Testi:** Girilen API anahtarının geçerliliğini ve API'ye erişimin olup olmadığını kontrol eden bir "Bağlantıyı Test Et" butonu.

#### **2.2. Çalışma Modu ve Otomasyon Seviyesi**
*   **Manuel Mod:** Eklenti, kullanıcı ACA Kontrol Panelinden manuel olarak tetikleme yapana kadar hiçbir arka plan işlemi yapmaz.
*   **Yarı Otomatik Mod (Fikir ve Onay):** Eklenti, belirlenen periyotlarda sadece yeni blog yazısı fikirleri üretir. Kullanıcı bu fikirleri panelden onayladığında, seçilenler için taslak yazı oluşturulur.
*   **Tam Otomatik Mod (Taslak Oluşturma):** Fikir bulma ve taslak yazı oluşturma süreçleri tamamen otomatiktir. **Kritik Not:** Üretilen tüm içerikler, istisnasız olarak daima "Taslak" olarak kaydedilir, asla doğrudan yayınlanmaz.

#### **2.3. İçerik Analizi ve Öğrenme Kuralları**
*   **Analiz Hedefleme:** Kullanıcının, ACA'nın "tarz öğrenmek" için hangi içerikleri okuyacağını seçebileceği alanlar:
    *   **İçerik Türleri:** Yazılar, Sayfalar (Checkbox ile seçilebilir).
    *   **Kategoriler:** Tarz analizi için taranacak veya hariç tutulacak kategorilerin seçimi.
*   **Analiz Derinliği:** Stili öğrenmek için baz alınacak son yazı sayısı (Örn: 10, 20, 50).

#### **2.4. İçerik Üretim Kuralları**
*   **Otomasyon Sıklığı:** (Otomatik modlar için) WordPress'in dahili zamanlayıcısı (WP-Cron) ile yeni fikir/taslak üretme sıklığı (Örn: Her gün 03:00'da, Haftada bir, Ayda bir).
*   **Varsayılan Yazar:** Oluşturulan taslakların hangi WordPress kullanıcısı adına kaydedileceğinin seçimi.
*   **Üretim Limiti:** Her otomasyon döngüsünde üretilecek maksimum fikir/taslak sayısı (API maliyetlerini kontrol altında tutmak için).

---

### **Bölüm 3: İçerik Üretim Motoru: Öğrenme, Fikir ve Yazım**

Bu bölüm, eklentinin temel çalışma mekanizmasını tanımlar.

#### **3.1. Adım: Stil Kılavuzu Üretimi (Öğrenme Aşaması)**
*   **Süreç:** Belirlenen periyotlarda (örn. haftada bir) arka planda çalışan bir WP-Cron görevi.
*   **İşleyiş:** Eklenti, kullanıcının belirlediği ayarlara göre son X yazının içeriğini alır. Bu içerikleri Gemini API'sine özel bir prompt ile gönderir:
    *   *"Aşağıdaki metinleri analiz et. Bu metinlerin yazım tonunu (samimi, resmi, esprili), cümle yapısını (kısa, uzun), paragraf uzunluğunu ve genel formatlama stilini (liste kullanımı, kalın metinler vb.) tanımlayan bir 'Stil Kılavuzu' oluştur. Bu kılavuz, başka bir yazara bu stili taklit etmesi için verilecek bir talimat metni gibi olmalı."*
*   **Çıktı:** API'den dönen bu "Stil Kılavuzu Promtu" veritabanında saklanır ve sonraki tüm içerik üretimlerinde temel kimlik olarak kullanılır.

#### **3.2. Adım: Fikir Üretimi (Yaratıcılık Aşaması)**
*   **Süreç:** Otomasyon döngüsünde veya manuel olarak tetiklenir.
*   **İşleyiş:** Eklenti, sitedeki son yazı başlıklarını ve kategorileri analiz eder. Gemini'ye şu prompt'u gönderir:
    *   *"Mevcut blog yazısı başlıkları şunlar: [...]. Bu konularla alakalı, ancak bunları tekrar etmeyen, SEO dostu ve ilgi çekici 5 yeni blog yazısı başlığı öner."*
*   **Çıktı:** Üretilen başlıklar, ACA Kontrol Paneli'ndeki "Fikirler" bölümüne kaydedilir.

#### **3.3. Adım: İçerik Yazımı (Üretim Aşaması)**
*   **Süreç:** Kullanıcı onayıyla veya tam otomatik modda tetiklenir.
*   **İşleyiş:** Bir fikir yazıya dönüştürüleceği zaman, eklenti şu parçaları birleştirerek nihai, karmaşık bir prompt oluşturur:
    1.  **Stil Kılavuzu:** Bölüm 3.1'de oluşturulan prompt.
    2.  **Yazı Görevi:** *"Yukarıdaki stil kılavuzuna sadık kalarak, başlığı '[Seçilen Yazı Fikri]' olan, yaklaşık 800 kelimelik, SEO'ya uygun bir blog yazısı yaz. Yazıyı bir giriş, H2 ve H3 alt başlıkları içeren bir ana gövde ve bir sonuç bölümü olarak yapılandır."*
    3.  **Meta Veri ve Kaynak Talebi:** *"Yazının sonuna, yazıyla ilgili 5 adet etiket, 155 karakterlik bir meta açıklama ve yazıda bahsettiğin önemli veriler için en az 2 adet güvenilir kaynak URL'si ekle."*
    4.  **Formatlama Talimatı:** *"Çıktıyı ayrıştırabilmem için şu formatta ver: ---YAZI İÇERİĞİ--- [Yazı] ---ETİKETLER--- [Etiketler] ---META AÇIKLAMA--- [Açıklama] ---KAYNAKLAR--- [URL'ler]"*
*   **Çıktı:** Eklenti, bu strukture edilmiş cevabı alır, `wp_insert_post()` ile taslağı oluşturur ve meta verileri ilgili alanlara kaydeder.

---

### **Bölüm 4: İçerik Kalitesi, Güvenilirlik ve Zenginleştirme**

Bu katman, üretilen içeriğin standart metnin ötesine geçmesini sağlar.

#### **4.1. Güvenilirlik ve Özgünlük**
*   **Kaynak Göstermeli İçerik:** Üretilen yazılara, özellikle veri ve istatistik içeren kısımlar için, güvenilir kaynaklara (örn: .gov, .edu, bilimsel yayınlar) link eklenir. Bu kaynaklar yazının sonunda listelenir.
*   **Otomatik İntihal Kontrolü:** Üretilen her taslak, Copyscape veya benzeri bir servisin API'si ile otomatik olarak taranır. "İntihal Skoru" ACA panelinde gösterilerek kullanıcının içeriğin özgünlüğünden emin olması sağlanır.

#### **4.2. İçerik Zenginleştirme**
*   **Akıllı Öne Çıkan Görsel:** Pexels/Unsplash API'leri ile içeriğe uygun, telifsiz stok görseller önerir. İleri seviyede, DALL-E 3 gibi API'lerle yazıya özel, tamamen orijinal görseller üretme seçeneği sunar.
*   **Otomatik İç Linkleme:** Yeni taslakların içine, sitenin eski ve ilgili yazılarına SEO dostu iç linkler ekler. Ayarlar panelinden eklenecek maksimum link sayısı belirlenebilir.
*   **Veri Destekli Bölümler:** Yazının konusuna uygun olarak güncel istatistikler, veriler veya basit tablolar bulup yazıya ekleyerek içeriğin otoritesini artırır.

---

### **Bölüm 5: Stratejik Planlama ve Gelişmiş Yönetim**

ACA'yı bir "içerik stratejisti" haline getiren özellikler.

#### **5.1. Stratejik Planlama Araçları**
*   **İçerik Kümesi (Content Cluster) Planlayıcısı:** Kullanıcının belirlediği bir ana konu ("Pillar Content") etrafında, bu konuyu destekleyecek alt konu başlıkları ("Cluster Content") önerir ve bu içeriklerin birbiriyle linklenmesini planlar.
*   **İçerik Güncelleme Asistanı:** Sitedeki eski yazıların güncelliğini yitirdiğini tespit eder ve bu yazıları en son bilgilerle güncellemek için somut önerilerde bulunur.
*   **Google Search Console Entegrasyonu:** Arama performansını analiz ederek, kullanıcıların aradığı ama sitede cevabı olmayan konuları tespit edip yeni içerik fikirleri üretir.

#### **5.2. Gelişmiş Uyarlanabilirlik**
*   **"Prompt Editörü" Arayüzü:** Gelişmiş kullanıcıların, ACA'nın arka planda kullandığı temel prompt'ları (Stil Kılavuzu, İçerik Yazma vb.) manuel olarak düzenleyebileceği bir arayüz.
*   **Marka Sesi Profilleri:** Farklı içerik türleri (örn: blog, teknik doküman, ürün açıklaması) için farklı yazım stilleri ve ses tonları tanımlayıp kaydetme imkanı.
*   **Kullanıcı Geri Bildirim Döngüsü:** Üretilen her fikir/taslak için "👍/👎" butonları. Bu geri bildirimler, sistemin zamanla daha isabetli sonuçlar üretmesini sağlamak için kullanılır.

---

### **Bölüm 6: Yönetişim, Güvenlik ve Erişilebilirlik**

#### **6.1. Yönetim ve Maliyet Kontrolü**
*   **Rol Bazlı Yetkilendirme:** WordPress kullanıcı rollerine (Admin, Editör, Yazar) göre ACA paneli içinde farklı yetkiler tanımlama (örn: Yazar sadece taslakları görür, Editör fikirleri onaylayabilir, Admin tüm ayarları değiştirir).
*   **API Kullanım Yönetimi:** Ayarlar panelinde aylık API token/çağrı limiti belirleme seçeneği ve bu limite yaklaşıldığında uyarı sistemi. Panelde anlık olarak ay içi API kullanımını gösteren bir sayaç.

#### **6.2. Teknik Mimari ve Standartlar**
*   **API İletişim Mimarisi:** Tüm API çağrıları, Bölüm 7'de detaylandırılan merkezi ve yeniden kullanılabilir bir fonksiyon üzerinden yönetilir.
*   **Teknik Yığın:** PHP 7.4+, Google Gemini API, WP-Cron. Yönetici paneli için modern bir JS kütüphanesi (React/Vue.js) veya Vanilla JS.
*   **Erişilebilirlik ve Mobil Uyum:** Tüm yönetici panelleri, mobil cihazlarda rahatça kullanılabilecek şekilde responsive olarak tasarlanır ve klavye navigasyonu, aria-label kullanımı gibi erişilebilirlik (a11y) standartlarına tam uyum sağlar.

---

### **Bölüm 7: Merkezi API İletişim Mimarisi**

#### **7.1. Amaç**
Tüm Gemini API çağrılarını, kod tekrarını önleyen, bakımı kolay ve yeniden kullanılabilir tek bir merkezi fonksiyon üzerinden yönetmek.

#### **7.2. Fonksiyon Yapısı: `aca_call_gemini_api( $prompt, $system_instruction = '' )`**
Bu fonksiyon:
1.  API anahtarını kontrol eder.
2.  Verilen `$prompt` ve opsiyonel `$system_instruction`'a göre JSON `$payload`'u hazırlar.
3.  WordPress'in `wp_remote_post()` fonksiyonunu kullanarak API isteğini güvenli bir şekilde yapar. Timeout süresi, içerik üretimi gibi uzun sürebilecek işlemler için artırılır (örn: 60-120 saniye).
4.  API'den dönen cevabı kapsamlı bir şekilde kontrol eder: `WP_Error` kontrolü, HTTP durum kodu kontrolü (200), Gemini API'sinin kendi hata mesajları.
5.  Tüm kontrollerden geçerse, üretilen metni temiz bir şekilde döndürür. Aksi takdirde, loglanabilir bir `WP_Error` nesnesi döndürür.

#### **7.3. Uygulama**
Eklentinin diğer tüm özellikleri (Stil Kılavuzu, Fikir Üretme, İçerik Yazma vb.), karmaşık API kodunu tekrar etmek yerine, sadece bu merkezi fonksiyonu çağırarak çalışacaktır. Bu, kodun okunabilirliğini, güvenliğini ve ölçeklenebilirliğini en üst düzeye çıkarır.

---

### **Bölüm 8: Kullanıcı Deneyimi (UX) ve Arayüz (UI) Felsefesi**

#### **8.1. Temel Arayüz Felsefesi**
*   **Netlik ve Odaklanma:** Arayüz, kullanıcıyı gereksiz bilgilerle boğmayacak. Her ekranın tek bir ana amacı olacaktır (örn: Ayarlar, Fikirler, Raporlar).
*   **Rehberli Deneyim:** Özellikle yeni kullanıcılar için, ne yapmaları gerektiğini anlamalarına yardımcı olacak ipuçları, araç ipuçları (tooltips) ve kısa açıklamalar kullanılacaktır.
*   **Görsel Hiyerarşi:** Önemli eylemler (örn: "Taslak Oluştur") ve bilgiler (örn: "API Limiti") görsel olarak öne çıkarılacaktır.

#### **8.2. Kurulum Sihirbazı (Onboarding Wizard)**
Eklenti ilk aktive edildiğinde, kullanıcıyı adım adım temel ayarlara yönlendiren bir kurulum sihirbazı çalışır:
1.  **Hoş Geldiniz:** Projenin kısa bir tanıtımı.
2.  **API Bağlantısı:** API anahtarının girilmesi ve test edilmesi.
3.  **Temel Öğrenme Ayarları:** Hangi içerik türlerinin analiz edileceğinin hızlıca seçilmesi.
4.  **Çalışma Modu Seçimi:** Manuel, Yarı Otomatik, Tam Otomatik modlarından birinin seçilmesi.
5.  **Tamamlandı:** Kullanıcıyı ana ACA Kontrol Paneli'ne yönlendirme.

#### **8.3. Merkezi Kontrol Paneli (Dashboard)**
WordPress yönetici menüsünde "ACA" adıyla yer alacak ana panel, aşağıdaki bileşenleri içerir:
*   **Genel Bakış:** API kullanım durumu, bekleyen fikir sayısı, oluşturulan taslak sayısı gibi hızlı istatistikler.
*   **Fikir Akışı:** Onay bekleyen yeni içerik fikirlerinin listelendiği, "Onayla ve Yaz" veya "Reddet" butonlarının bulunduğu bir alan.
*   **Son Etkinlikler:** "Stil kılavuzu güncellendi", "3 yeni fikir üretildi" gibi son işlemlerin bir kaydı.
*   **Hızlı Eylemler:** "Şimdi Yeni Fikir Üret", "Stil Kılavuzunu Manuel Güncelle" gibi kısayol butonları.

#### **8.4. Bildirim Merkezi**
API anahtarı geçersiz olduğunda, API kullanım limiti %80'e ulaştığında, yeni fikirler onaya hazır olduğunda (isteğe bağlı) veya bir içerik oluşturma işlemi başarısız olduğunda kullanıcıyı bilgilendirir.

---

### **Bölüm 9: Ticarileştirme ve Destek Modeli**

#### **9.1. Lisanslama Modeli: Freemium**
*   **ACA (Ücretsiz Sürüm):** WordPress.org'da yayınlanır. Ayda 5 fikir ve 2 taslak gibi sınırlamalar içerir. Manuel modda çalışır. Gelişmiş strateji ve zenginleştirme özellikleri kapalıdır.
*   **ACA Pro (Premium Sürüm):** Yıllık abonelik tabanlıdır. Tüm özelliklerin (otomatik modlar, sınırsız üretim, intihal kontrolü, strateji araçları vb.) kilidini açar.

#### **9.2. Satış ve Lisanslama Platformu: Gumroad**
*   **Platform:** ACA Pro sürümünün satışı, ödeme işlemleri ve lisans anahtarı yönetimi için **Gumroad** platformu kullanılacaktır.
*   **Lisans Anahtarı Mekanizması:** Kullanıcılar, Gumroad üzerinden satın alma işlemi yaptıklarında kendilerine özel, tekil bir lisans anahtarı alacaklardır. Bu lisans anahtarı, eklentinin WordPress yönetici panelindeki ilgili alana girilerek Pro özelliklerin kilidini açmak ve güncellemeleri almak için kullanılacaktır.
*   **Doğrulama:** Eklenti, girilen lisans anahtarının geçerliliğini doğrulamak için bir mekanizmaya sahip olacaktır.

#### **9.3. Fiyatlandırma ve Lisans Türleri**
*   Tek Site Lisansı
*   3 Site Lisansı
*   Ajans Lisansı (Sınırsız Site)

#### **9.4. Destek ve Güncelleme Politikası**
Aktif ve geçerli bir **Gumroad lisans anahtarına** sahip kullanıcılar, 1 yıl boyunca eklenti güncellemelerine ve ticket sistemi üzerinden teknik desteğe erişim hakkına sahip olur.

---

### **Bölüm 10: Performans, Optimizasyon ve Kaynak Yönetimi**

#### **10.1. Asenkron İşlemler (Arka Plan Görevleri)**
İçerik analizi, stil kılavuzu oluşturma ve içerik yazma gibi uzun süren işlemler, kullanıcı arayüzünü kilitlemeden, WordPress'in Action Scheduler kütüphanesi kullanılarak arka planda asenkron olarak çalışacaktır.

#### **10.2. Veritabanı Optimizasyonu**
Eklenti, kendi verilerini (ayarlar, fikirler, loglar vb.) depolamak için özel veritabanı tabloları oluşturarak `wp_posts` ve `wp_postmeta` tablolarının şişmesini önler ve sorgu performansını artırır.

#### **10.3. Akıllı Önbellekleme (Caching)**
Sıkça erişilen ancak nadiren değişen veriler (örn: Stil Kılavuzu) WordPress Transients API kullanılarak geçici olarak önbelleğe alınır. Bu, gereksiz API ve veritabanı çağrılarını engeller.

---

### **Bölüm 11: Güvenlik, Veri Gizliliği ve Yasal Uyumluluk**

#### **11.1. Veri Güvenliği**
*   **API Anahtarı Saklama:** API anahtarı, veritabanında şifrelenmiş (encrypted) olarak saklanacaktır.
*   **Güvenli API Çağrıları:** Tüm API çağrıları SSL doğrulaması etkin şekilde yapılacaktır.
*   **Yetki Kontrolü:** Tüm eylemler WordPress'in yetki (capability) sistemine göre kontrol edilecektir.

#### **11.2. Veri Gizliliği (GDPR Uyumluluğu)**
*   **Şeffaflık:** Kullanıcı, harici bir API kullanıldığı konusunda açıkça bilgilendirilir.
*   **Veri Minimizasyonu:** API'ye sadece görevin gerektirdiği minimum veri gönderilir.
*   **Veri Saklamama Politikası:** Analiz için alınan site içeriklerinin tam metni eklentinin veritabanında kalıcı olarak saklanmaz.
*   **GDPR Araçları Uyumu:** Eklenti, WordPress'in "Kişisel Verileri Dışa Aktar/Sil" araçlarıyla uyumlu olacaktır.

#### **11.3. Yasal Sorumluluk Reddi (Disclaimer)**
Yönetim panelinde ve dokümantasyonda, üretilen tüm içeriğin bir "taslak" olduğu ve yayınlanmadan önce kullanıcı tarafından mutlaka kontrol edilmesi, düzenlenmesi ve doğrulanması gerektiği açıkça belirtilir. Nihai sorumluluğun kullanıcıya ait olduğu vurgulanır.

-----------


#### **Bölüm 4: İçerik Kalitesi, Güvenilirlik ve Zenginleştirme**

Bu bölümdeki kritik özelliklerin bir kısmı bu sürümle birlikte eklenmiştir.

*   **Güvenilirlik ve Özgünlük:**
    *   **Otomatik İntihal Kontrolü:** Copyscape API entegrasyonu yapıldı ve her taslak için sonuçlar meta alana kaydediliyor.
    *   **Kaynak Göstermeli İçerik:** `write_post_draft` fonksiyonunda kaynak linkleri taslak içeriğine otomatik olarak ekleniyor.

*   **İçerik Zenginleştirme:**
    *   **Akıllı Öne Çıkan Görsel:** Unsplash entegrasyonu eklenerek, taslaklara otomatik görsel atanabiliyor.
    *   **Otomatik İç Linkleme:** Yeni taslaklara mevcut içerikten rastgele iç linkler ekleyen temel bir mekanizma eklendi.
    *   **Veri Destekli Bölümler:** Taslak sonuna güncel istatistikler veya tablolar ekleyen özellik eklendi.

#### **Bölüm 5: Stratejik Planlama ve Gelişmiş Yönetim**

Bu bölümdeki stratejik özelliklerin de büyük çoğunluğu uygulanmamıştır. "Prompt Editörü" dışında kalanlar, eklentinin vizyonundaki "dijital içerik stratejisti" rolünü üstlenmesini sağlayacak özelliklerdir.

*   **Stratejik Planlama Araçları:**
    *   **İçerik Kümesi (Content Cluster) Planlayıcısı:** Basit bir AI tabanlı planlayıcı eklendi.
    *   **İçerik Güncelleme Asistanı:** Yazılar için güncelleme önerileri sunan yardımcı fonksiyon eklendi.
    *   **Google Search Console Entegrasyonu:** API anahtarı ile temel arama sorguları çekilebiliyor.

*   **Gelişmiş Uyarlanabilirlik:**
    *   **Marka Sesi Profilleri:** Birden fazla stil kılavuzu kaydedip içerik üretiminde kullanmak mümkün.
    *   **Kullanıcı Geri Bildirim Döngüsü:** Fikir listesinde 👍/👎 butonlarıyla geri bildirim kaydedilebiliyor.

#### **Bölüm 9: Ticarileştirme ve Destek Modeli**

Ticarileştirme mantığı henüz tam olarak entegre edilmemiştir.

*   **Lisanslama Modeli (Freemium):**
    *   `aca_is_pro()` fonksiyonu artık Gumroad lisansı doğrulamasına göre gerçek değeri döndürüyor ve bazı özellikler Pro sürüme özel.

*   **Gumroad Entegrasyonu:**
    *   Lisans anahtarları gerçek Gumroad API'si ile doğrulanıyor.

---

### **Kısmen Uygulanmış veya Farklı Uygulanmış Özellikler**

*   **API Anahtarı Güvenliği (Bölüm 6.1 ve 11.1):**
    *   API anahtarı artık `openssl` kullanılarak şifrelenmiş biçimde saklanmaktadır.

*   **Asenkron İşlemler (Bölüm 10.1):**
    *   Uzun işlemler Action Scheduler kütüphanesi ile arka planda yürütülmektedir.

---

### **Özet ve Sonuç**

Eklenti, **"Proje Uygulama Belgesi"nin yaklaşık %40-50'sini** kapsayan sağlam bir temel üzerine inşa edilmiştir.

**Mevcut ve Çalışan Özellikler:**
*   Kurulum Sihirbazı (Onboarding)
*   Veritabanı Tablolarının Oluşturulması
*   Yönetici Paneli ve Ayarlar Sayfası (API Anahtarı, Çalışma Modları, Analiz Kuralları vb.)
*   Action Scheduler entegrasyonu
*   Merkezi `aca_call_gemini_api` fonksiyonu
*   "Stil Kılavuzu" ve "Fikir" üretme AJAX fonksiyonları
*   Fikirlerden "Taslak" oluşturma mantığı
*   Rol bazlı yetkilendirme (`add_capabilities`)
*   API kullanım limiti ve sayacı
*   "Prompt Editörü" arayüzü
*   Loglama sistemi

**Henüz Uygulanmamış Kritik Özellikler:**
*   Bu sürümle birlikte içerik zenginleştirme (intihal kontrolü, otomatik görsel ve iç link ekleme) ve stratejik planlama araçları (Content Cluster, Güncelleme Asistanı, Search Console verileri) eklendi.
*   Pro özellikleri için temel lisanslama mantığı (Gumroad doğrulaması) uygulanmış olsa da daha gelişmiş bir model planlanmaktadır.

Proje, temel işlevleri yerine getiren bir "Minimum Viable Product" (MVP - Minimum Uygulanabilir Ürün) aşamasındadır. `README.md`'de belirtilen vizyona tam olarak ulaşması için özellikle Bölüm 4, 5 ve 9'daki özelliklerin geliştirilmesi gerekmektedir.
