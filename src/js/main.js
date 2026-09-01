import { initHeader } from './components/header.js';
import { observeMotion } from './components/motion.js';
import { initBlog } from './components/blog.js';



function initCourseVideoModal() {
  const triggers = Array.from(document.querySelectorAll('[data-course-video-modal]'));
  if (!triggers.length) return;

  const toEmbedUrl = (raw) => {
    try {
      const url = new URL(raw, window.location.href);
      const host = url.hostname.replace(/^www\./, '').toLowerCase();
      let id = '';
      if (host === 'youtu.be') id = url.pathname.split('/').filter(Boolean)[0] || '';
      if (host === 'youtube.com' || host === 'm.youtube.com' || host === 'youtube-nocookie.com') {
        if (url.pathname === '/watch') id = url.searchParams.get('v') || '';
        if (!id && /^\/(embed|shorts)\//.test(url.pathname)) id = url.pathname.split('/')[2] || '';
      }
      if (!id || !/^[A-Za-z0-9_-]{6,}$/.test(id)) return '';
      return `https://www.youtube-nocookie.com/embed/${encodeURIComponent(id)}?autoplay=1&rel=0&modestbranding=1`;
    } catch (e) {
      return '';
    }
  };

  const modal = document.createElement('div');
  modal.className = 'wpbb-course-video-modal';
  modal.hidden = true;
  modal.setAttribute('role', 'dialog');
  modal.setAttribute('aria-modal', 'true');
  modal.setAttribute('aria-labelledby', 'wpbb-course-video-modal-title');
  modal.innerHTML = `
    <div class="wpbb-course-video-modal__backdrop" data-course-video-close></div>
    <div class="wpbb-course-video-modal__dialog" role="document">
      <div class="wpbb-course-video-modal__bar">
        <h2 id="wpbb-course-video-modal-title" class="wpbb-course-video-modal__title"></h2>
        <button type="button" class="wpbb-course-video-modal__close" data-course-video-close aria-label="Close video">&times;</button>
      </div>
      <div class="wpbb-course-video-modal__frame">
        <iframe title="Course video" src="about:blank" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
      </div>
    </div>`;
  document.body.appendChild(modal);

  const iframe = modal.querySelector('iframe');
  const title = modal.querySelector('.wpbb-course-video-modal__title');
  const closeButton = modal.querySelector('.wpbb-course-video-modal__close');
  let returnFocus = null;

  const close = () => {
    if (modal.hidden) return;
    modal.hidden = true;
    iframe.src = 'about:blank';
    document.documentElement.classList.remove('wpbb-course-video-modal-open');
    document.body.classList.remove('wpbb-course-video-modal-open');
    if (returnFocus && typeof returnFocus.focus === 'function') returnFocus.focus();
  };

  const open = (trigger) => {
    const embed = toEmbedUrl(trigger.dataset.videoUrl || '');
    if (!embed) return;
    returnFocus = trigger;
    title.textContent = trigger.dataset.videoTitle || trigger.textContent.trim() || 'Course video';
    iframe.src = embed;
    modal.hidden = false;
    document.documentElement.classList.add('wpbb-course-video-modal-open');
    document.body.classList.add('wpbb-course-video-modal-open');
    window.requestAnimationFrame(() => closeButton.focus());
  };

  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-course-video-modal]');
    if (trigger) {
      event.preventDefault();
      open(trigger);
      return;
    }
    if (event.target.closest('[data-course-video-close]')) {
      event.preventDefault();
      close();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) close();
  });
}

function boot() {
  initHeader();
  observeMotion();
  initBlog();
  initCourseVideoModal();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
