@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <h3 class="pt-4 pb-2 text-dark">Permintaan Peminjaman Masuk</h3>
            <div class="card">
                <div class="card-header bg-warning text-dark">Menunggu Persetujuan</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Rencana Kembali</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    <tr>
                                        <td>{{ $loan->user->name }}</td>
                                        <td>{{ $loan->tool->nama_alat }}
                                            <br>
                                            <small class="badge bg-primary">Jumlah: {{ $loan->quantity }}</small>
                                        </td>
                                        <td>{{ $loan->tanggal_pinjam }}</td>
                                        <td>{{ $loan->tanggal_kembali_rencana }}</td>
                                        <td>
                                            <form action="{{ url('/petugas/approve/' . $loan->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button class="btn btn-success btn-sm">Setujui</button>
                                            </form>
                                            <form action="{{ url('/petugas/reject/' . $loan->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button class="btn btn-danger btn-sm">Tolak</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada permintaan baru</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <h3 class="pt-4 pb-2">Daftar belum kembali</h3>
            <div class="card">
                <div class="card-header bg-info text-dark">Monitor Peminjaman</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Status</th>
                                    <th>Rencana Kembali</th>
                                    <th>Tanggal Kembali Aktual</th>
                                    <th>Denda (Rp)</th>
                                    <th>Keterangan</th>
                                    <th>Upload & Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeLoans as $active)
                                    <tr id="loan-row-{{ $active->id }}">
                                        <td>{{ $active->user->name }}</td>
                                        <td>{{ $active->tool->nama_alat }}
                                            <br>
                                            <small class="badge bg-primary">Jumlah: {{ $active->quantity }}</small>
                                        </td>
                                        <td>
                                            @if ($active->status == 'diajukan')
                                                <span class="badge bg-primary">Konfirmasi</span>
                                            @elseif($active->status == 'disetujui')
                                                <span class="badge bg-warning text-dark">Dipinjam</span>
                                            @else
                                                <span class="badge bg-info">{{ $active->status }}</span>
                                            @endif
                                        <td class="tgl-rencana"
                                            data-tgl="{{ $active->tanggal_kembali_rencana->format('Y-m-d') }}">
                                            {{ $active->tanggal_kembali_rencana->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            <input type="date" name="tanggal_kembali_aktual"
                                                class="form-control form-control-sm tgl-aktual"
                                                data-id="{{ $active->id }}" value="{{ date('Y-m-d') }}" required>
                                        </td>
                                        <td class="denda-display" id="denda-{{ $active->id }}">
                                            <span class="text-muted">Rp 0</span>
                                        </td>
                                        <td>
                                            <textarea name="keterangan_kondisi" class="form-control form-control-sm" placeholder="Kondisi alat..." maxlength="500"
                                                rows="2" style="min-width: 120px;"></textarea>
                                        </td>
                                        <td>
                                            <form action="{{ url('/petugas/return/' . $active->id) }}" method="POST"
                                                class="return-form" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="tanggal_kembali_aktual" class="hidden-tgl"
                                                    value="">
                                                <input type="hidden" name="keterangan_kondisi" class="hidden-keterangan"
                                                    value="">
                                                <input type="file" name="gambar_kondisi"
                                                    class="form-control form-control-sm" accept="image/*"
                                                    style="min-width: 140px;">
                                                <small class="text-muted d-block">Max 2MB</small>
                                                <button type="submit" class="btn btn-primary btn-sm">Proses
                                                    Kembali</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <h3 class="pt-4 pb-2">Daftar Sudah Dikembalikan</h3>
            <div class="card">
                <div class="card-header bg-success text-dark">Monitor Peminjaman</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Status</th>
                                    <th>Tanggal Kembali Aktual</th>
                                    <th>Keterangan Kondisi</th>
                                    <th>Denda (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sudahDikembalikan as $sudah)
                                    <tr>
                                        <td>{{ $sudah->user->name }}</td>
                                        <td>{{ $sudah->tool->nama_alat }}
                                            <br>
                                            <small class="badge bg-primary">Jumlah: {{ $sudah->quantity }}</small>
                                        </td>
                                        <td><span class="badge bg-success">Sudah Kembali</span></td>
                                        <td>{{ $sudah->tanggal_kembali_aktual ? $sudah->tanggal_kembali_aktual->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>
                                            <div>{{ $sudah->keterangan_kondisi ?? '-' }}</div>
                                            <div>
                                                @if ($sudah->gambar_kondisi)
                                                    <a href="{{ asset('storage/' . $sudah->gambar_kondisi) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-image"></i> Lihat
                                                    </a>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-image"></i>none</span>
                                                @endif
                                        </td>
                    </div>
                    <td>
                        @if ($sudah->denda > 0)
                            <span class=" fw-bold">Rp
                                {{ number_format($sudah->denda, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">Rp 0</span>
                        @endif
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7  " class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menghitung denda
            function hitungDenda(tglRencana, tglAktual) {
                if (!tglRencana || !tglAktual) return 0;
                const rencana = new Date(tglRencana);
                const aktual = new Date(tglAktual);

                // Reset jam ke 00:00:00 untuk perbandingan hari
                rencana.setHours(0, 0, 0, 0);
                aktual.setHours(0, 0, 0, 0);

                if (aktual <= rencana) return 0;

                // Hitung selisih hari
                const diffTime = aktual - rencana;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                return diffDays * 5000;
            }

            // Fungsi untuk memperbarui tampilan denda
            function updateDenda(inputElement) {
                const row = inputElement.closest('tr');
                const tglRencanaElem = row.querySelector('.tgl-rencana');
                const tglAktualValue = inputElement.value;
                const loanId = inputElement.getAttribute('data-id');

                if (tglRencanaElem && tglAktualValue) {
                    const tglRencana = tglRencanaElem.getAttribute('data-tgl');
                    const denda = hitungDenda(tglRencana, tglAktualValue);
                    const dendaDisplay = document.getElementById('denda-' + loanId);
                    if (dendaDisplay) {
                        if (denda > 0) {
                            dendaDisplay.innerHTML = '<span class=" fw-bold">Rp ' + denda.toLocaleString('id-ID') +
                                '</span>';
                        } else {
                            dendaDisplay.innerHTML = '<span class="text-muted">Rp 0</span>';
                        }
                    }
                    // Update hidden input pada form dengan nilai tanggal aktual
                    const form = row.querySelector('.return-form');
                    const hiddenInput = form.querySelector('.hidden-tgl');
                    if (hiddenInput) {
                        hiddenInput.value = tglAktualValue;
                    }
                }
            }

            // event listener untuk semua input tanggal aktual
            const inputs = document.querySelectorAll('.tgl-aktual');
            inputs.forEach(input => {
                // Hitung denda saat halaman pertama kali dimuat
                updateDenda(input);

                // Hitung denda saat tanggal berubah
                input.addEventListener('change', function() {
                    updateDenda(this);
                });
            });
            // Event listener untuk keterangan dan gambar
            const keteranganInputs = document.querySelectorAll('textarea[name="keterangan_kondisi"]');
            const gambarInputs = document.querySelectorAll('input[name="gambar_kondisi"]');

            keteranganInputs.forEach((input, index) => {
                input.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const form = row.querySelector('.return-form');
                    const hiddenKeterangan = form.querySelector('.hidden-keterangan');
                    hiddenKeterangan.value = this.value;
                });
            });

            gambarInputs.forEach((input, index) => {
                input.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const form = row.querySelector('.return-form');
                    // Untuk file, kita perlu append langsung ke form
                    // Ini akan di-handle saat form submit
                });
            });

        });
    </script>
@endsection
