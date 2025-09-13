@extends('layouts.app')

@section('title', 'My Purchases')

@section('content')

<div class="container">
    <h1>My Purchase History</h1>

    @if(!auth()->check())
        <div class="alert alert-warning">
            Please <a href="{{ route('login') }}">login</a> to view your purchase history.
        </div>
    @else
        <p>Hello, {{ auth()->user()->name }}! Here are your completed purchases:</p>
        @if(empty($purchases))
            <div class="alert alert-info">
                No completed purchases found! This could be because:
                <ul>
                    <li>You haven't made any purchases yet</li>
                    <li>Your purchases are still processing</li>
                    <li>Store is not configured in admin settings</li>
                    <li>There's an issue connecting to the Store</li>
                </ul>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Completed Purchases ({{ count($purchases) }} total)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $payment)
                                <tr>
                                    <td class="fw-bold">${{ number_format($payment['price'] ?? 0, 2) }} {{ $payment['currency'] ?? 'USD' }}</td>
                                    <td>
                                        @if(isset($payment['time']))
                                            {{ \Carbon\Carbon::createFromTimestamp($payment['time'])->format('M j, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        <span class="txn-id" style="cursor: pointer; user-select: none;" data-shown="false">
                                            [Click to show]
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-details"
                                                data-transaction-id="{{ $payment['txn_id'] }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paymentModal">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Single Modal for all payments (will be populated by JavaScript) -->
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loading Purchase Details...</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="paymentModalBody">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading payment details...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const modalTitle = document.querySelector('#paymentModal .modal-title');
    const modalBody = document.getElementById('paymentModalBody');

    // Lazy-load payment details when clicking "View Details"
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            loadPaymentDetails(transactionId);
        });
    });

    function loadPaymentDetails(transactionId) {
        modalTitle.textContent = 'Loading Purchase Details...';
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading payment details...</p>
            </div>
        `;

        fetch(`/api/mypurchases/payment/${transactionId}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch payment details');
                return response.json();
            })
            .then(data => {
                modalTitle.textContent = 'Purchase Details';
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Transaction Information</h6>
                            <table class="table table-sm">
                                <tr><th>Transaction ID:</th><td>${data.id || transactionId}</td></tr>
                                <tr><th>Amount:</th><td class="fw-bold">${data.currency?.symbol || '$'}${parseFloat(data.amount || 0).toFixed(2)} ${data.currency?.iso_4217 || 'USD'}</td></tr>
                                <tr><th>Date:</th><td>${new Date(data.date).toLocaleString()}</td></tr>
                                <tr><th>Status:</th><td><span class="badge bg-success">${data.status}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Packages Purchased</h6>
                            ${renderPackages(data.packages)}
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalTitle.textContent = 'Error';
                modalBody.innerHTML = `<div class="alert alert-danger"><p>Failed to load payment details:</p><p class="mb-0">${error.message}</p></div>`;
            });
    }

    function renderPackages(packages) {
        if (!packages || packages.length === 0) {
            return '<p class="text-muted">No package details available</p>';
        }
        return `<div class="list-group">
            ${packages.map(pkg => `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">${pkg.name || 'Unknown Package'}</span>
                    <span class="text-muted small">ID: ${pkg.id || 'N/A'}</span>
                </div>
            `).join('')}
        </div>`;
    }

    // Transaction ID spoiler toggle
    document.querySelectorAll('.txn-id').forEach(span => {
        span.addEventListener('click', function() {
            const row = this.closest('tr');
            const txnId = row.querySelector('.view-details').getAttribute('data-transaction-id');

            if (this.dataset.shown === 'true') {
                this.textContent = '[Click to show]';
                this.dataset.shown = 'false';
            } else {
                this.textContent = txnId;
                this.dataset.shown = 'true';
            }
        });
    });
});
</script>
@endpush
