@extends('layouts.master')

@section('title', 'Detail Transaksi - ' . $transaction->code)

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">Detail Transaksi</h2>
                        <p class="header-subtitle mb-0">Kode: <span class="fw-semibold">{{ $transaction->code }}</span></p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('transactions.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
                        </a>
                        <button class="btn btn-info text-white" onclick="printReceipt()">
                            <i class="fas fa-print me-1"></i> Cetak Struk
                        </button>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi
                                Transaksi</h5>
                            @php
                                $transactionInfo = [
                                    'Tanggal Transaksi' => $transaction->created_at
                                        ? $transaction->created_at->translatedFormat('d M Y, H:i:s')
                                        : 'N/A',
                                    'Nama Pelanggan' => $transaction->customer_name ?: '-',
                                    'Nomor Meja' => $transaction->table_number ?: '-',
                                    'Kasir' => $transaction->user->name ?? 'N/A',
                                    'Metode Pembayaran' => Str::title(
                                        str_replace('_', ' ', $transaction->payment_method),
                                    ),
                                ];
                            @endphp
                            <ul class="info-list info-list-dense">
                                @foreach ($transactionInfo as $label => $value)
                                    <li><span class="label">{{ $label }}</span> <span
                                            class="value">{{ $value }}</span></li>
                                @endforeach
                                <li>
                                    <span class="label">Status Pembayaran</span>
                                    <span class="value">
                                        @php
                                            $paymentStatusClass =
                                                $transaction->payment_status == 'paid'
                                                    ? 'bg-success-subtle'
                                                    : 'bg-danger-subtle';
                                        @endphp
                                        <span class="badge {{ $paymentStatusClass }}">
                                            {{ ucfirst($transaction->payment_status) }}
                                        </span>
                                    </span>
                                </li>
                                <li>
                                    <span class="label">Status Pesanan</span>
                                    <span class="value">
                                        @php
                                            $statusConfig = [
                                                'in_progress' => [
                                                    'class' =>
                                                        'bg-info-subtle text-info-emphasis border border-info-subtle',
                                                    'label' => 'Dalam Proses',
                                                ],
                                                'done' => [
                                                    'class' =>
                                                        'bg-success-subtle text-success-emphasis border border-success-subtle',
                                                    'label' => 'Selesai',
                                                ],
                                                'cancelled' => [
                                                    'class' =>
                                                        'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                                                    'label' => 'Dibatalkan',
                                                ],
                                            ];
                                            $currentStatus = $statusConfig[$transaction->status] ?? [
                                                'class' =>
                                                    'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
                                                'label' => Str::title(str_replace('_', ' ', $transaction->status)),
                                            ];
                                        @endphp
                                        <span class="badge {{ $currentStatus['class'] }}">
                                            {{ $currentStatus['label'] }}
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="content-block">
                            <h5 class="content-block-title"><i class="fas fa-receipt me-2 text-success"></i>Detail Item
                                Dipesan</h5>
                            @if ($transaction->transactionDetail->count()) 
                                <div class="table-responsive">
                                    <table class="table modern-table table-sm">
                                        <thead>
                                            <tr>
                                                <th class="ps-3">#</th>
                                                <th>Produk</th>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-end">Harga Satuan</th>
                                                <th class="text-end pe-3">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transaction->transactionDetail as $index => $detailItem)
                                                <tr>
                                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="fw-medium">
                                                            {{ $detailItem->product->name ?? 'Produk Tidak Ditemukan' }}
                                                        </div>
                                                        @if (isset($detailItem->product->code))
                                                            <small class="text-muted">Kode:
                                                                {{ $detailItem->product->code }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $detailItem->quantity }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($detailItem->product->selling_price ?? 0, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end fw-semibold pe-3">Rp
                                                        {{ number_format($detailItem->total_price, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-summary">
                                    <span>Total Keseluruhan</span>
                                    <span class="price-value">Rp
                                        {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="p-4 text-center">
                                    <p class="text-muted mb-0">Tidak ada item dalam transaksi ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Total
                                Pembayaran</h5>
                            <div class="text-center py-3">
                                <div class="display-5 fw-bolder text-primary mb-2">Rp
                                    {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                @if ($transaction->payment_status == 'paid')
                                    <p class="text-success fw-bold mb-0"><i class="fas fa-check-circle me-1"></i>Lunas</p>
                                @else
                                    <p class="text-danger fw-bold mb-0"><i class="fas fa-hourglass-half me-1"></i>Menunggu
                                        Pembayaran</p>
                                @endif
                            </div>
                        </div>

                        <div class="content-block">
                            <h5 class="content-block-title"><i class="fas fa-cogs me-2 text-secondary"></i>Aksi Transaksi
                            </h5>
                            <div class="d-grid gap-2">
                                @if ($transaction->status == 'in_progress')
                                    <form action="{{ route('transactions.updateStatus', $transaction->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Anda yakin ingin menandai transaksi ini sebagai SELESAI?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check-circle me-1"></i> Tandai Selesai
                                        </button>
                                    </form>
                                    <form action="{{ route('transactions.updateStatus', $transaction->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Anda yakin ingin MEMBATALKAN transaksi ini? Tindakan ini tidak dapat diurungkan.');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-times-circle me-1"></i> Batalkan Transaksi
                                        </button>
                                    </form>
                                @elseif($transaction->status == 'done')
                                    <p class="text-center text-muted mt-2 mb-0">Transaksi telah selesai.</p>
                                @elseif($transaction->status == 'cancelled')
                                    <p class="text-center text-muted mt-2 mb-0">Transaksi telah dibatalkan.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('content.transaction.main.components.receipt', ['detail' => $transaction])
    </div>
@endsection

@push('scripts')
    <script>
        function printReceipt() {
            const receiptElement = document.getElementById('printable-receipt');
            if (receiptElement) {
                const iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);

                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write('<html><head><title>Struk</title>');
                doc.write('<style>');
                doc.write(`
                @media print {
                    body { margin: 0; padding: 5mm; color: #000; }
                    .receipt-container { width: 100% !important; max-width: 100% !important; margin: 0; font-size: 10pt; } 
                    /* Additional print-specific overrides if needed */
                }
                body { font-family: 'Courier New', Courier, monospace; margin: 0; padding:0; color: #000; }
                .receipt-container { max-width: 320px; margin: 0 auto; font-size: 12px; }
                h4 { text-align: center; margin-top: 5px; margin-bottom: 10px; font-size:14px; }
                hr { border: 0; border-top: 1px dashed #000; margin: 10px 0; }
            `);
                doc.write('</style></head><body>');
                doc.write(receiptElement.innerHTML);
                doc.write('</body></html>');
                doc.close();

                iframe.contentWindow.focus();
                iframe.contentWindow.print();

                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 1000); 

            } else {
                console.error('Printable receipt element not found.');
            }
        }
    </script>
@endpush
