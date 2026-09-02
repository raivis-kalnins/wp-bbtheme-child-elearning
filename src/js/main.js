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


function syncSwiperNavigationCenter(block) {
  const swiper = Array.from(block.children).find((child) => child.classList && child.classList.contains('swiper')) || block.querySelector('.swiper');
  if (!swiper) return;

  const slide = swiper.querySelector('.swiper-slide-active.wpbb-swiper-slide, .swiper-slide-visible.wpbb-swiper-slide, .wpbb-swiper-slide');
  let y = 0;
  if (slide) {
    const blockRect = block.getBoundingClientRect();
    const slideRect = slide.getBoundingClientRect();
    if (slideRect.height > 0) y = (slideRect.top - blockRect.top) + (slideRect.height / 2);
  }
  if (!(Number.isFinite(y) && y > 0)) y = swiper.offsetTop + (swiper.clientHeight / 2);
  if (Number.isFinite(y) && y > 0) block.style.setProperty('--wpbb-swiper-nav-y', `${Math.round(y)}px`);
}


function normalizePartnerSwipers() {
  document.querySelectorAll('.wp-theme-partners-section .swiper').forEach((swiperElement) => {
    const swiper = swiperElement.swiper;
    if (!swiper || !swiper.params) return;

    const normalize = (params) => {
      if (!params) return;
      params.centeredSlides = false;
      params.centeredSlidesBounds = false;
      params.centerInsufficientSlides = false;
      params.slidesOffsetBefore = 0;
      params.slidesOffsetAfter = 0;
    };
    normalize(swiper.params);
    normalize(swiper.originalParams);
    swiper.update();
  });
}

function syncAllSwiperNavigationCenters() {
  document.querySelectorAll('.wpbb-swiper-block--nav-gutter').forEach(syncSwiperNavigationCenter);
}

function moveTestimonialNavigationToOuterGutter() {
  document.querySelectorAll('.wpbb-swiper-block').forEach((block) => {
    const cards = block.querySelector('.wpbb-swiper--cards');
    const explicitTestimonials = block.closest('.business-testimonials, .wp-theme-testimonials-section');
    if (!cards && !explicitTestimonials) return;
    if (block.querySelector('.wpbb-swiper--hero, .wpbb-swiper--gallery, .wpbb-swiper--logos')) return;

    const prev = block.querySelector('.swiper-button-prev');
    const next = block.querySelector('.swiper-button-next');
    if (!prev && !next) return;

    block.classList.add('wpbb-swiper-block--nav-gutter');
    if (prev && prev.parentElement !== block) block.appendChild(prev);
    if (next && next.parentElement !== block) block.appendChild(next);
    syncSwiperNavigationCenter(block);
  });
}

function initPresentationGeometry() {
  let resizeTimer = 0;
  const run = () => {
    moveTestimonialNavigationToOuterGutter();
    normalizePartnerSwipers();
    window.setTimeout(() => {
      moveTestimonialNavigationToOuterGutter();
      normalizePartnerSwipers();
      syncAllSwiperNavigationCenters();
    }, 600);
  };
  window.addEventListener('resize', () => {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(() => {
      normalizePartnerSwipers();
      syncAllSwiperNavigationCenters();
    }, 100);
  }, { passive: true });
  if (document.readyState === 'complete') run();
  else window.addEventListener('load', run, { once: true });
}

function boot() {
  initHeader();
  observeMotion();
  initBlog();
  initPresentationGeometry();
  initCourseVideoModal();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
