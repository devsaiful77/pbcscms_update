@extends('layouts.admin')

@section('content')
<h3 class="text-center mb-5">Encrypt Decrypt Page</h3>

<div class="row">
    {{-- Encrypt --}}
    <div class="col-6">
        <form action="{{ route('encrypt-decrypt.index') }}" method="get">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Encrypt</h4>
                </div>
                <div class="card-body">
                    <label class="form-label">Enter Plain Text</label>
                    <input type="text"
                           class="form-control"
                           name="plainTextInput"
                           value="{{ request('plainTextInput') }}"
                           placeholder="Enter text to encrypt">
                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Decrypt --}}
    <div class="col-6">
        <form action="{{ route('encrypt-decrypt.index') }}" method="get">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Decrypt</h4>
                </div>
                <div class="card-body">
                    <label class="form-label">Enter Encrypted Text</label>
                    <input type="text"
                           class="form-control"
                           name="encryptedTextInput"
                           value="{{ request('encryptedTextInput') }}"
                           placeholder="Enter text to decrypt">
                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Result --}}
@if(!is_null($result))
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Result</h4>
            </div>
            <div class="card-body">
                <p class="fw-bold text-break">{{ $result }}</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
