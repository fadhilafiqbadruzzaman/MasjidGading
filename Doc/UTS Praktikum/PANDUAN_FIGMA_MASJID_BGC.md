# 📱 Panduan Lengkap UTS Praktikum - Perancangan UI Aplikasi Masjid BGC

### Proyek: Aplikasi Masjid Baitul Ghafur Cisoka (BGC)

🔗 **Repository GitHub:** [https://github.com/fadhilafiqbadruzzaman/PraktikumPemob.git](https://github.com/fadhilafiqbadruzzaman/PraktikumPemob.git)  
🎨 **Figma Design:** [https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=ki6OkTcVcOZdlc2P-1](https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=ki6OkTcVcOZdlc2P-1)

**Standar Frame:** Android Standar (360 × 800 px)  
**Alur Utama:** Jamaah Flow (Login → Beranda → Warta → Agenda → Donasi → Transparansi → Profil)

---

## 📂 Daftar Isi Folder `UTS Praktikum`

| No | Nama File | Ukuran | Keterangan |
|:--:|:---|:---|:---|
| 1 | `PANDUAN_FIGMA_MASJID_BGC.md` | ~5 KB | File ini — panduan lengkap pengerjaan & pemindahan ke Figma |
| 2 | `MasjidBGC_Wireframe_LoFi.html` | ~21 KB | Wireframe Low-Fidelity (7 screen, grayscale, layout rangka fungsional) |
| 3 | `MasjidBGC_Mockup_HiFi.html` | ~34 KB | Mockup High-Fidelity (7 screen, warna hijau islami `#1B5E20`, identik Flutter) |
| 4 | `MasjidBGC_Interactive_Prototype.html` | ~31 KB | Prototype interaktif dengan navigasi klik antar layar (bisa dijalankan langsung di browser) |
| 5 | `LaporanWireframe-Mockup-Prototype.pdf` | ~2.3 MB | Laporan lengkap UTS: latar belakang, wireframe, mockup, prototype, analisis UI/UX |

---

## 📝 Penjelasan Setiap File

### 1. `MasjidBGC_Wireframe_LoFi.html` — Wireframe Low-Fidelity
Rancangan kerangka tampilan awal aplikasi dalam gaya **grayscale** (abu-abu hitam putih). Tidak ada warna brand, hanya menunjukkan:
- **Tata letak (layout)** dan penempatan elemen UI
- **Hierarki konten** (judul, subjudul, paragraf, tombol)
- **Navigasi dasar** antar halaman

**7 Screen yang ada:**
1. **Login** — Form masuk akun (email & password)
2. **Beranda (Home)** — Header masjid, jadwal sholat, grid layanan, warta terkini
3. **Warta & Pengumuman** — Daftar berita/pengumuman dengan filter kategori
4. **Agenda & Kegiatan** — Jadwal kajian dan kegiatan masjid
5. **Donasi Online** — Form donasi dengan pilihan nominal & metode pembayaran
6. **Transparansi Keuangan** — Saldo kas, pemasukan/pengeluaran, grafik alokasi dana
7. **Profil Jamaah** — Identitas user, riwayat donasi, pengaturan, logout

**Cara buka:** Klik 2x file HTML-nya → langsung terbuka di browser, semua 7 layar tampil berdampingan.

---

### 2. `MasjidBGC_Mockup_HiFi.html` — Mockup High-Fidelity
Desain visual **final dan detail** yang identik 1:1 dengan kode Flutter di repository. Fitur:
- Warna brand hijau islami (`#1B5E20`, `#0D330F`, `#4CAF50`)
- Aksen emas (`#D4AF37`) untuk penanda waktu sholat berikutnya
- Tipografi Roboto, ikon emoji, card shadows, rounded corners
- Semua 7 layar ditampilkan berdampingan dengan ukuran 360 × 800 px

**Perbedaan dengan Wireframe:** Wireframe = kerangka abu-abu. Mockup = tampilan final berwarna lengkap.

---

### 3. `MasjidBGC_Interactive_Prototype.html` — Prototype Interaktif
Simulasi aplikasi yang **bisa diklik langsung di browser** layaknya aplikasi nyata:
- Klik **"MASUK KE AKUN"** → pindah ke Beranda
- Klik **bottom navigation** (Beranda, Warta, Kegiatan, Donasi, Profil) → pindah layar
- Klik **"DONASI SEKARANG"** → masuk ke form donasi
- Klik **"KELUAR DARI AKUN"** → kembali ke Login
- Klik **menu layanan** (Warta, Kegiatan, Keuangan, dll.) → navigasi ke layar terkait

**Format:** Tampilan mockup HP tunggal (360×800) dengan dark background, screen berganti-ganti via JavaScript `navigateTo()`.

---

### 4. `LaporanWireframe-Mockup-Prototype.pdf` — Laporan UTS
Dokumen PDF laporan lengkap berisi:
- Latar belakang proyek & tujuan
- Deskripsi aplikasi Masjid BGC
- Screenshot & penjelasan Wireframe (Lo-Fi)
- Screenshot & penjelasan Mockup (Hi-Fi)
- Screenshot & penjelasan Prototype Interaktif
- Analisis perancangan UI/UX
- Kesimpulan

---

## 🎨 Langkah Pemindahan ke Figma (html.to.design)

### Persiapan: Buat 3 Page di Figma
1. Buka file Figma: [Figma Project Masjid BGC](https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=ki6OkTcVcOZdlc2P-1)
2. Di panel kiri atas (bagian Pages), buat/rename menjadi 3 halaman:
   - **Page 1:** `1. Wireframe (Lo-Fi)`
   - **Page 2:** `2. Mockup (Hi-Fi)`
   - **Page 3:** `3. Prototype`

---

### Langkah A: Import Wireframe (Page 1)
1. Di Figma, pilih halaman **`1. Wireframe (Lo-Fi)`**.
2. Klik kanan di kanvas kosong → **`Plugins`** → **`html.to.design`**.
3. Di jendela plugin:
   - Buka file `MasjidBGC_Wireframe_LoFi.html` di browser Chrome, lalu copy URL-nya ke plugin.
   - Atau gunakan opsi **File upload** dan drag file HTML-nya.
   - Set viewport ke **Mobile (360 px)**.
   - Klik **Import**.
4. Setelah import, **klik frame pembungkus luar** → tekan `Ctrl + Shift + G` (Ungroup) agar ke-7 screen menjadi frame terpisah.

---

### Langkah B: Import Mockup (Page 2)
1. Pindah ke halaman **`2. Mockup (Hi-Fi)`**.
2. Buka kembali plugin **`html.to.design`**.
3. Masukkan file `MasjidBGC_Mockup_HiFi.html`.
4. Klik **Import**.
5. Ungroup frame luar (`Ctrl + Shift + G`) agar semua 7 layar menjadi frame sendiri-sendiri.

---

### Langkah C: Menyusun Prototype (Page 3)
Prototype di Figma = frame Mockup yang dihubungkan dengan **tali interaksi biru**:

1. Pindah ke halaman **`3. Prototype`**.
2. **Copy** semua frame dari Page 2 (Mockup) → **Paste** di Page 3.
3. Di panel kanan Figma, klik tab **`Prototype`** (sebelah tab *Design*).
4. Hubungkan interaksi klik sesuai tabel berikut:

| Elemen yang Diklik | Trigger | Tujuan Layar (*Navigate to*) |
|:---|:---|:---|
| Tombol **"MASUK KE AKUN"** (Login) | *On click* | Frame **Beranda (Home)** |
| Bottom Nav **"Beranda"** | *On click* | Frame **Beranda (Home)** |
| Bottom Nav **"Warta"** | *On click* | Frame **Warta & Pengumuman** |
| Bottom Nav **"Kegiatan"** | *On click* | Frame **Agenda Kegiatan** |
| Bottom Nav **"Donasi"** | *On click* | Frame **Form Donasi** |
| Bottom Nav **"Profil"** | *On click* | Frame **Profil Jamaah** |
| Tombol **"DONASI SEKARANG"** (Beranda) | *On click* | Frame **Form Donasi** |
| Menu **"Keuangan"** (Layanan Beranda) | *On click* | Frame **Transparansi Kas** |
| Tombol **Kembali (←)** (Form Donasi) | *On click* | *Back* / Frame **Beranda** |
| Tombol **"KELUAR DARI AKUN"** (Profil) | *On click* | Frame **Login** |

**Cara menghubungkan:**
1. Klik elemen/tombol yang ingin dijadikan trigger.
2. Di panel kanan (tab Prototype), klik **`+`** di bagian **Interactions**.
3. Set trigger: **On click** → **Navigate to** → pilih frame tujuan.
4. Atau: hover di atas elemen, muncul **lingkaran biru kecil (+)** di sisi kanan → klik & tarik tali ke frame tujuan.

---

### Langkah D: Menjalankan Prototype
Klik tombol **Present / Play (▶️)** di pojok kanan atas Figma → prototype interaktif langsung berjalan!

---

## 🔗 Link Penting

| Sumber | Link |
|:---|:---|
| **GitHub Repository** | [github.com/fadhilafiqbadruzzaman/PraktikumPemob](https://github.com/fadhilafiqbadruzzaman/PraktikumPemob.git) |
| **Figma Design** | [Figma - Masjid BGC](https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=ki6OkTcVcOZdlc2P-1) |

---

## 💡 Tips & Troubleshooting

- **Hanya muncul 1 layar saat import?** → File `MasjidBGC_Interactive_Prototype.html` menyembunyikan layar lain dengan CSS `display: none`. Gunakan file `Wireframe` atau `Mockup` yang menampilkan ke-7 layar sekaligus.
- **Tidak bisa tarik tali interaksi?** → Pastikan sudah Ungroup frame luar (`Ctrl + Shift + G`) agar setiap screen menjadi frame independen.
- **Interaction masih `None`?** → Klik tulisan `None` di panel Prototype → ubah ke **Navigate to** → pilih frame tujuan.
- **Layar terlalu kecil di Figma?** → Zoom ke 100% (`Ctrl + 0`), atau klik frame lalu tekan `Shift + 1` untuk zoom-to-fit.
