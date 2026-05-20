# Manual Pengguna: Graduation Registration System KONVOJTM
### Panduan Pengoperasian Sistem Untuk Pihak Pentadbir (Admin) & Portal Urus Diri Pelajar (konvojtm)

[cite_start]Manual ini disediakan khas bagi membimbing dua kelompok pengguna utama sistem: **Pihak Pentadbir (Admin)** yang bertanggungjawab menguruskan keseluruhan acara konvokesyen dan mengesahkan pembayaran, serta **Pelajar** yang akan menggunakan portal kendiri bagi mengemas kini profil dan menghantar bukti pembayaran[cite: 452].

---

## 1. Panduan Penggunaan Pentadbir (Admin Manual)

[cite_start]Modul pentadbir merupakan pusat kawalan utama bagi urus setia konvokesyen untuk memantau pendaftaran, menetapkan yuran, dan melakukan penentusahan dokumen kewangan pelajar[cite: 454].

### 1.1 Mengurus Acara Graduasi (Graduation CRUD)
[cite_start]Untuk memulakan pengurusan acara, layari URL pengurusan utama aplikasi melalui pautan berikut[cite: 455]:
[cite_start]👉 `http://konvojtm.test/graduations` [cite: 457]

#### ➕ Mencipta Acara Baharu:
1. [cite_start]Klik butang berwarna biru bertulis **"+ Cipta Acara Baru"** di bahagian atas kanan skrin[cite: 459].
2. [cite_start]Isi butiran borang dengan lengkap[cite: 460]:
   * [cite_start]**Nama Acara:** Masukkan tajuk rasmi sesi (Contoh: `Sesi 1 Konvo UTM 2026`)[cite: 461].
   * [cite_start]**Tarikh Majlis:** Pilih tarikh rasmi majlis berlangsung daripada kalendar[cite: 462].
   * [cite_start]**Kadar Yuran:** Nyatakan amaun yuran dalam format RM tanpa simbol (Contoh: `350.00`)[cite: 463].
   * [cite_start]**Status Permulaan:** Setkan status kepada **Aktif** sekiranya anda mahu borang pendaftaran dibuka serta-merta kepada pelajar[cite: 464].
3. [cite_start]Klik butang **"Simpan Acara"** untuk memuktamadkan penyimpanan data[cite: 465].

#### 🔄 Mengemas Kini & Menutup Acara:
* [cite_start]**Edit Info Acara:** Klik butang **"Edit"** pada baris nama majlis untuk meminda tarikh atau kadar yuran[cite: 467]. [cite_start]Klik **"Kemas Kini"** untuk menyimpan data terbaru[cite: 468].
* [cite_start]**Menutup Pendaftaran (Shortcut Close):** Apabila tarikh tamat pendaftaran dicapai, klik butang **"Tutup"**[cite: 469]. [cite_start]Status akan bertukar secara automatik ke **Closed** dan menyekat mana-mana pelajar daripada membuat perubahan resit[cite: 470].

#### 👥 Melihat Senarai Pelajar Terdaftar:
* [cite_start]Klik butang **"Lihat"** pada senarai utama acara konvokesyen untuk menyemak maklumat terperinci majlis berserta senarai penuh nama pelajar terdaftar di bawah sesi tersebut[cite: 474].

### 1.2 Mengesahkan Pembayaran Yuran Pelajar
[cite_start]Apabila pelajar memuat naik dokumen bukti transaksi, nama pendaftar secara automatik dimasukkan ke dalam baris giliran kelulusan pentadbir[cite: 476]. Sila layari:
[cite_start]👉 `http://konvojtm.test/admin/verifications` [cite: 477]

1. [cite_start]Semak senarai paparan nama pelajar yang sedang menunggu proses penentusahan[cite: 478].
2. [cite_start]Klik pautan biru bertulis **"Lihat Fail Resit"**[cite: 479]. [cite_start]Dokumen transaksi (Fail PDF/Imej) yang dihantar oleh pelajar akan dipaparkan dalam tab baru pelayar web untuk semakan silang[cite: 479].
3. [cite_start]Sekiranya pembayaran disahkan sah masuk ke dalam pangkalan akaun universiti, klik butang hijau **"Sahkan Pembayaran"**[cite: 480].
4. [cite_start]Sistem akan mengemas kini rekod dan menukar status akaun pendaftaran pelajar tersebut kepada status disahkan serta-merta[cite: 481].

---

## 2. Panduan Penggunaan Pelajar (Student Portal Manual)

[cite_start]Portal pelajar direka bentuk berkonsepkan urus diri (*self-service*) yang fleksibel[cite: 483]. [cite_start]Pelajar **tidak memerlukan** kombinasi kata laluan atau pendaftaran akaun, sebaliknya hanya perlu menggunakan pautan UUID unik masing-masing[cite: 484].

### 2.1 Mengakses Portal Kendiri
[cite_start]Sila klik atau layari pautan khas pendaftaran kendiri yang diemelkan oleh pihak urus setia konvokesyen kepada anda[cite: 486]:
[cite_start]👉 `http://konvojtm.test/portal/{id-uuid-pelajar}` [cite: 487]

> [cite_start]⚠️ **Peringatan Keselamatan:** ID UUID di dalam pautan URL anda bertindak sebagai kunci keselamatan akses peribadi[cite: 488]. [cite_start]Jangan sekali-kali berkongsi pautan penuh ini kepada mana-mana pihak ketiga bagi mengelakkan data profil anda diubah tanpa izin[cite: 489].

### 2.2 Menyemak & Mengemas Kini Maklumat
1. [cite_start]Semak paparan profil data anda pada borang utama yang disediakan[cite: 491].
2. [cite_start]Sila ambil perhatian bahawa medan **Nama Penuh**, **Nombor Kad Pengenalan (IC)**, dan **Nombor Matrik** dikunci secara kekal (**Read-Only**) demi integriti maklumat akademik[cite: 492]. [cite_start]Sila hubungi urus setia sekiranya wujud ralat ejaan[cite: 493].
3. [cite_start]Pelajar dibenarkan membetulkan atau mengemas kini ruangan **Alamat Emel** dan **Nombor Telefon** sekiranya berlaku perubahan maklumat hubungan terkini[cite: 494].

### 2.3 Memuat Naik Resit Pembayaran
1. [cite_start]Lakukan pemindahan wang atau pembayaran yuran mengikut amaun pendaftaran rasmi majlis yang dipaparkan dalam portal anda[cite: 498].
2. [cite_start]Sediakan imej paparan atau dokumen bukti pembayaran dalam bentuk fail **PDF, JPG, JPEG, atau PNG** dengan saiz maksimum fail tidak melebihi **2MB**[cite: 499].
3. [cite_start]Klik pada zon muat naik fail resit pendaftaran, pilih dokumen transaksi anda, dan klik butang **"Simpan & Kemas Kini"** untuk menghantar[cite: 500].

### 2.4 Memahami Status Pendaftaran Anda
[cite_start]Status terkini pendaftaran anda boleh disemak secara langsung pada label status di bahagian sudut bawah kiri borang[cite: 502]:

* [cite_start]**Belum Bayar:** Pelajar belum memuat naik atau melampirkan sebarang dokumen bukti transaksi kewangan di dalam sistem[cite: 503].
* [cite_start]**Menunggu Semakan Admin:** Dokumen resit pendaftaran anda telah berjaya disimpan dan kini sedang berada dalam proses semakan silang oleh pihak urus setia pentadbir[cite: 504].
* [cite_start]**✓ Sah / Disahkan:** Pihak urus setia pentadbir telah meluluskan bukti transaksi anda[cite: 505]. [cite_start]Proses pendaftaran konvokesyen anda kini selesai sepenuhnya dan status kehadiran anda adalah sah[cite: 506].

---

## 3. Ringkasan Aliran Kerja Sistem (Workflow Summary)

[cite_start]Berikut menerangkan ringkasan kitaran aliran pendaftaran konvokesyen dari fasa persediaan pentadbir sehingga pendaftaran tamat dan disahkan[cite: 508]:

| Fasa | Aliran Kerja | Tindakan Utama |
| :---: | :--- | :--- |
| **1** | [cite_start]**Langkah 1:** Admin Cipta Sesi Acara Konvo [cite: 511, 512] | [cite_start]Menetapkan tajuk, tarikh, kadar yuran dan status aktif[cite: 461, 462, 463, 464]. |
| **2** | [cite_start]**Langkah 2:** Pelajar Terima Pautan Portal & Kemas Kini Profil [cite: 513, 514] | [cite_start]Mengakses portal menggunakan UUID dan mengemas kini info hubungan[cite: 484, 494]. |
| **3** | [cite_start]**Langkah 3:** Pelajar Muat Naik Fail Bukti Resit Yuran [cite: 515, 522] | [cite_start]Menyediakan dokumen resit (< 2MB) dan klik "Simpan & Kemas Kini"[cite: 499, 500]. |
| **4** | [cite_start]**Langkah 4:** Nama Masuk Giliran Semakan Dokumen Admin [cite: 527, 534] | [cite_start]Sistem mengemas kini status borang pelajar kepada "Menunggu Semakan Admin"[cite: 504]. |
| **5** | [cite_start]**Langkah 5:** Admin Klik "Sahkan Pembayaran" Pelajar [cite: 525, 526] | [cite_start]Menyemak silang resit kewangan dan meluluskan rekod[cite: 479, 480]. |
| **Selesai** | [cite_start]**Pendaftaran Lengkap & Sah Berdaftar** [cite: 523, 524] | [cite_start]Status bertukar kepada Sah/Disahkan dan pendaftaran tamat dengan jayanya[cite: 505, 506]. |
