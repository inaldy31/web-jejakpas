<?php

return [

    'modal' => [

        'description' => <<<'BLADE'
            Anda memerlukan aplikasi seperti Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) untuk menyelesaikan proses ini.
            BLADE,

        'content' => [

            'recovery_codes' => [

                'instruction' => 'Harap simpan kode pemulihan berikut pada tempat yang aman. Kode ini hanya akan ditampilkan sekali, tetapi akan diperlukan jika Anda kehilangan akses ke aplikasi authenticator Anda:',

            ],

        ],

        'form' => [

            'code' => [

                'below_content' => 'Saat masuk, kode 6 digit dari aplikasi authenticator diminta sesudah kata sandi, kecuali di perangkat yang Anda minta diingat. Kode juga diminta untuk tindakan sensitif.',

            ],

        ],

    ],

];
