/* ================================================================
   PROFILE PAGE – JavaScript
   NOTE: dashboard.js handles lucide init, theme + dropdown toggle.
         This file handles profile-specific features only.
================================================================ */

/* ── Helpers ────────────────────────────────────────────────── */
const $ = (sel, ctx) => (ctx || document).querySelector(sel);
const $$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];

let CSRF = '';

function toast(msg, type) {
    type = type || 'success';
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = msg;
    el.className = 'toast ' + type + ' show';
    clearTimeout(el._t);
    el._t = setTimeout(function () { el.classList.remove('show'); }, 3200);
}

function apiPost(url, body, method) {
    method = method || 'POST';
    const isFormData = body instanceof FormData;
    return fetch(url, {
        method: method,
        headers: isFormData
            ? { 'X-CSRF-TOKEN': CSRF }
            : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: isFormData ? body : JSON.stringify(body),
    }).then(function (r) { return r.json(); });
}



/* ================================================================
   PROFILE PHOTO — upload & delete
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    // Init CSRF from meta tag
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) CSRF = csrfMeta.content;

    const photoInput = document.getElementById('photoUpload');
    const previewAvatar = document.getElementById('previewAvatar');
    const deleteBtn = document.getElementById('deletePhotoBtn');

    photoInput?.addEventListener('change', async () => {
        const file = photoInput.files[0];
        if (!file) return;

        // Local preview
        const previewUrl = URL.createObjectURL(file);
        if (previewAvatar) previewAvatar.src = previewUrl;

        const fd = new FormData();
        fd.append('photo', file);

        try {
            const res = await apiPost(window.ROUTES.updatePhoto, fd);
            if (res.ok) {
                // Update all avatar images on the page with the new URL
                $$('img').forEach(img => {
                    if (img.src === previewAvatar?.src || img.dataset.ownAvatar) return;
                });
                toast('Profile photo updated!', 'success');
            } else {
                toast(res.message ?? 'Upload failed.', 'error');
            }
        } catch {
            toast('Upload failed.', 'error');
        }
        photoInput.value = '';
    });

    deleteBtn?.addEventListener('click', async () => {
        try {
            const res = await apiPost(window.ROUTES.deletePhoto, {});
            if (res.ok) {
                const newAvatarUrl = res.avatar_url;
                if (previewAvatar) previewAvatar.src = newAvatarUrl;

                // Update all avatar images on the page for the current user
                $$('img').forEach(img => {
                    // Update header avatars or post avatars that belong to this user
                    if (img.dataset.ownAvatar || img.closest('.avatar-btn') || img.closest('.dropdown-avatar')) {
                        img.src = newAvatarUrl;
                    }
                });

                toast('Profile photo removed.', 'success');
            } else {
                toast(res.message ?? 'Error.', 'error');
            }
        } catch {
            toast('Error connecting to server.', 'error');
        }
    });
});

/* ================================================================
   EDIT PROFILE MODAL
================================================================ */
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editModal');
    var editBtn = document.getElementById('editProfileBtn');
    var closeBtn = document.getElementById('closeEditModal');
    var cancelBtn = document.getElementById('cancelEditBtn');
    var editForm = document.getElementById('editProfileForm');
    var bioArea = document.getElementById('inp_bio');
    var bioCount = document.getElementById('bioCharCount');
    var feedback = document.getElementById('editFeedback');
    var saveBtn = document.getElementById('saveProfileBtn');

    function openModal() { if (editModal) editModal.classList.add('open'); }
    function closeModal() {
        if (editModal) editModal.classList.remove('open');
        clearFeedback();
    }
    function clearFeedback() {
        if (feedback) { feedback.textContent = ''; feedback.className = 'form-feedback'; }
    }

    if (editBtn) editBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) closeModal();
        });
    }

    // Live character counter
    if (bioArea && bioCount) {
        bioArea.addEventListener('input', function () {
            bioCount.textContent = bioArea.value.length;
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (saveBtn) saveBtn.disabled = true;
            clearFeedback();

            var data = {
                fname: (document.getElementById('inp_fname') || {}).value || '',
                mname: (document.getElementById('inp_mname') || {}).value || '',
                lname: (document.getElementById('inp_lname') || {}).value || '',
                username: (document.getElementById('inp_username') || {}).value || '',
                bio: bioArea ? bioArea.value : '',
            };

            // Trim all values
            Object.keys(data).forEach(function (k) { data[k] = data[k].trim(); });

            apiPost(window.ROUTES.updateInfo, data).then(function (res) {
                if (res.ok) {
                    // ── Use DB-confirmed values from server response ────────
                    var savedName = res.full_name || [data.fname, data.mname, data.lname].filter(Boolean).join(' ');
                    var savedUsername = res.username || data.username;
                    var savedBio = res.bio || data.bio;

                    // Update full name everywhere
                    $$('.prof-fullname').forEach(function (el) { el.textContent = savedName; });

                    // Update @handle
                    var handleEl = document.getElementById('profHandle');
                    if (handleEl) handleEl.textContent = '@' + savedUsername;
                    $$('.prof-handle').forEach(function (el) { el.textContent = '@' + savedUsername; });

                    // Update bio display
                    var bioDisplay = document.getElementById('bioDisplay');
                    if (bioDisplay) {
                        if (savedBio) {
                            bioDisplay.textContent = savedBio;
                            bioDisplay.classList.remove('muted');
                        } else {
                            bioDisplay.textContent = window.IS_OWN_PROFILE ? 'Add a short bio\u2026' : 'No bio yet.';
                            bioDisplay.classList.add('muted');
                        }
                    }

                    // Update topbar dropdown
                    var dropUser = document.querySelector('.profile-username');
                    if (dropUser) dropUser.textContent = '@' + savedUsername;
                    var dropName = document.querySelector('.profile-fullname');
                    if (dropName) dropName.textContent = savedName;

                    // Show success
                    if (feedback) {
                        feedback.textContent = res.message || 'Profile updated!';
                        feedback.classList.add('success');
                    }
                    toast(res.message || 'Profile updated!', 'success');
                    setTimeout(closeModal, 1200);
                } else {
                    var errorMsg = 'Error saving profile.';
                    if (res.errors) {
                        var msgs = [];
                        Object.keys(res.errors).forEach(function (k) {
                            res.errors[k].forEach(function (m) { msgs.push(m); });
                        });
                        errorMsg = msgs.join(' ');
                    } else if (res.message) {
                        errorMsg = res.message;
                    }
                    if (feedback) {
                        feedback.textContent = errorMsg;
                        feedback.classList.add('error');
                    }
                    toast(errorMsg, 'error');
                }

                if (saveBtn) saveBtn.disabled = false;
            }).catch(function () {
                if (feedback) {
                    feedback.textContent = 'Network error. Please try again.';
                    feedback.classList.add('error');
                }
                toast('Network error.', 'error');
                if (saveBtn) saveBtn.disabled = false;
            });
        });
    }
});


/* ================================================================
   TABS
================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const tabs = $$('.tab-btn', document.getElementById('profTabs'));
    const panels = $$('.tab-content');

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            const target = btn.dataset.tab;
            panels.forEach(p => {
                p.classList.toggle('hidden', p.id !== `tab-${target}`);
            });

            if (window.lucide) lucide.createIcons();

            // Optionally update URL so reloads keep you there
            const url = new URL(window.location);
            url.searchParams.set('tab', target);
            window.history.replaceState({}, '', url);
        });
    });

    // Auto-select tab from query param if present
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const targetBtn = tabs.find(b => b.dataset.tab === tabParam);
        if (targetBtn) {
            targetBtn.click();
        }
    }
});

/* ================================================================
   CREATE POST
================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const postText = document.getElementById('postText');
    const postMedia = document.getElementById('postMedia');
    const submitBtn = document.getElementById('submitPostBtn');
    const previewArea = document.getElementById('mediaPreviewArea');
    const postFeedback = document.getElementById('postFeedback');
    const postsFeed = document.getElementById('postsFeed');

    if (!submitBtn) return;  // Not own profile

    let selectedFiles = [];

    postMedia?.addEventListener('change', () => {
        const newFiles = [...postMedia.files];
        selectedFiles.push(...newFiles);
        renderPreviews();
        postMedia.value = '';
    });

    function renderPreviews() {
        if (!previewArea) return;
        previewArea.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const wrap = document.createElement('div');
            wrap.className = 'preview-item';

            const url = URL.createObjectURL(file);
            if (file.type.startsWith('video')) {
                const vid = document.createElement('video');
                vid.src = url;
                wrap.appendChild(vid);
            } else {
                const img = document.createElement('img');
                img.src = url;
                wrap.appendChild(img);
            }

            const removeBtn = document.createElement('button');
            removeBtn.className = 'preview-remove';
            removeBtn.innerHTML = '×';
            removeBtn.addEventListener('click', () => {
                selectedFiles.splice(idx, 1);
                renderPreviews();
            });

            wrap.appendChild(removeBtn);
            previewArea.appendChild(wrap);
        });
    }

    submitBtn.addEventListener('click', async () => {
        const text = postText?.value.trim();
        if (!text && selectedFiles.length === 0) {
            if (postFeedback) postFeedback.textContent = 'Write something or attach a file.';
            return;
        }

        submitBtn.disabled = true;
        if (postFeedback) postFeedback.textContent = '';

        const fd = new FormData();
        if (text) fd.append('text_content', text);
        selectedFiles.forEach(f => fd.append('media[]', f));

        try {
            const res = await apiPost(window.ROUTES.storePost, fd);
            if (res.ok) {
                // Clear composer
                if (postText) postText.value = '';
                selectedFiles = [];
                renderPreviews();

                // Prepend post to feed
                const postEl = buildPostEl(res.post);
                const empty = postsFeed?.querySelector('.empty-state');
                if (empty) empty.remove();
                postsFeed?.insertBefore(postEl, postsFeed.firstChild);

                // Update post count
                const badge = document.getElementById('postCountBadge');
                if (badge) badge.textContent = parseInt(badge.textContent || 0) + 1;

                if (window.lucide) lucide.createIcons();
                toast('Post shared!', 'success');
            } else {
                const msg = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message ?? 'Error.');
                if (postFeedback) postFeedback.textContent = msg;
                toast(msg, 'error');
            }
        } catch {
            toast('Network error.', 'error');
        }
        submitBtn.disabled = false;
    });
});

/* ── Build post DOM element from JSON ─────────────────────────── */
function buildPostEl(post) {
    const article = document.createElement('article');
    article.className = 'panel post';
    article.dataset.postId = post.id;

    const mediaHtml = post.media && post.media.length
        ? `<div class="post-media-grid media-count-${Math.min(post.media.length, 4)}">
        ${post.media.map(m => m.media_type === 'video'
            ? `<video src="${m.url}" controls class="post-media-item"></video>`
            : `<img src="${m.url}" class="post-media-item" alt="Post image">`
        ).join('')}
       </div>`
        : '';

    const menuHtml = post.can_manage
        ? `<div class="post-menu-wrap">
        <button class="icon-btn post-menu-btn" type="button"><i data-lucide="more-horizontal"></i></button>
        <div class="post-menu hidden">
          <button class="post-menu-item edit-post-btn" type="button"
              data-post-id="${post.id}"
              data-text="${escapeHtml(post.text_content ?? '')}">
            <i data-lucide="pencil"></i> Edit
          </button>
          <button class="post-menu-item delete-post-btn danger" type="button"
              data-post-id="${post.id}">
            <i data-lucide="trash-2"></i> Delete
          </button>
        </div>
       </div>`
        : '';

    article.innerHTML = `
    <div class="post-head">
      <div class="avatar md"><img src="${post.user.avatar_url}" alt="${escapeHtml(post.user.name)}"></div>
      <div class="post-meta">
        <div class="post-name">${escapeHtml(post.user.name)}</div>
        <div class="post-sub">@${escapeHtml(post.user.username)} · ${post.created_at}</div>
      </div>
      ${menuHtml}
    </div>
    ${post.text_content ? `<div class="post-body post-text-content">${escapeHtml(post.text_content)}</div>` : ''}
    ${mediaHtml}
    <div class="post-actions">
      <button class="post-btn like-btn ${post.is_liked ? 'liked' : ''}" type="button" data-post-id="${post.id}">
        <i data-lucide="heart" class="like-icon"></i>
        <span class="like-count">${post.like_count}</span>
      </button>
      <button class="post-btn comment-toggle-btn" type="button" data-post-id="${post.id}">
        <i data-lucide="message-square"></i>
        <span class="comment-count">${post.comment_count}</span>
      </button>
      <button class="post-btn" type="button"><i data-lucide="share-2"></i></button>
      <button class="post-btn end" type="button"><i data-lucide="bookmark"></i></button>
    </div>
    <div class="comments-section hidden" id="comments-${post.id}">
      <div class="comment-composer">
        <div class="avatar sm"><img src="${post.user.avatar_url}" alt="You"></div>
        <div class="comment-input-wrap">
          <input type="text" class="comment-input" placeholder="Write a comment…" data-post-id="${post.id}">
          <button class="comment-send-btn" type="button" data-post-id="${post.id}"><i data-lucide="send"></i></button>
        </div>
      </div>
      <div class="comments-list" id="comments-list-${post.id}">
        ${post.comments.map(c => buildCommentHtml(c)).join('')}
      </div>
    </div>`;

    return article;
}

function buildCommentHtml(comment, isReply = false, parentId = null) {
    const canDelete = comment.can_delete;
    const repliesHtml = (!isReply && comment.replies) ? comment.replies.map(r => buildCommentHtml(r, true, comment.id)).join('') : '';
    const targetCommentId = isReply ? parentId : comment.id;
    return `
    <div class="comment-item ${isReply ? 'reply-item' : ''}" id="comment-${comment.id}">
      <div class="avatar sm"><img src="${comment.user.avatar_url}" alt="${escapeHtml(comment.user.name)}"></div>
      <div class="comment-bubble">
        <div class="comment-meta">
          <span class="comment-author">${escapeHtml(comment.user.name)}</span>
          <span class="comment-time">${comment.created_at}</span>
          ${canDelete ? `<button class="comment-delete-btn" type="button" data-comment-id="${comment.id}"><i data-lucide="x"></i></button>` : ''}
        </div>
        <p class="comment-text">${escapeHtml(comment.comment_text)}</p>
        <button class="reply-toggle-btn" type="button" data-comment-id="${targetCommentId}" data-post-id="${comment.post_id ?? ''}" data-reply-to="${escapeHtml(comment.user.username)}">Reply</button>
        ${!isReply ? `
        <div class="reply-composer hidden" id="reply-composer-${comment.id}">
          <input type="text" class="comment-input reply-input" placeholder="Write a reply…" data-post-id="${comment.post_id ?? ''}" data-parent-id="${comment.id}">
          <button class="comment-send-btn reply-send-btn" type="button" data-post-id="${comment.post_id ?? ''}" data-parent-id="${comment.id}"><i data-lucide="send"></i></button>
        </div>
        <div class="replies-list" id="replies-${comment.id}">${repliesHtml}</div>
        ` : ''}
      </div>
    </div>`;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

/* ================================================================
   POST INTERACTIONS — Like, 3-dot menu, Edit, Delete
================================================================ */
document.addEventListener('click', async (e) => {
    const postsFeed = document.getElementById('postsFeed');

    // ── LIKE ─────────────────────────────────────────────────────
    const likeBtn = e.target.closest('.like-btn');
    if (likeBtn) {
        const postId = likeBtn.dataset.postId;
        try {
            const res = await apiPost(window.ROUTES.toggleLike(postId), {});
            if (res.ok) {
                likeBtn.classList.toggle('liked', res.liked);
                const countEl = likeBtn.querySelector('.like-count');
                if (countEl) countEl.textContent = res.like_count;
                if (window.lucide) lucide.createIcons();
            }
        } catch { toast('Error.', 'error'); }
        return;
    }

    // ── TOGGLE COMMENTS ───────────────────────────────────────────
    const commentToggle = e.target.closest('.comment-toggle-btn');
    if (commentToggle) {
        const postId = commentToggle.dataset.postId;
        const section = document.getElementById(`comments-${postId}`);
        section?.classList.toggle('hidden');
        if (window.lucide) lucide.createIcons();
        return;
    }

    // ── 3-DOT MENU TOGGLE ────────────────────────────────────────
    const menuBtn = e.target.closest('.post-menu-btn');
    if (menuBtn) {
        e.stopPropagation();
        const menu = menuBtn.nextElementSibling;
        // Close all other menus
        $$('.post-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
        menu?.classList.toggle('hidden');
        if (window.lucide) lucide.createIcons();
        return;
    }

    // Close all open menus on click elsewhere
    if (!e.target.closest('.post-menu-wrap')) {
        $$('.post-menu').forEach(m => m.classList.add('hidden'));
    }

    // ── EDIT POST ────────────────────────────────────────────────
    const editBtn = e.target.closest('.edit-post-btn');
    if (editBtn) {
        const postId = editBtn.dataset.postId;
        const text = editBtn.dataset.text;
        const article = document.querySelector(`[data-post-id="${postId}"]`);
        if (!article) return;

        const textEl = article.querySelector('.post-text-content');
        const existing = article.querySelector('.post-edit-area');
        if (existing) return;  // Already editing

        // Hide menu
        editBtn.closest('.post-menu')?.classList.add('hidden');

        // Build inline editor
        const editorWrap = document.createElement('div');
        editorWrap.className = 'post-edit-area';
        editorWrap.innerHTML = `
      <textarea class="post-edit-textarea">${escapeHtml(text)}</textarea>
      <div class="post-edit-actions">
        <button class="btn-edit-cancel" type="button">Cancel</button>
        <button class="btn-edit-save" type="button" data-post-id="${postId}">Save</button>
      </div>`;

        if (textEl) {
            textEl.style.display = 'none';
            textEl.insertAdjacentElement('afterend', editorWrap);
        } else {
            article.querySelector('.post-head').insertAdjacentElement('afterend', editorWrap);
        }

        editorWrap.querySelector('textarea').focus();

        editorWrap.querySelector('.btn-edit-cancel').addEventListener('click', () => {
            editorWrap.remove();
            if (textEl) textEl.style.display = '';
        });

        editorWrap.querySelector('.btn-edit-save').addEventListener('click', async () => {
            const newText = editorWrap.querySelector('textarea').value.trim();
            if (!newText) { toast('Post cannot be empty.', 'error'); return; }

            const res = await apiPost(window.ROUTES.updatePost(postId), { text_content: newText }, 'PUT');
            if (res.ok) {
                if (textEl) { textEl.textContent = newText; textEl.style.display = ''; }
                editorWrap.remove();
                toast('Post updated!', 'success');
            } else {
                toast(res.message ?? 'Error.', 'error');
            }
        });
        return;
    }

    // ── DELETE POST ───────────────────────────────────────────────
    const deleteBtn = e.target.closest('.delete-post-btn');
    if (deleteBtn) {
        if (!confirm('Delete this post? This cannot be undone.')) return;

        const postId = deleteBtn.dataset.postId;
        const article = document.querySelector(`[data-post-id="${postId}"]`);

        const res = await apiPost(window.ROUTES.destroyPost(postId), {}, 'DELETE');
        if (res.ok) {
            article?.remove();
            const badge = document.getElementById('postCountBadge');
            if (badge && parseInt(badge.textContent) > 0) badge.textContent = parseInt(badge.textContent) - 1;
            toast('Post deleted.', 'success');

            // Show empty state if no posts
            const feed = document.getElementById('postsFeed');
            if (feed && feed.querySelectorAll('.post').length === 0) {
                feed.innerHTML = `<div class="empty-state panel">
          <i data-lucide="file-text"></i><p>No posts yet.</p></div>`;
                if (window.lucide) lucide.createIcons();
            }
        } else {
            toast(res.message ?? 'Error.', 'error');
        }
        return;
    }

    // ── COMMENT SEND ──────────────────────────────────────────────
    const sendBtn = e.target.closest('.comment-send-btn:not(.reply-send-btn)');
    if (sendBtn) {
        const postId = sendBtn.dataset.postId;
        const input = sendBtn.closest('.comment-input-wrap')?.querySelector('.comment-input')
            ?? sendBtn.previousElementSibling;
        await sendComment(postId, null, input);
        return;
    }

    // ── REPLY SEND ────────────────────────────────────────────────
    const replySend = e.target.closest('.reply-send-btn');
    if (replySend) {
        const postId = replySend.dataset.postId;
        const parentId = replySend.dataset.parentId;
        const input = document.querySelector(`.reply-input[data-parent-id="${parentId}"]`);
        await sendComment(postId, parentId, input);
        return;
    }

    // ── REPLY TOGGLE ──────────────────────────────────────────────
    const replyToggle = e.target.closest('.reply-toggle-btn');
    if (replyToggle) {
        const commentId = replyToggle.dataset.commentId;
        const replyTo = replyToggle.dataset.replyTo;
        const composer = document.getElementById(`reply-composer-${commentId}`);
        if (composer) {
            composer.classList.remove('hidden');
            const inp = composer.querySelector('input');
            if (inp) {
                inp.focus();
                if (replyTo && replyTo !== 'undefined' && replyTo !== '') {
                    const tag = `@${replyTo} `;
                    const currentVal = inp.value;
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

    // ── DELETE COMMENT ────────────────────────────────────────────
    const delComment = e.target.closest('.comment-delete-btn');
    if (delComment) {
        if (!confirm('Delete this comment?')) return;
        const commentId = delComment.dataset.commentId;
        const res = await apiPost(window.ROUTES.destroyComment(commentId), {}, 'DELETE');
        if (res.ok) {
            const el = document.getElementById(`comment-${commentId}`);
            el?.remove();
            toast('Comment deleted.', 'success');
        } else {
            toast(res.message ?? 'Error.', 'error');
        }
    }
});

/* ── Send comment/reply ───────────────────────────────────────── */
async function sendComment(postId, parentId, inputEl) {
    const text = inputEl?.value?.trim();
    if (!text) return;

    const body = { comment_text: text };
    if (parentId) body.parent_comment_id = parentId;

    const res = await apiPost(window.ROUTES.storeComment(postId), body);
    if (res.ok) {
        inputEl.value = '';

        const comment = res.comment;
        const html = buildCommentHtml(comment, !!parentId, parentId);

        if (parentId) {
            // Append to replies list
            const repliesList = document.getElementById(`replies-${parentId}`);
            if (repliesList) {
                repliesList.insertAdjacentHTML('beforeend', html);
            }
            // Hide reply composer
            document.getElementById(`reply-composer-${parentId}`)?.classList.add('hidden');
        } else {
            // Prepend to comments list
            const commentsList = document.getElementById(`comments-list-${postId}`);
            if (commentsList) {
                commentsList.insertAdjacentHTML('afterbegin', html);
            }
            // Update comment count
            const article = document.querySelector(`[data-post-id="${postId}"]`);
            const countEl = article?.querySelector('.comment-count');
            if (countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
        }

        if (window.lucide) lucide.createIcons();
    } else {
        toast(res.message ?? 'Error.', 'error');
    }
}

/* ── Enter key on comment inputs ─────────────────────────────── */
document.addEventListener('keydown', async (e) => {
    if (e.key !== 'Enter') return;

    const input = e.target;
    if (input.matches('.comment-input:not(.reply-input)')) {
        const postId = input.dataset.postId;
        await sendComment(postId, null, input);
    } else if (input.matches('.reply-input')) {
        const postId = input.dataset.postId;
        const parentId = input.dataset.parentId;
        await sendComment(postId, parentId, input);
    }
});
