@extends('admin.layouts.admin')

@section('title', 'MyPurchases - Admin Settings')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h3>MyPurchases Settings</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('mypurchases.admin.save') }}">
                @csrf

                <div class="form-group">
                    <label for="is_purchases_page_enabled">Purchases Page</label>
                    <select class="form-control" id="is_purchases_page_enabled" name="is_purchases_page_enabled">
                        <option value="1" {{ $isEnabled == '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ $isEnabled == '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                    <small class="form-text text-muted">Enable or disable the public purchases page.</small>
                </div>

                <div class="form-group mt-3">
                    <label for="tebex_secret">Tebex Secret Key</label>
                    <input type="password" class="form-control" id="tebex_secret" name="tebex_secret"
                           value="{{ $tebexSecret }}" placeholder="Enter your Tebex secret key">
                    <small class="form-text text-muted">Get this from your Tebex control panel.</small>
                </div>

                <div class="form-group mt-3">
                    <label for="tebex_store_url">Tebex Store URL</label>
                    <input type="text" class="form-control" id="tebex_store_url" name="tebex_store_url"
                           value="{{ $tebexStoreUrl }}" placeholder="https://yourstore.tebex.io">
                    <small class="form-text text-muted">Your Tebex store URL.</small>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
            </form>
        </div>
    </div>
@endsection
