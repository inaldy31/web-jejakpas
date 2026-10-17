<?php

return [
    'admin' => [
        'username' => env('ADMIN_USERNAME', 'admin'),
        'password' => env('ADMIN_PASSWORD'),
        'name' => env('ADMIN_NAME'),
    ],

    'security' => [
        'csp_report_only' => filter_var(env('CSP_REPORT_ONLY', false), FILTER_VALIDATE_BOOLEAN),

        'trusted_device_days' => 14,
    ],

    'upload' => [
        'max_kb' => 10240,
        'document_mimes' => [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
    ],

    'import' => [
        'max_rows' => 2000,
        'max_columns' => 40,
    ],

    'privacy' => [
        'min_group_size' => 3,
    ],

    'contact' => [
        'unit' => 'Subdirektorat Kerja Sama Pemasyarakatan, Direktorat Jenderal Pemasyarakatan',
        'email' => env('CONTACT_EMAIL'),
        'phone' => env('CONTACT_PHONE'),
        'address' => env('CONTACT_ADDRESS'),
    ],

    'recommendation' => [
        'siap_min_competency' => 37,
        'siap_min_confidence' => 61,
        'belum_siap_max_competency' => 24,
    ],

    'competency_scale' => [1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik'],

    'confidence_scale' => [1 => 'Sangat Tidak Sesuai', 2 => 'Tidak Sesuai', 3 => 'Ragu-ragu', 4 => 'Sesuai', 5 => 'Sangat Sesuai'],

    'competency_items' => [
        1 => ['dimension' => 'Penguasaan Teknik Dasar', 'indicator' => 'Memahami prinsip dan tahapan dasar sesuai bidang keterampilan'],
        2 => ['dimension' => 'Penguasaan Teknik Dasar', 'indicator' => 'Mampu menggunakan alat, bahan, dan/atau aplikasi secara tepat'],
        3 => ['dimension' => 'Penguasaan Teknik Dasar', 'indicator' => 'Mampu melaksanakan proses kerja sesuai prosedur'],
        4 => ['dimension' => 'Konsistensi Hasil Kerja', 'indicator' => 'Menghasilkan produk/hasil kerja sesuai standar yang ditetapkan'],
        5 => ['dimension' => 'Konsistensi Hasil Kerja', 'indicator' => 'Menjaga kualitas hasil secara konsisten'],
        6 => ['dimension' => 'Konsistensi Hasil Kerja', 'indicator' => 'Mampu mengidentifikasi dan memperbaiki kesalahan dalam proses kerja'],
        7 => ['dimension' => 'Kemampuan Menjelaskan', 'indicator' => 'Mampu menjelaskan tahapan kerja secara runtut'],
        8 => ['dimension' => 'Kemampuan Menjelaskan', 'indicator' => 'Menggunakan bahasa yang mudah dipahami Warga Binaan lain'],
        9 => ['dimension' => 'Kemampuan Menjelaskan', 'indicator' => 'Mampu mendemonstrasikan keterampilan kepada orang lain'],
        10 => ['dimension' => 'Kemampuan Menjelaskan', 'indicator' => 'Mampu menjawab pertanyaan terkait bidang keterampilan yang dikuasai'],
        11 => ['dimension' => 'Pengalaman Pelatihan', 'indicator' => 'Pernah mengikuti pelatihan yang relevan dengan bidang kompetensi'],
        12 => ['dimension' => 'Sertifikasi', 'indicator' => 'Memiliki sertifikat pelatihan dan/atau sertifikasi kompetensi yang relevan'],
    ],

    'confidence_sections' => [
        'A' => 'Kepercayaan Diri Tampil dan Berbicara',
        'B' => 'Menjawab Pertanyaan dan Menghadapi Situasi Baru',
        'C' => 'Menerima dan Mengelola Umpan Balik',
        'D' => 'Motivasi untuk Membina',
        'E' => 'Kesiapan Menjadi Panutan/Role Model',
    ],

    'confidence_items' => [
        1 => ['section' => 'A', 'statement' => 'Saya percaya diri berbicara di depan kelompok Warga Binaan.'],
        2 => ['section' => 'A', 'statement' => 'Saya mampu menjelaskan keterampilan yang saya kuasai kepada orang lain.'],
        3 => ['section' => 'A', 'statement' => 'Saya tetap percaya diri ketika harus melakukan demonstrasi di depan kelompok.'],
        4 => ['section' => 'A', 'statement' => 'Saya dapat menyampaikan penjelasan dengan runtut dan mudah dipahami.'],
        5 => ['section' => 'B', 'statement' => 'Saya nyaman menerima pertanyaan dari Warga Binaan lain.'],
        6 => ['section' => 'B', 'statement' => 'Saya tetap tenang ketika mendapat pertanyaan yang sulit.'],
        7 => ['section' => 'B', 'statement' => 'Saya berani mengatakan belum tahu apabila pertanyaan berada di luar kemampuan saya.'],
        8 => ['section' => 'B', 'statement' => 'Saya bersedia mencari informasi lebih lanjut ketika belum dapat menjawab pertanyaan.'],
        9 => ['section' => 'C', 'statement' => 'Saya terbuka menerima kritik terhadap cara saya bekerja atau menjelaskan.'],
        10 => ['section' => 'C', 'statement' => 'Saya dapat menerima saran tanpa merasa tersinggung.'],
        11 => ['section' => 'C', 'statement' => 'Saya menggunakan umpan balik untuk memperbaiki kemampuan saya.'],
        12 => ['section' => 'C', 'statement' => 'Saya bersedia mengubah cara mengajar apabila cara sebelumnya kurang efektif.'],
        13 => ['section' => 'D', 'statement' => 'Saya ingin membagikan keterampilan yang saya miliki kepada Warga Binaan lain.'],
        14 => ['section' => 'D', 'statement' => 'Saya merasa senang ketika Warga Binaan lain berhasil memahami keterampilan yang saya ajarkan.'],
        15 => ['section' => 'D', 'statement' => 'Saya bersedia membantu Warga Binaan lain meskipun tidak diminta oleh petugas.'],
        16 => ['section' => 'D', 'statement' => 'Saya bersedia meluangkan waktu untuk mendampingi Warga Binaan lain belajar.'],
        17 => ['section' => 'E', 'statement' => 'Saya merasa siap menjadi contoh positif bagi Warga Binaan lain.'],
        18 => ['section' => 'E', 'statement' => 'Saya mampu menjaga perilaku ketika dipercaya membantu Warga Binaan lain.'],
        19 => ['section' => 'E', 'statement' => 'Saya siap bertanggung jawab terhadap peran sebagai Peer Educator.'],
        20 => ['section' => 'E', 'statement' => 'Saya yakin dapat membantu menciptakan suasana belajar yang positif di antara Warga Binaan.'],
    ],

    'hero_photos' => [
        ['file' => 'images/hero/beranda-1-membatik.jpg', 'caption' => 'Ilustrasi pelatihan membatik dengan canting', 'source' => 'Canva'],
        ['file' => 'images/hero/beranda-2-menjahit.jpg', 'caption' => 'Ilustrasi pelatihan menjahit dengan mesin jahit', 'source' => 'Canva'],
        ['file' => 'images/hero/beranda-3-tata-boga.jpg', 'caption' => 'Ilustrasi pelatihan tata boga dengan kue tradisional', 'source' => 'Canva'],
        ['file' => 'images/hero/beranda-4-anyaman.jpg', 'caption' => 'Ilustrasi pelatihan menganyam keranjang', 'source' => 'Canva'],
        ['file' => 'images/hero/beranda-5-kelas-digital.jpg', 'caption' => 'Ilustrasi kelas keterampilan digital dengan laptop', 'source' => 'Canva'],
    ],

    'impact_hero_photos' => [
        ['file' => 'images/hero/dampak-1-peer-educator.jpg', 'caption' => 'Ilustrasi Peer Educator memperagakan cara menjahit kepada peserta lain', 'source' => 'Canva'],
        ['file' => 'images/hero/dampak-2-hasil-karya.jpg', 'caption' => 'Ilustrasi hasil karya pelatihan di meja pamer', 'source' => 'Canva'],
        ['file' => 'images/hero/dampak-3-sertifikat.jpg', 'caption' => 'Ilustrasi tangan memegang sertifikat pelatihan', 'source' => 'Canva'],
        ['file' => 'images/hero/dampak-4-belajar-bersama.jpg', 'caption' => 'Ilustrasi dua pasang tangan belajar menjahit bersama', 'source' => 'Canva'],
        ['file' => 'images/hero/dampak-5-tata-rias.jpg', 'caption' => 'Ilustrasi praktik tata rias dengan palet dan kuas', 'source' => 'Canva'],
    ],

    'partner_hero_photos' => [
        ['file' => 'images/hero/mitra-1-kesepakatan.jpg', 'caption' => 'Ilustrasi jabat tangan kesepakatan kerja sama dengan mitra', 'source' => 'Canva'],
        ['file' => 'images/hero/mitra-2-instruktur-mitra.jpg', 'caption' => 'Ilustrasi instruktur dari mitra mengajar di kelas pelatihan', 'source' => 'Canva'],
        ['file' => 'images/hero/mitra-3-pengemasan-produk.jpg', 'caption' => 'Ilustrasi pengemasan hasil karya untuk dipasarkan bersama mitra', 'source' => 'Canva'],
    ],

    'program_hero_photos' => [
        ['file' => 'images/hero/program-1-hidroponik.jpg', 'caption' => 'Ilustrasi pelatihan bertanam sayur hidroponik', 'source' => 'Canva'],
        ['file' => 'images/hero/program-2-barista.jpg', 'caption' => 'Ilustrasi pelatihan barista menyeduh kopi', 'source' => 'Canva'],
        ['file' => 'images/hero/program-3-ecoprint.jpg', 'caption' => 'Ilustrasi pelatihan ecoprint dengan daun pada kain', 'source' => 'Canva'],
    ],
];
