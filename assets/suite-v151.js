/* E-Learning 3.8.11.51 - media-only runtime; no page geometry mutation. */
(function(W,D){
  'use strict';
  var C=W.wpbbSuiteV151||{};
  function q(s,r){try{return(r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return[];}}
  function arr(v){return Array.isArray(v)?v.filter(Boolean):[];}
  function setImg(img,url){
    if(!img||!url)return;
    img.setAttribute('src',url);
    ['srcset','sizes','data-src','data-srcset','data-lazy-src','data-lazy-srcset'].forEach(function(a){img.removeAttribute(a);});
    img.style.removeProperty('opacity');img.style.removeProperty('visibility');img.style.removeProperty('filter');img.style.removeProperty('transform');
  }
  function removeCourseGallery(card){
    if(!card)return;
    qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more,.wp-theme-item-gallery__open,.wp-theme-item-gallery__thumbs,.wp-theme-item-gallery__thumb,.wp-theme-item-gallery__more',card).forEach(function(el){el.remove();});
    qa('a,button',card).forEach(function(el){if(/^\s*5\+1\s*$/.test(String(el.textContent||'')))el.remove();});
    var media=q('.wpbb-sector-card__media',card);if(media)media.classList.remove('wp-theme-item-gallery-card','wp-theme-item-gallery-link');
  }
  function media(){
    var about=q('#wp-theme-main .wp-theme-about-section');if(about&&C.about)setImg(q('img',about),C.about);
    var finder=q('#wp-theme-main .wpbb-courses-finder-section');
    if(finder){
      var cards=qa('.wpbb-sector-card',finder).filter(function(c){return !!q('.wpbb-sector-card__media img',c);});
      cards.slice(0,arr(C.courses).length).forEach(function(c,i){removeCourseGallery(c);setImg(q('.wpbb-sector-card__media img',c),C.courses[i]);});
    }
    var gallery=q('#wp-theme-main .wp-theme-gallery-section');
    if(gallery){qa('.swiper-slide,.wpbb-swiper-slide',gallery).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');}).slice(0,arr(C.gallery).length).forEach(function(s,i){setImg(q('img',s),C.gallery[i]);});}
    var insights=q('#wp-theme-main .wp-theme-insights-section');
    if(insights){qa('.wp-theme-blog-card',insights).slice(0,arr(C.blog).length).forEach(function(c,i){setImg(q('img',c),C.blog[i]);});}
  }
  function run(){media();}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',run,{once:true});else run();
  W.addEventListener('load',function(){run();W.setTimeout(run,250);W.setTimeout(run,900);});
  var finder=q('#wp-theme-main .wpbb-courses-finder-section');
  if(finder&&W.MutationObserver)new MutationObserver(function(){W.setTimeout(media,40);}).observe(finder,{childList:true,subtree:true});
})(window,document);
