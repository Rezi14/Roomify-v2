@extends('layouts.user.app')

@section('title', 'Kontak Kami - Roomify')

@section('content')
    <style>
        .contact-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #f2f2f2;
        }

        .contact-info-box {
            border-radius: 20px;
            background: linear-gradient(135deg, #4a6cf7, #6c9dfd);
            color: white;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .map-wrapper {
            position: relative;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .map-header {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(5px);
        }

        .map-header i {
            color: #4a6cf7;
        }

        .modern-map {
            width: 100%;
            height: 380px;
            border: none;
            filter: grayscale(20%) brightness(96%);
        }

        .contact-title {
            font-weight: 800;
            font-size: 30px;
            letter-spacing: -0.5px;
        }
    </style>
    <div class="container py-5">
        {{-- ALERT SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row g-5">
            {{-- LEFT: CONTACT INFO --}}
            <div class="col-lg-4">
                <div class="contact-info-box h-100">

                    <h2 class="fw-bold mb-3">Hubungi Kami</h2>
                    <p class="mb-4 text-white-50">
                        Butuh bantuan? Kami siap melayani Anda.
                    </p>
                    <div class="d-flex align-items-start mb-4">
                        <div class="icon-circle me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Alamat</h6>
                            <p class="mb-0 text-white-50">Jl. Kaliurang Km 14.5, Yogyakarta</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div class="icon-circle me-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email</h6>
                            <p class="mb-0 text-white-50">support@roomify.com</p>
                        </div>
                    </div>

                    <hr class="border-light opacity-25 my-4">

                    {{-- SOCIAL MEDIA SECTION --}}
                    <h5 class="fw-bold mb-3">Media Sosial</h5>
                    <div class="d-flex align-items-start mb-3">
                        <div class="icon-circle me-3">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Instagram</h6>
                            <p class="mb-0 text-white-50">@roomify_id</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-1">
                        <div class="icon-circle me-3">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Facebook</h6>
                            <p class="mb-0 text-white-50">Roomify Official</p>
                        </div>
                    </div>

                </div>
            </div>
            {{-- RIGHT: CONTACT FORM --}}
            <div class="col-lg-8">
                <div class="contact-card">
                    <h3 class="contact-title text-primary mb-4">Kirim Pesan</h3>
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Nama" required>
                                    <label for="name">Nama Lengkap</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Email" required>
                                    <label for="email">Alamat Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="subject" id="subject"
                                        placeholder="Subjek" required>
                                    <label for="subject">Subjek Pesan</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="message" id="message" style="height: 150px" placeholder="Pesan Anda" required></textarea>
                                    <label for="message">Pesan Anda</label>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" type="submit">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- GOOGLE MAPS --}}
        <div class="map-wrapper">
            <div class="map-header">
                <i class="fas fa-map-marked-alt"></i>
                Lokasi Kami
            </div>
            <iframe class="modern-map"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.381626112423!2d110.408!3d-7.705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59d6911eadff%3A0xbbbdb9869f9e0fa0!2sKaliurang!5e0!3m2!1sid!2sid!4v1700000000000"
                loading="lazy" allowfullscreen>
            </iframe>
        </div>
    </div>
@endsection
