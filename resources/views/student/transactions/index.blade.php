@extends('layouts.student')
@section('title', 'My Fees & Transactions')
@section('nav_fees', 'active')

@push('styles')
<style>
.summary-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.summary-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 20px; text-align: center; }
.sc-val { font-size: 28px; font-weight: 900; margin-bottom: 4px; }
.sc-label { font-size: 13px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.table-container { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
.tx-table { width: 100%; border-collapse: collapse; text-align: left; }
.tx-table th { background: rgba(255,255,255,0.03); padding: 14px 20px; font-size: 12px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
.tx-table td { padding: 16px 20px; font-size: 14px; font-weight: 700; border-bottom: 1px solid var(--border); }
.tx-table tr:last-child td { border-bottom: none; }
.tx-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.tag { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
.tag-credit { background: rgba(0,212,170,0.15); color: #00D4AA; }
.tag-debit { background: rgba(255,101,132,0.15); color: #FF6584; }
.tx-amount { font-size: 16px; font-weight: 900; }
.tx-amount.credit { color: #00D4AA; }
.tx-amount.debit { color: #FF6584; }
.empty-state { text-align: center; padding: 48px 24px; color: var(--muted); }
.empty-icon { font-size: 48px; margin-bottom: 16px; }

@media (max-width: 600px) {
    .tx-table thead { display: none; }
    .tx-table, .tx-table tbody, .tx-table tr, .tx-table td { display: block; width: 100%; }
    .tx-table tr { margin-bottom: 12px; border: 1px solid var(--border); border-radius: 12px; padding: 10px; background: rgba(255,255,255,0.02); }
    .tx-table td { padding: 8px 12px; border: none; text-align: right; position: relative; }
    .tx-table td::before { content: attr(data-label); position: absolute; left: 12px; font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; }
}
</style>
@endpush

@section('content')

<div class="summary-row">
    <div class="summary-card">
        <div class="sc-val" style="color: #FF6584">₹{{ number_format($totalDue, 2) }}</div>
        <div class="sc-label">Total Fees Due</div>
    </div>
    <div class="summary-card">
        <div class="sc-val" style="color: #00D4AA">₹{{ number_format($totalPaid, 2) }}</div>
        <div class="sc-label">Total Paid</div>
    </div>
    <div class="summary-card">
        <div class="sc-val" style="color: {{ $balance > 0 ? '#FFD700' : '#00D4AA' }}">₹{{ number_format(max(0, $balance), 2) }}</div>
        <div class="sc-label">Outstanding Balance</div>
    </div>
</div>

<div class="table-container">
    @if($transactions->count())
    <table class="tx-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Remarks / Duration</th>
                <th>Method</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            @php
                // Check if this fee has already been paid
                $isPaid = false;
                if($tx->transaction_type === 'debit') {
                    $isPaid = $transactions->contains(function($t) use ($tx) {
                        return $t->transaction_type === 'credit' && $t->remarks === 'Payment for Fee ID: ' . $tx->id;
                    });
                }
            @endphp
            <tr>
                <td data-label="Date" style="color: var(--muted)">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M Y') }}</td>
                <td data-label="Type">
                    <span class="tag {{ $tx->transaction_type === 'credit' ? 'tag-credit' : 'tag-debit' }}">
                        {{ $tx->transaction_type === 'credit' ? 'Payment' : 'Fee Due' }}
                    </span>
                    @if($tx->transaction_type === 'debit' && !$isPaid)
                        <span class="tag" style="background: rgba(255,213,0,0.15); color: #FFD700; margin-left: 6px;">Unpaid</span>
                    @elseif($tx->transaction_type === 'debit' && $isPaid)
                        <span class="tag tag-credit" style="margin-left: 6px;">Paid</span>
                    @endif
                </td>
                <td data-label="Remarks">
                    <div>{{ $tx->remarks ?? 'N/A' }}</div>
                    @if($tx->transaction_duration)
                    <div style="font-size: 11px; font-weight: 800; color: #6C63FF; margin-top: 4px; display: inline-block; background: rgba(108,99,255,0.1); padding: 2px 6px; border-radius: 4px;">{{ ucfirst($tx->transaction_duration) }} Fee</div>
                    @endif
                </td>
                <td data-label="Method">{{ $tx->transaction_method ?? '-' }}</td>
                <td data-label="Amount" class="tx-amount {{ $tx->transaction_type }}">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <span>{{ $tx->transaction_type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->transaction_amount, 2) }}</span>
                        @if($tx->transaction_type === 'debit' && !$isPaid)
                            <button class="btn btn-sm pay-btn" onclick="payFee({{ $tx->id }})" style="background: linear-gradient(135deg, #FFD700, #FFA500); color: #1E1E35; border: none; padding: 6px 14px; font-size: 12px; border-radius: 999px; font-weight: 900; cursor: pointer;">Pay Now</button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <div class="empty-icon">🧾</div>
        <div style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">No Transactions Found</div>
        <div style="font-size: 14px;">Your fee structure and payment history will appear here.</div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function payFee(transactionId) {
    try {
        // 1. Create Order
        const res = await fetch("{{ route('student.fees.pay') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ transaction_id: transactionId })
        });
        
        const order = await res.json();
        
        if (order.error) {
            alert(order.error);
            return;
        }

        // 2. Open Razorpay Checkout
        const options = {
            key: order.key,
            amount: order.amount,
            currency: order.currency,
            name: "School Bag",
            description: "Fee Payment",
            order_id: order.order_id,
            handler: async function (response) {
                // 3. Verify Payment
                const verifyRes = await fetch("{{ route('student.fees.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                        transaction_id: order.transaction_id
                    })
                });
                
                const verifyData = await verifyRes.json();
                if (verifyData.success) {
                    window.location.reload();
                } else {
                    alert('Payment verification failed. Please contact admin.');
                }
            },
            theme: { color: "#6C63FF" }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
        
    } catch (err) {
        console.error(err);
        alert('Something went wrong initiating the payment.');
    }
}
</script>
@endpush
