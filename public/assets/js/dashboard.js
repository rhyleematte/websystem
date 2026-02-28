// public/assets/js/dashboard.js
/* ================================================================
   DASHBOARD JS – handles:
   • Theme toggle + profile dropdown
   • Composer  (text, media, mood, hashtags)  → POST /profile/posts
   • Live feed loading  → GET  /api/dashboard/feed
   • Like / unlike  → POST /profile/posts/{id}/like
   • Comments / replies  → POST /profile/posts/{id}/comments
   • Delete comment  → DELETE /profile/comments/{id}
================================================================ */

document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) lucide.createIcons();

  /* ── CSRF ────────────────────────────────────────────────── */
  var CSRF = '';
  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
  if (csrfMeta) CSRF = csrfMeta.content;

  /* ── Helpers ─────────────────────────────────────────────── */
  function apiPost(url, body, method) {
    method = method || 'POST';
    var isFormData = body instanceof FormData;
    return fetch(url, {
      method: method,
      headers: isFormData
        ? { 'X-CSRF-TOKEN': CSRF }
        : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: isFormData ? body : JSON.stringify(body),
    }).then(function (r) { return r.json(); });
  }

  function apiGet(url) {
    return fetch(url, {
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    }).then(function (r) { return r.json(); });
  }

  function showToast(msg, type) {
    var el = document.getElementById('dash-toast');
    if (!el) return;
    el.textContent = msg;
    el.className = 'dash-toast' + (type === 'error' ? ' error' : '') + ' show';
    clearTimeout(el._t);
    el._t = setTimeout(function () { el.classList.remove('show'); }, 3200);
  }

  function esc(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  /* ── Theme toggle ───────────────────────────────────────── */
  var root = document.documentElement;
  var savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') root.classList.add('theme-dark');

  var themeBtn = document.getElementById('themeToggleBtn');

  function updateThemeBtn() {
    if (!themeBtn) return;
    var isDark = root.classList.contains('theme-dark');
    var label = themeBtn.querySelector('span');
    var icon = themeBtn.querySelector('i');
    if (label) label.textContent = isDark ? 'Light mode' : 'Dark mode';
    if (icon) icon.setAttribute('data-lucide', isDark ? 'sun' : 'moon');
    if (window.lucide) lucide.createIcons();
  }
  updateThemeBtn();

  if (themeBtn) {
    themeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      root.classList.toggle('theme-dark');
      localStorage.setItem('theme', root.classList.contains('theme-dark') ? 'dark' : 'light');
      updateThemeBtn();
    });
  }

  /* ── Profile dropdown ───────────────────────────────────── */
  var toggle = document.getElementById('profileToggle');
  var menu = document.getElementById('profileDropdown');

  if (toggle && menu) {
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = menu.classList.contains('open');
      menu.classList.toggle('open', !isOpen);
      toggle.setAttribute('aria-expanded', String(!isOpen));
      if (!isOpen && window.lucide) lucide.createIcons();
    });
    menu.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () {
      menu.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { menu.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); }
    });
  }

  /* ── Header Search ──────────────────────────────────────── */
  var searchInput = document.querySelector('.dash-search input');
  var searchWrap = document.querySelector('.dash-search');
  var searchDropdown = null;
  var searchTimer = null;

  if (searchInput && searchWrap) {
    searchDropdown = document.createElement('div');
    searchDropdown.className = 'search-dropdown';
    searchWrap.appendChild(searchDropdown);

    searchInput.addEventListener('input', function (e) {
      var query = e.target.value.trim();
      clearTimeout(searchTimer);

      if (!query) {
        searchDropdown.classList.remove('open');
        return;
      }

      searchTimer = setTimeout(function () {
        apiGet('/api/search/users?q=' + encodeURIComponent(query))
          .then(function (res) {
            if (res.ok && res.users) {
              searchDropdown.innerHTML = '';
              if (res.users.length === 0) {
                searchDropdown.innerHTML = '<div class="search-empty">No users found.</div>';
              } else {
                res.users.forEach(function (u) {
                  var item = document.createElement('a');
                  item.href = u.profile_url;
                  item.className = 'search-item';
                  item.innerHTML =
                    '<img src="' + esc(u.avatar_url) + '" class="avatar" alt="User">' +
                    '<div class="search-item-info">' +
                    '<div class="search-item-name">' + esc(u.name) + '</div>' +
                    '<div class="search-item-username">@' + esc(u.username) + '</div>' +
                    '</div>';
                  searchDropdown.appendChild(item);
                });
              }
              searchDropdown.classList.add('open');
            }
          }).catch(function () {
            // fail silently
          });
      }, 300);
    });

    document.addEventListener('click', function (e) {
      if (!searchWrap.contains(e.target)) {
        searchDropdown.classList.remove('open');
      }
    });

    searchInput.addEventListener('focus', function () {
      if (searchInput.value.trim() && searchDropdown.children.length > 0) {
        searchDropdown.classList.add('open');
      }
    });
  }

  /* ================================================================
     COMPOSER
  ================================================================ */
  var postText = document.getElementById('dashPostText');
  var mediaUpload = document.getElementById('mediaUpload');
  var previewArea = document.getElementById('mediaPreviewArea');
  var hashtagRow = document.getElementById('hashtagRow');
  var hashtagInput = document.getElementById('hashtagInput');
  var hashtagToggleBtn = document.getElementById('hashtagToggleBtn');
  var moodBar = document.getElementById('moodBar');
  var moodToggleBtn = document.getElementById('moodToggleBtn');
  var selectedMoodDisplay = document.getElementById('selectedMoodDisplay');
  var shareBtn = document.getElementById('dashShareBtn');
  var composerFeedback = document.getElementById('composerFeedback');
  var feed = document.getElementById('dashFeed');
  var feedLoading = document.getElementById('feedLoading');
  var feedEmpty = document.getElementById('feedEmpty');

  var selectedFiles = [];
  var selectedMood = '';

  /* ── Mood toggle ────────────────────────────────────────── */
  if (moodToggleBtn && moodBar) {
    moodToggleBtn.addEventListener('click', function () {
      var open = moodBar.style.display !== 'none';
      moodBar.style.display = open ? 'none' : 'flex';
    });
  }

  /* ── Mood pick ──────────────────────────────────────────── */
  if (moodBar) {
    moodBar.addEventListener('click', function (e) {
      var btn = e.target.closest('.mood-btn');
      if (!btn) return;
      selectedMood = btn.dataset.mood;
      moodBar.querySelectorAll('.mood-btn').forEach(function (b) {
        b.classList.toggle('active', b === btn);
      });
      if (selectedMoodDisplay) {
        selectedMoodDisplay.textContent = 'Feeling: ' + selectedMood;
        selectedMoodDisplay.style.display = 'inline-block';
      }
    });
  }

  /* ── Hashtag toggle ─────────────────────────────────────── */
  if (hashtagToggleBtn && hashtagRow) {
    hashtagToggleBtn.addEventListener('click', function () {
      var open = hashtagRow.style.display !== 'none';
      hashtagRow.style.display = open ? 'none' : 'flex';
      if (!open && hashtagInput) hashtagInput.focus();
    });
  }

  /* ── Media pick ─────────────────────────────────────────── */
  if (mediaUpload) {
    mediaUpload.addEventListener('change', function () {
      var newFiles = Array.from(mediaUpload.files);
      selectedFiles = selectedFiles.concat(newFiles);
      renderPreviews();
      mediaUpload.value = '';
    });
  }

  function renderPreviews() {
    if (!previewArea) return;
    previewArea.innerHTML = '';
    if (selectedFiles.length === 0) {
      previewArea.style.display = 'none';
      return;
    }
    previewArea.style.display = 'grid';
    selectedFiles.forEach(function (file, idx) {
      var wrap = document.createElement('div');
      wrap.className = 'preview-item';

      var url = URL.createObjectURL(file);
      if (file.type.startsWith('video')) {
        var vid = document.createElement('video');
        vid.src = url;
        vid.controls = true;
        wrap.appendChild(vid);
      } else {
        var img = document.createElement('img');
        img.src = url;
        wrap.appendChild(img);
      }

      var rem = document.createElement('button');
      rem.className = 'preview-remove';
      rem.innerHTML = '&times;';
      rem.type = 'button';
      rem.addEventListener('click', function () {
        selectedFiles.splice(idx, 1);
        renderPreviews();
      });
      wrap.appendChild(rem);
      previewArea.appendChild(wrap);
    });
    if (window.lucide) lucide.createIcons();
  }

  /* ── Share / submit post ───────────────────────────────── */
  if (shareBtn) {
    shareBtn.addEventListener('click', async function () {
      var text = postText ? postText.value.trim() : '';
      if (!text && selectedFiles.length === 0) {
        if (composerFeedback) composerFeedback.textContent = 'Write something or attach a file.';
        return;
      }
      if (composerFeedback) composerFeedback.textContent = '';
      shareBtn.disabled = true;
      shareBtn.textContent = 'Sharing…';

      var fd = new FormData();
      if (text) fd.append('text_content', text);
      if (selectedMood) fd.append('mood', selectedMood);
      if (hashtagInput && hashtagInput.value.trim()) fd.append('hashtags', hashtagInput.value.trim());
      selectedFiles.forEach(function (f) { fd.append('media[]', f); });

      try {
        var res = await apiPost(window.DASH_ROUTES.storePost, fd);
        if (res.ok) {
          if (postText) postText.value = '';
          if (hashtagInput) hashtagInput.value = '';
          if (hashtagRow) hashtagRow.style.display = 'none';
          if (moodBar) moodBar.style.display = 'none';
          if (selectedMoodDisplay) selectedMoodDisplay.style.display = 'none';
          selectedMood = '';
          moodBar && moodBar.querySelectorAll('.mood-btn').forEach(function (b) { b.classList.remove('active'); });
          selectedFiles = [];
          renderPreviews();

          var postEl = buildPostEl(res.post);
          var firstChild = feed.firstChild;
          if (firstChild && firstChild.id === 'feedLoading') {
            feed.insertBefore(postEl, firstChild);
          } else {
            feed.insertBefore(postEl, feed.firstChild);
          }
          if (feedEmpty) feedEmpty.style.display = 'none';
          if (window.lucide) lucide.createIcons();
          showToast('Post shared! 🎉');
        } else {
          var msg = res.errors
            ? Object.values(res.errors).flat().join(' ')
            : (res.message || 'Error sharing post.');
          if (composerFeedback) composerFeedback.textContent = msg;
          showToast(msg, 'error');
        }
      } catch (err) {
        showToast('Network error. Please try again.', 'error');
      }

      shareBtn.disabled = false;
      shareBtn.innerHTML = 'Share <i data-lucide="send"></i>';
      if (window.lucide) lucide.createIcons();
    });
  }

  /* ================================================================
     LOAD FEED
  ================================================================ */
  function loadFeed() {
    apiGet(window.DASH_ROUTES.feed).then(function (res) {
      if (feedLoading) feedLoading.remove();
      if (!res.ok) { showToast('Failed to load feed.', 'error'); return; }

      if (!res.posts || res.posts.length === 0) {
        if (feedEmpty) feedEmpty.style.display = 'flex';
        return;
      }

      res.posts.forEach(function (post) {
        feed.appendChild(buildPostEl(post));
      });
      if (window.lucide) lucide.createIcons();
    }).catch(function () {
      if (feedLoading) feedLoading.innerHTML = '<i data-lucide="alert-circle"></i> <span>Could not load posts.</span>';
      if (window.lucide) lucide.createIcons();
    });
  }
  loadFeed();

  /* ================================================================
     BUILD POST ELEMENT
  ================================================================ */
  function buildPostEl(post) {
    var article = document.createElement('article');
    article.className = 'panel post';
    article.dataset.postId = post.id;

    /* media */
    var mediaHtml = '';
    if (post.media && post.media.length) {
      var cnt = Math.min(post.media.length, 4);
      mediaHtml = '<div class="post-media-grid media-count-' + cnt + '">';
      post.media.slice(0, 4).forEach(function (m) {
        if (m.media_type === 'video') {
          mediaHtml += '<video src="' + esc(m.url) + '" controls class="post-media-item"></video>';
        } else {
          mediaHtml += '<img src="' + esc(m.url) + '" alt="Post image" class="post-media-item">';
        }
      });
      if (post.media.length > 4) {
        mediaHtml += '<div class="media-more">+' + (post.media.length - 4) + '</div>';
      }
      mediaHtml += '</div>';
    }

    /* mood */
    var moodHtml = post.mood
      ? '<div class="post-mood"><i data-lucide="heart-handshake"></i> Feeling: <strong>' + esc(post.mood) + '</strong></div>'
      : '';

    /* hashtags */
    var tagsHtml = '';
    if (post.hashtags && post.hashtags.length) {
      tagsHtml = '<div class="post-tags">';
      post.hashtags.forEach(function (t) {
        tagsHtml += '<span class="tag">#' + esc(t) + '</span>';
      });
      tagsHtml += '</div>';
    }

    /* manage menu */
    var menuHtml = '';
    if (post.can_manage) {
      menuHtml = '<div class="post-menu-wrap">'
        + '<button class="icon-btn post-menu-btn" type="button"><i data-lucide="more-horizontal"></i></button>'
        + '<div class="post-menu hidden">'
        + '<button class="post-menu-item delete-post-btn danger" type="button" data-post-id="' + post.id + '">'
        + '<i data-lucide="trash-2"></i> Delete</button>'
        + '</div></div>';
    }

    /* verified badge */
    var verifiedBadge = (post.user && post.user.role === 'doctor')
      ? '<span class="verified" title="Verified Doctor"><i data-lucide="badge-check"></i></span>'
      : '';

    var profileUrl = (post.user.id === window.MY_ID)
      ? window.MY_PROFILE_URL
      : esc(post.user.profile_url || '/profile/' + post.user.id);

    article.innerHTML =
      '<div class="post-head">'
      + '<a href="' + profileUrl + '" class="post-author-link">'
      + '<div class="avatar md"><img src="' + esc(post.user.avatar_url) + '" alt="' + esc(post.user.name) + '"></div>'
      + '</a>'
      + '<div class="post-meta">'
      + '<div class="post-name">'
      + '<a href="' + profileUrl + '" class="post-author-link">' + esc(post.user.name) + '</a>'
      + verifiedBadge
      + '</div>'
      + '<div class="post-sub">@' + esc(post.user.username) + ' · ' + esc(post.created_at) + '</div>'
      + '</div>'
      + menuHtml
      + '</div>'
      + (post.text_content ? '<div class="post-body">' + esc(post.text_content) + '</div>' : '')
      + moodHtml
      + mediaHtml
      + tagsHtml
      + '<div class="post-actions">'
      + '<button class="post-btn dash-like-btn ' + (post.is_liked ? 'liked' : '') + '" type="button"'
      + ' data-post-id="' + post.id + '" data-liked="' + (post.is_liked ? '1' : '0') + '">'
      + '<i data-lucide="heart" class="like-icon"></i>'
      + '<span class="like-count">' + post.like_count + '</span>'
      + '</button>'
      + '<button class="post-btn dash-comment-toggle" type="button" data-post-id="' + post.id + '">'
      + '<i data-lucide="message-square"></i>'
      + '<span class="comment-count">' + post.comment_count + '</span>'
      + '</button>'
      + '<button class="post-btn" type="button"><i data-lucide="share-2"></i></button>'
      + '<button class="post-btn end" type="button" title="Save"><i data-lucide="bookmark"></i></button>'
      + '</div>'
      + '<div class="comments-section hidden" id="dash-comments-' + post.id + '">'
      + '<div class="comment-composer">'
      + '<div class="avatar sm"><img src="' + esc(window.MY_AVATAR) + '" alt="You"></div>'
      + '<div class="comment-input-wrap">'
      + '<input type="text" class="comment-input" placeholder="Write a comment…" data-post-id="' + post.id + '">'
      + '<button class="comment-send-btn" type="button" data-post-id="' + post.id + '"><i data-lucide="send"></i></button>'
      + '</div></div>'
      + '<div class="comments-list" id="dash-comments-list-' + post.id + '">'
      + (post.comments || []).map(function (c) { return buildCommentHtml(c); }).join('')
      + '</div>'
      + '</div>';

    return article;
  }

  function buildCommentHtml(c, isReply = false, parentId = null) {
    var cUrl = (c.user.id === window.MY_ID)
      ? window.MY_PROFILE_URL
      : esc(c.user.profile_url || '/profile/' + c.user.id);

    if (isReply) {
      return '<div class="comment-item reply-item" id="dash-comment-' + c.id + '">'
        + '<a href="' + cUrl + '" class="comment-avatar-link"><div class="avatar sm"><img src="' + esc(c.user.avatar_url) + '" alt="' + esc(c.user.name) + '"></div></a>'
        + '<div class="comment-bubble">'
        + '<div class="comment-meta">'
        + '<a href="' + cUrl + '" class="comment-author-link"><span class="comment-author">' + esc(c.user.name) + '</span></a>'
        + '<span class="comment-time">' + esc(c.created_at) + '</span>'
        + (c.can_delete
          ? '<button class="comment-delete-btn" type="button" data-comment-id="' + c.id + '" title="Delete"><i data-lucide="x"></i></button>'
          : '')
        + '</div>'
        + '<p class="comment-text">' + esc(c.comment_text) + '</p>'
        + '<button class="reply-toggle-btn" type="button" data-comment-id="' + parentId + '" data-post-id="' + (c.post_id || '') + '" data-reply-to="' + esc(c.user.username) + '">Reply</button>'
        + '</div></div>';
    }

    var repliesHtml = (c.replies || []).map(function (r) {
      return buildCommentHtml(r, true, c.id);
    }).join('');

    return '<div class="comment-item" id="dash-comment-' + c.id + '">'
      + '<a href="' + cUrl + '" class="comment-avatar-link"><div class="avatar sm"><img src="' + esc(c.user.avatar_url) + '" alt="' + esc(c.user.name) + '"></div></a>'
      + '<div class="comment-bubble">'
      + '<div class="comment-meta">'
      + '<a href="' + cUrl + '" class="comment-author-link"><span class="comment-author">' + esc(c.user.name) + '</span></a>'
      + '<span class="comment-time">' + esc(c.created_at) + '</span>'
      + (c.can_delete
        ? '<button class="comment-delete-btn" type="button" data-comment-id="' + c.id + '" title="Delete"><i data-lucide="x"></i></button>'
        : '')
      + '</div>'
      + '<p class="comment-text">' + esc(c.comment_text) + '</p>'
      + '<button class="reply-toggle-btn" type="button" data-comment-id="' + c.id + '" data-post-id="' + c.post_id + '">Reply</button>'
      + '<div class="reply-composer hidden" id="dash-reply-composer-' + c.id + '">'
      + '<input type="text" class="comment-input reply-input" placeholder="Write a reply…"'
      + ' data-post-id="' + c.post_id + '" data-parent-id="' + c.id + '">'
      + '<button class="comment-send-btn reply-send-btn" type="button"'
      + ' data-post-id="' + c.post_id + '" data-parent-id="' + c.id + '"><i data-lucide="send"></i></button>'
      + '</div>'
      + '<div class="replies-list" id="dash-replies-' + c.id + '">' + repliesHtml + '</div>'
      + '</div></div>';
  }

  /* ================================================================
     INTERACTIONS (delegated)
  ================================================================ */
  document.addEventListener('click', async function (e) {

    /* ── Like ───────────────────────────────────────────────── */
    var likeBtn = e.target.closest('.dash-like-btn');
    if (likeBtn) {
      var postId = likeBtn.dataset.postId;
      var isLiked = likeBtn.dataset.liked === '1';
      var countEl = likeBtn.querySelector('.like-count');

      // Optimistic update
      var newLiked = !isLiked;
      likeBtn.dataset.liked = newLiked ? '1' : '0';
      likeBtn.classList.toggle('liked', newLiked);
      if (countEl) countEl.textContent = parseInt(countEl.textContent || '0') + (newLiked ? 1 : -1);

      // Heart pop animation
      likeBtn.classList.remove('pop');
      void likeBtn.offsetWidth;
      likeBtn.classList.add('pop');
      setTimeout(function () { likeBtn.classList.remove('pop'); }, 400);

      try {
        var res = await apiPost(window.DASH_ROUTES.toggleLike(postId), {});
        if (res.ok) {
          likeBtn.dataset.liked = res.liked ? '1' : '0';
          likeBtn.classList.toggle('liked', res.liked);
          if (countEl) countEl.textContent = res.like_count;
          if (window.lucide) lucide.createIcons();
        }
      } catch (err) { /* keep optimistic */ }
      return;
    }

    /* ── Toggle comments section ────────────────────────────── */
    var commentToggle = e.target.closest('.dash-comment-toggle');
    if (commentToggle) {
      var postId = commentToggle.dataset.postId;
      var section = document.getElementById('dash-comments-' + postId);
      if (section) {
        section.classList.toggle('hidden');
        if (!section.classList.contains('hidden')) {
          section.querySelector('.comment-input') && section.querySelector('.comment-input').focus();
        }
      }
      if (window.lucide) lucide.createIcons();
      return;
    }

    /* ── 3-dot menu toggle ──────────────────────────────────── */
    var menuBtn = e.target.closest('.post-menu-btn');
    if (menuBtn) {
      e.stopPropagation();
      var postMenu = menuBtn.nextElementSibling;
      document.querySelectorAll('.post-menu').forEach(function (m) {
        if (m !== postMenu) m.classList.add('hidden');
      });
      postMenu && postMenu.classList.toggle('hidden');
      if (window.lucide) lucide.createIcons();
      return;
    }
    if (!e.target.closest('.post-menu-wrap')) {
      document.querySelectorAll('.post-menu').forEach(function (m) { m.classList.add('hidden'); });
    }

    /* ── Delete post ────────────────────────────────────────── */
    var deletePostBtn = e.target.closest('.delete-post-btn');
    if (deletePostBtn) {
      if (!confirm('Delete this post? This cannot be undone.')) return;
      var postId = deletePostBtn.dataset.postId;
      var article = document.querySelector('[data-post-id="' + postId + '"]');
      try {
        var res = await apiPost(window.DASH_ROUTES.destroyPost(postId), {}, 'DELETE');
        if (res.ok) {
          article && article.remove();
          showToast('Post deleted.');
          if (document.querySelectorAll('#dashFeed .post').length === 0 && feedEmpty) {
            feedEmpty.style.display = 'flex';
          }
        } else {
          showToast(res.message || 'Error.', 'error');
        }
      } catch (err) {
        showToast('Network error.', 'error');
      }
      return;
    }

    /* ── Comment send ───────────────────────────────────────── */
    var sendBtn = e.target.closest('.comment-send-btn:not(.reply-send-btn)');
    if (sendBtn) {
      var postId = sendBtn.dataset.postId;
      var wrap = sendBtn.closest('.comment-input-wrap');
      var input = wrap ? wrap.querySelector('.comment-input') : null;
      if (input) await submitDashComment(postId, null, input);
      return;
    }

    /* ── Reply send ─────────────────────────────────────────── */
    var replySend = e.target.closest('.reply-send-btn');
    if (replySend) {
      var postId = replySend.dataset.postId;
      var parentId = replySend.dataset.parentId;
      var input = document.querySelector('.reply-input[data-parent-id="' + parentId + '"]');
      if (input) await submitDashComment(postId, parentId, input);
      return;
    }

    /* ── Reply toggle ───────────────────────────────────────── */
    var replyToggle = e.target.closest('.reply-toggle-btn');
    if (replyToggle) {
      var commentId = replyToggle.dataset.commentId;
      var replyTo = replyToggle.dataset.replyTo;
      var composer = document.getElementById('dash-reply-composer-' + commentId);
      if (composer) {
        composer.classList.remove('hidden');
        var inp = composer.querySelector('input');
        if (inp) {
          inp.focus();
          if (replyTo && replyTo !== 'undefined' && replyTo !== '') {
            var tag = '@' + replyTo + ' ';
            var currentVal = inp.value;
            if (!currentVal.startsWith(tag)) {
              if (/^@[\w.\-]+ /.test(currentVal)) {
                inp.value = currentVal.replace(/^@[\w.\-]+ /, tag);
              } else {
                inp.value = tag + currentVal;
              }
            }
          }
        }
      }
      return;
    }

    /* ── Delete comment ─────────────────────────────────────── */
    var delComment = e.target.closest('.comment-delete-btn');
    if (delComment) {
      if (!confirm('Delete this comment?')) return;
      var commentId = delComment.dataset.commentId;
      try {
        var res = await apiPost(window.DASH_ROUTES.destroyComment(commentId), {}, 'DELETE');
        if (res.ok) {
          var el = document.getElementById('dash-comment-' + commentId);
          if (el) {
            el.style.transition = 'opacity 0.25s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 260);
          }
          showToast('Comment deleted.');
        } else {
          showToast(res.message || 'Error.', 'error');
        }
      } catch (err) {
        showToast('Network error.', 'error');
      }
      return;
    }
  });

  /* ── Enter key on comment inputs ─────────────────────────── */
  document.addEventListener('keydown', async function (e) {
    if (e.key !== 'Enter') return;
    var input = e.target;
    if (input.matches && input.matches('.comment-input:not(.reply-input)')) {
      await submitDashComment(input.dataset.postId, null, input);
    } else if (input.matches && input.matches('.reply-input')) {
      await submitDashComment(input.dataset.postId, input.dataset.parentId, input);
    }
  });

  /* ── Submit comment / reply via API ──────────────────────── */
  async function submitDashComment(postId, parentId, inputEl) {
    var text = inputEl && inputEl.value ? inputEl.value.trim() : '';
    if (!text) return;

    var body = { comment_text: text };
    if (parentId) body.parent_comment_id = parentId;

    try {
      var res = await apiPost(window.DASH_ROUTES.storeComment(postId), body);
      if (res.ok) {
        inputEl.value = '';
        var c = res.comment;
        var html = buildCommentHtml(c, !!parentId, parentId);

        if (parentId) {
          var repliesList = document.getElementById('dash-replies-' + parentId);
          if (repliesList) repliesList.insertAdjacentHTML('beforeend', html);
          var composer = document.getElementById('dash-reply-composer-' + parentId);
          if (composer) composer.classList.add('hidden');
        } else {
          var commentsList = document.getElementById('dash-comments-list-' + postId);
          if (commentsList) commentsList.insertAdjacentHTML('afterbegin', html);
          // Update count
          var article = document.querySelector('[data-post-id="' + postId + '"]');
          var countEl = article && article.querySelector('.comment-count');
          if (countEl) countEl.textContent = parseInt(countEl.textContent || '0') + 1;
        }
        if (window.lucide) lucide.createIcons();
      } else {
        showToast(res.message || 'Error.', 'error');
      }
    } catch (err) {
      showToast('Network error.', 'error');
    }
  }
});