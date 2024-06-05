<x-presensi-layout>
    @slot('custom_style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }

        #map {
            height: 170px;
        }

    </style>
    @endslot

    <!-- App Header -->
    <div class="appHeader bg-success text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Kembali</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <div class="section full mt-2">
        <div class="section-title">Title</div>
        <div class="wide-block pt-2 pb-2">
            <input type="hidden" id="lokasi" class="form-control">
        </div>

        <div class="row" style="margin-top: 10px">
            <div class="col">
                <div class="webcam-capture"></div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @if($cek > 0)
                <button type="button" id="takeabsen" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>
                @else
                <button type="button" id="takeabsen" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button>
                @endif
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <div id="map"></div>
            </div>
        </div>
    </div>

    @slot('custom_script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            Webcam.set({
                height: 480
                , width: 640
                , image_format: 'jpeg'
                , jpeg_quality: 80
            });

            Webcam.attach('.webcam-capture');

            var lokasi = document.getElementById('lokasi');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
            }

            function successCallback(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                lokasi.value = latitude + ',' + longitude;
                var map = L.map('map').setView([latitude, longitude], 17);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                    , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                var marker = L.marker([latitude, longitude]).addTo(map);

                var circle = L.circle([latitude, longitude], {
                    color: 'red'
                    , fillColor: '#f03'
                    , fillOpacity: 0.5
                    , radius: 50
                }).addTo(map);
            }

            function errorCallback() {

            }

        });

        $(document).on('click', '#takeabsen', function(e) {
            e.preventDefault();

            // Ambil gambar dan jalankan AJAX di dalam callback untuk memastikan gambar sudah diambil
            Webcam.snap(function(uri) {
                var image = uri; // Pastikan variabel image terisi dalam scope ini
                var lokasi = $('#lokasi').val();
                // alert(lokasi);

                $.ajax({
                    type: "POST"
                    , url: "{{ route('presensi.store') }}"
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , image: image
                        , lokasi: lokasi
                    }
                    , cache: false
                    , success: function(response) {
                        if (response == 0) {
                            Swal.fire({
                                title: "Berhasil!"
                                , text: "Terima kasih, selamat bekerja!"
                                , icon: "success"
                                , confirmButtonText: "OK"
                            });
                            setTimeout(function() {
                                location.href = '/dashboard';
                            }, 3000);
                        } else {
                            Swal.fire({
                                title: "Eror!"
                                , text: "Maaf, Gagal Absen, Silahkan hubungi tim IT !"
                                , icon: "error"
                                , confirmButtonText: "OK"
                            });
                        }
                    }
                    , error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });

    </script>
    @endslot

</x-presensi-layout>
