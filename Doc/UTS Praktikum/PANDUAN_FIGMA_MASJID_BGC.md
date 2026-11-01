# Panduan Lengkap Pemindahan ke Figma (Wireframe, Mockup, Prototype)
### Proyek: Aplikasi Masjid BGC (Bukit Gading Cisoka)
**Figma Canvas URL:** https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=nb43TIIo7uBDx1Fp-1  
**Standar Frame:** Android Standar (360 × 800 px)  
**Alur Utama:** Jamaah Flow (Login, Beranda, Warta, Agenda, Donasi, Transparansi, Profil)

---

## 1. Menjawab Masalah Menu "Development"
Dari tangkapan layar yang kamu kirimkan:
1. **Gambar 1:** Kamu mencari kata "Development" di search box *Plugins & Widgets Community*. Menu itu hanya mencari plugin publik buatan komunitas di internet.
2. **Gambar 2:** Di menu klik kanan kamu, kabar baiknya kamu **sudah menginstal plugin `html.to.design`** (muncul di daftar *Recents*).
   * Inilah alat tercepat dan paling presisi untuk mengonversi tampilan langsung menjadi layer frame Figma asli (Auto Layout, Text editable, Vector, Colors).

---

## 2. File yang Telah Dibuatkan Khusus Untukmu
Semua file sudah tersimpan di folder project: `Doc/Figma/`

| File | Fungsi | Tujuan di Figma |
| :--- | :--- | :--- |
| `MasjidBGC_Wireframe_LoFi.html` | Rancangan Wireframe Low-Fidelity (Grayscale, layout balok, rangka fungsional) | **Page 1: Wireframe** |
| `MasjidBGC_Mockup_HiFi.html` | Desain High-Fidelity 1:1 identik dengan kode Flutter (`#1B5E20`, `#D4AF37`, jadwal sholat, kartu donasi, dsb.) | **Page 2: Mockup** |
| `MasjidBGC_Interactive_Prototype.html` | Prototype web interaktif yang bisa diklik langsung di browser sebelum/sesudah dioper ke Figma | Preview & Validasi Interaksi |

---

## 3. Langkah Praktis Memindahkan ke Figma (3 Halaman)

### Langkah A: Siapkan 3 Page di Figma
1. Buka file Figma kamu: [Figma Project Masjid BGC](https://www.figma.com/design/8E3McIpb3fPIoRlAPnzcrq/Untitled?node-id=0-1&t=nb43TIIo7uBDx1Fp-1)
2. Di panel kiri atas (bagian Pages), buat / rename menjadi 3 halaman:
   * **Page 1:** `1. Wireframe (Lo-Fi)`
   * **Page 2:** `2. Mockup (Hi-Fi)`
   * **Page 3:** `3. Prototype`

---

### Langkah B: Import Page 1 (Wireframe)
1. Di Figma, pilih halaman **`1. Wireframe (Lo-Fi)`**.
2. Klik kanan di kanvas kosong > pilih **`Plugins`** > pilih **`html.to.design`**.
3. Di jendela plugin `html.to.design`:
   * Pilih opsi **HTML / File upload** (atau buka file `Doc/Figma/MasjidBGC_Wireframe_LoFi.html` di browser Chrome, lalu copy URL lokalnya / drag file HTML-nya ke jendela plugin).
   * Set viewport ke **Mobile (360 px)**.
   * Klik tombol **Import**.
4. Semua 7 frame wireframe Android (360 × 800) akan langsung ter-generate di kanvas sebagai layer Figma asli!

---

### Langkah C: Import Page 2 (Mockup)
1. Di panel kiri Figma, pindah ke halaman **`2. Mockup (Hi-Fi)`**.
2. Buka kembali plugin **`html.to.design`**.
3. Masukkan file `Doc/Figma/MasjidBGC_Mockup_HiFi.html`.
4. Klik **Import**.
5. Semua 7 layar mockup dengan warna hijau islami (`#1B5E20`), kartu donasi, jadwal sholat, dan bottom nav akan langsung ter-generate presisi 1:1.

---

### Langkah D: Menyusun Page 3 (Prototype)
Prototype di Figma adalah frame Mockup yang dihubungkan dengan panah interaksi biru:
1. Pindah ke halaman **`3. Prototype`**.
2. Copy semua frame yang ada di **Page 2 (Mockup)**, lalu Paste di **Page 3 (Prototype)**.
3. Di panel kanan Figma, klik tab **`Prototype`** (di sebelah tab *Design*).
4. Hubungkan interaksi klik (*On click -> Navigate to*) sesuai tabel berikut:

| Elemen yang Diklik | Aksi / Trigger | Tujuan Layar (*Navigate to*) |
| :--- | :--- | :--- |
| **Tombol "MASUK KE AKUN"** di Login | *On click* | Frame `2. Mockup - Beranda (Home)` |
| **Bottom Nav "Beranda"** | *On click* | Frame `2. Mockup - Beranda (Home)` |
| **Bottom Nav "Warta"** | *On click* | Frame `3. Mockup - Warta & Pengumuman` |
| **Bottom Nav "Kegiatan"** | *On click* | Frame `4. Mockup - Agenda Kegiatan` |
| **Bottom Nav "Donasi"** | *On click* | Frame `5. Mockup - Form Donasi` |
| **Bottom Nav "Profil"** | *On click* | Frame `7. Mockup - Profil Jamaah` |
| **Tombol "DONASI SEKARANG"** di Home | *On click* | Frame `5. Mockup - Form Donasi` |
| **Menu "Keuangan"** di Layanan Home | *On click* | Frame `6. Mockup - Transparansi Kas` |
| **Tombol Kembali (←)** di Form Donasi | *On click* | *Back* / Frame `2. Mockup - Beranda` |
| **Tombol "KELUAR DARI AKUN"** di Profil | *On click* | Frame `1. Mockup - Masuk Akun` |

5. Klik tombol **Present / Play (▶)** di pojok kanan atas Figma untuk menguji prototype interaktifnya!
