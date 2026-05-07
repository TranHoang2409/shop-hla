@extends('layouts.app')

@section('title', 'Contact')

@section('content')

    <!-- Start Map -->
    <div id="mapid" style="width: 100%; height: 300px;"></div>

    <!-- Start Contact -->
    <div class="container py-5">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-3">

                    <div class="card-body p-4">

                        <h4 class="text-center mb-4">Liên hệ với chúng tôi</h4>

                        <form method="post" role="form">

                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control mt-1" name="name" placeholder="Name">
                                </div>

                                <div class="form-group col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control mt-1" name="email" placeholder="Email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Subject</label>
                                <input type="text" class="form-control mt-1" name="subject" placeholder="Subject">
                            </div>

                            <div class="mb-3">
                                <label>Message</label>
                                <textarea class="form-control mt-1" name="message" rows="6" placeholder="Message"></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4">
                                    Gửi
                                </button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- End Contact -->

@endsection


@section('extra_js')

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        var mymap = L.map('mapid').setView([10.762622, 106.660172], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mymap);

        L.marker([10.762622, 106.660172]).addTo(mymap)
            .bindPopup('HLA Wifi Shop')
            .openPopup();
    </script>

@endsection
