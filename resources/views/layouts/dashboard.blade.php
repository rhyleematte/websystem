<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'AskDocPH')</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">

  <!-- Base + Dashboard CSS (tokens, panel, nav, etc.) -->
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Page-specific styles -->
  @stack('styles')
</head>
<body>

@yield('content')

<!-- Share modal (used by Dashboard/Profile/Resources) -->
<div class="modal-backdrop share-modal" id="shareModal" aria-hidden="true">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="shareModalTitle">
    <div class="modal-header">
      <h2 id="shareModalTitle">Share</h2>
      <button class="modal-close" id="shareModalCloseBtn" type="button" aria-label="Close">
        <i data-lucide="x"></i>
      </button>
    </div>
    <div class="share-modal-body">
      <label class="share-modal-label" for="shareModalText">Add a comment (optional)</label>
      <textarea id="shareModalText" class="share-modal-textarea" rows="4" placeholder="Say something about this..."></textarea>
      <div class="share-modal-preview" id="shareModalPreview" style="display:none;"></div>
      <div class="share-modal-actions">
        <button type="button" class="btn-cancel" id="shareModalCancelBtn">Cancel</button>
        <button type="button" class="btn-save" id="shareModalShareBtn">
          Share <i data-lucide="send"></i>
        </button>
      </div>
      <div class="form-feedback" id="shareModalFeedback"></div>
    </div>
  </div>
</div>

<!-- Dashboard JS -->
<script src="{{ asset('assets/js/dashboard.js') }}?v={{ filemtime(public_path('assets/js/dashboard.js')) }}" defer></script>
<script src="{{ asset('assets/js/post-ui.js') }}?v={{ filemtime(public_path('assets/js/post-ui.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
