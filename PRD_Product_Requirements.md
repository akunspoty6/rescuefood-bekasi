# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD)
## PROYEK: RESCUEFOOD BEKASI (FoodRescue.id)
**Kategori:** Civic-Tech & Enterprise Food Security SaaS  
**Disusun Oleh:** Pak PM (Product Lead) & Wowo (Root Architect)  
**Klien / Sponsor:** Bos Rafi (The Big Boss & Initiator)  
**Status:** APPROVED & IN-EXECUTION  
**Standar Format:** Atlassian Enterprise PRD Standard  

---

## 1. Executive Summary & Problem Statement
Indonesia merupakan salah satu penghasil sampah makanan (*food loss and waste*) terbesar di Asia Tenggara, mencapai 23–48 juta ton per tahun (kajian Bappenas). Di tingkat hyper-local (khususnya wilayah urban padat seperti Kota Bekasi), restoran, hotel, toko roti (*bakery*), dan kafe membuang makanan berkualitas tinggi setiap malam hanya karena tidak terjual sebelum jam operasional berakhir.

Pada saat yang sama, kelompok mahasiswa rantau, pekerja upah minimum, dan keluarga prasejahtera menghadapi inflasi harga pangan. **RescueFood Bekasi** hadir sebagai jembatan *high-concurrency real-time* yang menghubungkan surplus makanan layak makan dengan masyarakat melalui diskon 50–80%, sekaligus mengonversi transaksi menjadi metrik ketahanan pangan resmi terstandarisasi Dinas Ketahanan Pangan dan Perikanan (DKPP).

---

## 2. Strategic Objectives & Success Metrics
1. **Zero Food Waste Conversion:** Mengalihkan minimal 80% surplus makanan harian mitra terdaftar dari pembuangan TPA (Tempat Pembuangan Akhir Sumur Batu / Bantar Gebang).
2. **Sub-second Concurrency Handshake:** Mampu melayani *flash sale* rebutan kantong misteri (*Mystery Bag*) tanpa *race condition* atau *double-spending*.
3. **Guaranteed Escrow Protection:** Menjamin dana pembeli aman 100% dan merchant terhindar dari klaim palsu melalui verifikasi dua arah (Dual-Handshake OTP + Midtrans/Xendit Escrow).
4. **Verifiable ESG Impact:** Menerbitkan sertifikat keberlanjutan berbasis metodologi Life Cycle Assessment (LCA) FAO/KLHK yang dapat diaudit untuk laporan CSR merchant.

---

## 3. User Personas & Core Journeys

### A. Persona 1: "Dimas" (Mahasiswa Rantau / Pemburu Makanan Hemat)
- **Motivasi:** Mencari makanan lezat, higienis, dan bergizi dengan anggaran Rp 15.000 – Rp 30.000 menjelang makan malam.
- **Pain Point:** Makanan diskon malam hari sering habis tidak menentu atau harus datang langsung ke gerai tanpa kepastian stok.
- **Core Journey:** Buka web ➔ Cek radar peta terdekat ➔ Pilih Mystery Bag Roti Artisan ➔ Bayar via QRIS Midtrans ➔ Dapatkan tiket OTP ➔ Ambil di resto pukul 21.00 WIB.

### B. Persona 2: "Ibu Ratna" (Pemilik Toko Roti & Kafe Lokal)
- **Motivasi:** Menutup biaya bahan baku (*cost of goods sold*) daripada membuang 10–15 porsi sisa tiap malam.
- **Pain Point:** Repot memfoto satu per satu sisa kue/roti di media sosial.
- **Core Journey:** Jam 19.30 buka dashboard merchant ➔ Input 5 Mystery Bag @ Rp 25.000 (Harga Asli Rp 75.000) ➔ Jam 21.15 verifikasi OTP pelanggan ➔ Dana escrow cair otomatis ke rekening.

### C. Persona 3: "Dinas Ketahanan Pangan & Tim ESG" (Regulator & Auditor)
- **Motivasi:** Memantau ketahanan pangan wilayah, penurunan timbulan sampah organik, dan keterjangkauan kalori masyarakat.
- **Core Journey:** Mengakses dashboard monitoring agregat ➔ Mengekspor sertifikat ESG dan laporan serapan kalori berbasis kecamatan.

---

## 4. High-End Feature Breakdown

| ID | Fitur | Deskripsi Teknis | Prioritas |
|---|---|---|---|
| FT-01 | **Surprise Mystery Bag Engine** | Pengelompokan surplus makanan ke dalam paket misteri tanpa perlu katalogisasi eceran manual. | P0 (Must Have) |
| FT-02 | **Anti-Race Condition Flash Engine** | Transaksi atomik database (`lockForUpdate` / atomic decrement) untuk mencegah overselling. | P0 (Must Have) |
| FT-03 | **Midtrans Escrow Gateway** | Penahanan dana di rekening pool berstatus `PAYMENT_HELD` hingga serah terima fisik valid. | P0 (Must Have) |
| FT-04 | **Dual-Handshake 6-Digit OTP** | Kode verifikasi acak terenkripsi yang wajib diinput kasir toko untuk membuktikan penyerahan fisik unit. | P0 (Must Have) |
| FT-05 | **DKPP Food Salvage Calculator** | Algoritma konversi berat makanan ke CO₂e (2.5x), air virtual (400L/porsi), dan Angka Kecukupan Gizi (AKG). | P0 (X-Factor) |
| FT-06 | **Interactive Hyper-Local Map** | Integrasi peta Leaflet.js dengan pin interaktif lokasi mitra kuliner dan radius jemput. | P1 (High) |
| FT-07 | **Automated ESG Certificate Exporter** | Fitur cetak sertifikat dampak lingkungan digital untuk mitra dan donatur. | P1 (High) |

---

## 5. Non-Functional Requirements (NFR)
- **Kinerja:** Respon pemesanan < 300ms pada lonjakan akses serentak.
- **Keamanan:** Sanitasi penuh parameter SQL, pencegahan XSS innerHTML, hash OTP menggunakan bcrypt / SHA-256 salt.
- **Kompatibilitas:** Responsif penuh pada perangkat seluler (layar 360px hingga 430px) dengan target sentuh tombol minimal 44px.
- **Reliabilitas:** Fallback simulasi transaksi offline tanpa memutus pengalaman pengguna saat API eksternal mengalami gangguan.
