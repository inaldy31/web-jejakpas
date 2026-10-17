<?php

return [
    // Setiap proxy di rantai depan aplikasi (proxy TLS, juga WAF atau penyeimbang beban di depannya; IP atau rentang CIDR, dipisah
    // koma) yang header X-Forwarded-For dan -Proto-nya dipercaya (header lain diabaikan, lihat bootstrap/app.php); kosong = tidak ada.
    // Middleware TrustProxies bawaan Laravel membaca kunci ini pada setiap request, jadi HSTS, URL https://, cookie Secure, dan IP
    // pengunjung benar di belakang proxy tanpa mempercayai header yang sama dari klien lain.
    'proxies' => array_values(array_filter(array_map(trim(...), explode(',', (string) env('TRUSTED_PROXIES', ''))))),
];
