@extends('layouts.user.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow rounded-4 border-0">
                    <div class="card-header bg-white text-center py-4 border-bottom-0">
                        <h5 class="fw-bold mb-0 text-secondary">Sisa Waktu Pembayaran</h5>
                        <h2 class="display-4 fw-bold text-danger" id="countdown-timer">10:00</h2>
                    </div>

                    <div class="card-body text-center p-4">
                        <h5 class="mb-3">Scan QRIS di bawah ini</h5>
                        <h3 class="text-primary fw-bold">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</h3>

                        {{-- Gambar QRIS --}}
                        <div class="border p-3 d-inline-block rounded my-3 bg-light">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg"
                                alt="QRIS" width="200">
                        </div>

                        <p class="text-muted small">
                            Status pesanan akan berubah otomatis setelah pembayaran berhasil.<br>
                            Jangan tutup halaman ini.
                        </p>

                        <hr>

                        {{-- Tombol Batalkan --}}
                        <form action="{{ route('booking.payment.cancel', $pemesanan->id_pemesanan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Batalkan Pesanan Sekarang
                            </button>
                        </form>
                        <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded border border-warning">
                            <small class="fw-bold d-block mb-2">🔧 Area Simulasi (Developer)</small>
                            <a href="{{ route('simulation.qr.scan', $pemesanan->id_pemesanan) }}"
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-qr-code-scan"></i> Simulasi Scan QR Berhasil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Logika Countdown Timer
            // Mengambil waktu batas dari controller (format ISO string)
            const deadline = new Date("{{ $batasWaktu->toIso8601String() }}").getTime();

            const timerInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = deadline - now;

                // Menghitung menit dan detik
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Menampilkan di elemen
                document.getElementById("countdown-timer").innerHTML =
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (seconds < 10 ? "0" + seconds : seconds);

                // Jika waktu habis
                if (distance < 0) {
                    clearInterval(timerInterval);
                    document.getElementById("countdown-timer").innerHTML = "00:00";
                    // Redirect atau reload agar ditangani backend
                    window.location.href = "{{ route('dashboard') }}";
                }
            }, 1000);

            // Logika Cek Status Otomatis (Polling)
            const checkStatusInterval = setInterval(function() {
                fetch("{{ route('booking.payment.check', $pemesanan->id_pemesanan) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Jika sukses, redirect ke dashboard dengan pesan sukses
                            window.location.href = "{{ route('dashboard') }}?payment=success";
                        } else if (data.status === 'expired') {
                            // Jika expired, redirect ke dashboard
                            window.location.href = "{{ route('dashboard') }}?payment=expired";
                        }
                        // Jika pending, diam saja menunggu loop berikutnya
                    })
                    .catch(error => console.error('Error:', error));
            }, 3000); // Cek setiap 3 detik
        </script>
    @endpush

@endsection
