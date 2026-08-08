<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan {{ $penjualan->nota_jual }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 15mm;
            background-color: #fff;
            -webkit-print-color-adjust: exact !important;
        }
        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header img {
            max-height: 50px;
            margin-right: 12px;
        }
        .header-text h1 {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 10px;
            margin: 2px 0 0 0;
            color: #444;
        }
        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            margin-bottom: 14px;
            font-size: 11px;
        }
        .meta-row {
            display: flex;
        }
        .meta-label {
            width: 110px;
            color: #555;
        }
        .meta-value {
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 5px 7px;
            font-size: 10.5px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .footer-summary {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
        }
        .keterangan-box {
            width: 50%;
            font-size: 10px;
            color: #444;
            border: 1px dashed #ccc;
            padding: 8px;
            border-radius: 4px;
        }
        .total-box {
            width: 45%;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }
        .total-row.grand {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 13px;
            padding-top: 6px;
            margin-top: 4px;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 24px;
            text-align: center;
            page-break-inside: avoid;
        }
        .signature-title {
            font-size: 10.5px;
            margin-bottom: 45px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body { padding: 8mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #4C5C2D; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; shadow: 0 2px 4px rgba(0,0,0,0.2);">
            Cetak Nota Sekarang
        </button>
    </div>

    <div class="container">
        {{-- Kop Surat --}}
        <div class="header">
            @if(!empty($setting['logo']))
                <img src="data:image/png;base64,{{ base64_encode($setting['logo']) }}" alt="Logo">
            @endif
            <div class="header-text">
                <h1>{{ $setting['nama_instansi'] ?? 'RSIA IBI SURABAYA' }}</h1>
                <p>{{ $setting['alamat_instansi'] ?? 'Jl. Dupak No. 15, Surabaya' }} | Telp: {{ $setting['kontak'] ?? '-' }}</p>
            </div>
        </div>

        <div class="title">Nota Penjualan Obat & BHP</div>

        <div class="meta-grid">
            <div>
                <div class="meta-row"><span class="meta-label">No. Nota</span><span class="meta-value">: {{ $penjualan->nota_jual }}</span></div>
                <div class="meta-row"><span class="meta-label">Tanggal</span><span class="meta-value">: {{ $penjualan->tgl_jual ? $penjualan->tgl_jual->format('d/m/Y') : '-' }}</span></div>
                <div class="meta-row"><span class="meta-label">Jenis Jual</span><span class="meta-value">: {{ $penjualan->jns_jual }}</span></div>
            </div>
            <div>
                <div class="meta-row"><span class="meta-label">No. Rekam Medis</span><span class="meta-value">: {{ $penjualan->no_rkm_medis ?: '-' }}</span></div>
                <div class="meta-row"><span class="meta-label">Nama Pasien</span><span class="meta-value">: {{ $penjualan->nm_pasien ?: 'PASIEN UMUM' }}</span></div>
                <div class="meta-row"><span class="meta-label">Status Bayar</span><span class="meta-value">: {{ $penjualan->status }}</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Barang / Obat</th>
                    <th style="width: 45px;">Jumlah</th>
                    <th style="width: 50px;">Satuan</th>
                    <th style="width: 80px;" class="text-right">Harga (Rp)</th>
                    <th style="width: 70px;" class="text-right">Diskon</th>
                    <th style="width: 90px;" class="text-right">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->barang->nama_brng ?? $item->kode_brng }}</td>
                    <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->kode_sat }}</td>
                    <td class="text-right">{{ number_format($item->h_jual, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->bsr_dis, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-summary">
            <div class="keterangan-box">
                <strong>Catatan / Keterangan:</strong><br>
                {{ $penjualan->keterangan ?: 'Obat yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian.' }}
            </div>
            <div class="total-box">
                <div class="total-row"><span>Subtotal Item:</span><span>Rp {{ number_format($sumTotal, 0, ',', '.') }}</span></div>
                @if($penjualan->ppn > 0)
                <div class="total-row"><span>PPN:</span><span>Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</span></div>
                @endif
                @if($penjualan->ongkir > 0)
                <div class="total-row"><span>Ongkos Kirim:</span><span>Rp {{ number_format($penjualan->ongkir, 0, ',', '.') }}</span></div>
                @endif
                <div class="total-row grand"><span>GRAND TOTAL:</span><span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <div class="signature-grid">
            <div>
                <div class="signature-title">Pasien / Penerima</div>
                <div class="signature-name">( {{ $penjualan->nm_pasien ?: '....................' }} )</div>
            </div>
            <div>
                <div class="signature-title">Petugas Kasir / Farmasi</div>
                <div class="signature-name">( {{ $penjualan->petugas->nama ?? ($penjualan->nip ?: 'Petugas') }} )</div>
            </div>
        </div>
    </div>

</body>
</html>
