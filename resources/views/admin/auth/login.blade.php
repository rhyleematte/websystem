@extends('layouts.app')

@section('title', 'Admin Login - AskDocPH')

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-header" style="text-align: center; margin-bottom: 2rem;">
      <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Admin Portal</h1>
      <p style="color: var(--text-muted); font-size: 0.875rem;">Sign in to manage the system</p>
    </div>

    @if($errors->any())
      <div class="alert-error" style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: var(--error); padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.875rem;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf
      
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="email" style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus style="width: 100%; padding: 0.75rem 1rem; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-size: 1rem; transition: border-color 0.2s;">
      </div>

      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="password" style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
        <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-size: 1rem; transition: border-color 0.2s;">
      </div>

      <button type="submit" class="btn" style="width: 100%; padding: 0.75rem; border: none; border-radius: 6px; background: var(--primary); color: white; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.2s;">Sign In</button>
    </form>

    <div class="auth-footer" style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: var(--text-muted);">
      No account? <a href="{{ route('admin.signup') }}" style="color: var(--primary); text-decoration: none;">Sign up here</a>
    </div>
  </div>
</div>
@endsection
