<div id="printable-receipt" style="display: none;">
    <div class="receipt-container"
        style="max-width: 380px; margin: 0 auto; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000;">
        <h4 style="text-align:center; margin-bottom: 10px; font-size: 14px;">STRUK PEMBAYARAN</h4>
        <hr style="border: 0; border-top: 1px dashed #000; margin: 5px 0;">
        <div class="info" style="font-size: 11px;">
            <p style="margin: 2px 0; display: flex; justify-content: space-between;">
                <span><strong>No. Struk:</strong></span>
                <span>{{ $detail->code }}</span>
            </p>
            <p style="margin: 2px 0; display: flex; justify-content: space-between;">
                <span><strong>Tanggal:</strong></span>
                <span>{{ $detail->created_at ? $detail->created_at->format('d/m/y H:i') : '-' }}</span>
            </p>
            <p style="margin: 2px 0; display: flex; justify-content: space-between;">
                <span><strong>Kasir:</strong></span>
                <span>{{ $detail->user->name ?? '-' }}</span>
            </p>
            <p style="margin: 2px 0; display: flex; justify-content: space-between;">
                <span><strong>Pelanggan:</strong></span>
                <span>{{ $detail->customer_name ?: 'Umum' }}</span>
            </p>
            @if ($detail->table_number)
                <p style="margin: 2px 0; display: flex; justify-content: space-between;">
                    <span><strong>Meja:</strong></span>
                    <span>{{ $detail->table_number }}</span>
                </p>
            @endif
        </div>

        <hr style="border: 0; border-top: 1px dashed #000; margin: 5px 0;">

        <div class="item-details">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 2px 0;">Item</th>
                        <th style="text-align: right; padding: 2px 0;">Qty</th>
                        <th style="text-align: right; padding: 2px 0;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detail->transactionDetail as $item)
                        {{-- Assuming 'transactionDetail' is the correct plural relation name --}}
                        <tr>
                            <td colspan="3" style="padding: 1px 0; text-align: left;">
                                {{ $item->product->name ?? 'Produk Tidak Ditemukan' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0 2px 5px; text-align: left; font-size: 10px;">
                                @if ($item->product && $item->product->selling_price !== null)
                                    @ {{ number_format($item->product->selling_price, 0, ',', '.') }}
                                @else
                                    @ -
                                @endif
                            </td>
                            <td style="padding: 1px 0 2px 0; text-align: right;">
                                {{ $item->quantity }}
                            </td>
                            <td style="padding: 1px 0 2px 0; text-align: right;">
                                {{ number_format($item->quantity * ($item->product->selling_price ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr style="border: 0; border-top: 1px dashed #000; margin: 5px 0;">

        <div class="summary" style="font-size: 12px;">
            <p style="margin: 3px 0; display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span style="text-align: right;">Rp {{ number_format($detail->total_amount, 0, ',', '.') }}</span>
            </p>
            <p style="margin: 3px 0; display: flex; justify-content: space-between; font-weight: bold; font-size: 14px;"
                class="total-line">
                <span>TOTAL:</span>
                <span style="text-align: right;">Rp {{ number_format($detail->total_amount, 0, ',', '.') }}</span>
            </p>
            <p style="margin: 3px 0; display: flex; justify-content: space-between;">
                <span>Metode Bayar:</span>
                <span
                    style="text-align: right;">{{ Str::title(str_replace('_', ' ', $detail->payment_method)) }}</span>
            </p>
            <p style="margin: 3px 0; display: flex; justify-content: space-between;">
                <span>Status Bayar:</span>
                <span style="text-align: right; font-weight:bold;">{{ ucfirst($detail->payment_status) }}</span>
            </p>
        </div>

        <hr style="border: 0; border-top: 1px dashed #000; margin: 10px 0;">

        <p style="text-align:center; font-size: 11px; margin: 5px 0;">Terima kasih atas kunjungan Anda!</p>
        <p style="text-align:center; font-size: 11px; margin: 5px 0;">CafeHub Cafe</p>
    </div>
</div>
