/* E-Learning 3.8.11.52 - hero pager + exact row31 + course media cleanup. */
(function(W,D){
  'use strict';
  var C=W.wpbbSuiteV152||{};
  function q(s,r){try{return(r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return[];}}
  function arr(v){return Array.isArray(v)?v.filter(Boolean):[];}
  function setImg(img,url){
    if(!img||!url)return;
    img.setAttribute('src',url);
    ['srcset','sizes','data-src','data-srcset','data-lazy-src','data-lazy-srcset'].forEach(function(a){img.removeAttribute(a);});
    img.style.removeProperty('opacity');img.style.removeProperty('visibility');img.style.removeProperty('filter');img.style.removeProperty('transform');
  }
  function heroBlock(){
    return q('#wp-theme-main .wpbb-swiper--hero')||q('#wp-theme-main .wp-theme-sector-hero .swiper')||q('#wp-theme-main .wp-theme-hero .swiper');
  }
  function heroShell(block){return block&&(block.closest('.wp-theme-sector-hero,.wp-theme-hero,.wp-theme-section-shell,section')||block.parentElement);}
  function realSlides(block){return qa('.swiper-slide',block).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});}
  function swiperFor(block){var node=block&&block.classList.contains('swiper')?block:q('.swiper',block);return node&&(node.swiper||block.swiper)||block&&block.swiper||null;}
  function ensureHero(){
    var block=heroBlock();if(!block)return;
    var shell=heroShell(block)||block;
    block.classList.add('wpbb-v152-hero');shell.classList.add('wpbb-v152-hero-shell');
    var slides=realSlides(block), hero=arr(C.hero);
    slides.forEach(function(slide,i){
      var media=q('.wpbb-swiper-slide__media,.wp-theme-hero__media,.wpbb-hero-media',slide)||slide;
      if(hero.length)setImg(q('img',media),hero[i%hero.length]);
    });
    var count=slides.length;if(count<2)return;
    qa('.wpbb-v152-hero-pagination',shell).slice(1).forEach(function(x){x.remove();});
    var pager=q('.wpbb-v152-hero-pagination',shell);
    if(!pager){pager=D.createElement('div');pager.className='wpbb-v152-hero-pagination';pager.setAttribute('aria-label','Hero slides');shell.appendChild(pager);}
    if(pager.children.length!==count){
      pager.textContent='';
      for(var i=0;i<count;i++)(function(index){
        var b=D.createElement('button');b.type='button';b.className='wpbb-v152-hero-bullet';b.setAttribute('aria-label','Go to slide '+(index+1));
        b.addEventListener('click',function(){var sw=swiperFor(block);if(sw&&typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(sw&&typeof sw.slideTo==='function')sw.slideTo(index);paint();});
        pager.appendChild(b);
      })(i);
    }
    function paint(){
      var sw=swiperFor(block),idx=0;
      if(sw&&typeof sw.realIndex==='number')idx=((sw.realIndex%count)+count)%count;
      else{var active=q('.swiper-slide-active',block),p=slides.indexOf(active);if(p>=0)idx=p%count;}
      qa('.wpbb-v152-hero-bullet',pager).forEach(function(b,i){var on=i===idx;b.classList.toggle('is-active',on);if(on)b.setAttribute('aria-current','true');else b.removeAttribute('aria-current');});
    }
    paint();
    var sw=swiperFor(block);
    if(sw&&typeof sw.on==='function'&&!block.__wpbbV152Bound){block.__wpbbV152Bound=true;sw.on('slideChange',paint);sw.on('transitionEnd',paint);}
  }
  function removeCourseGallery(card){
    if(!card)return;
    qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more,.wp-theme-item-gallery__open,.wp-theme-item-gallery__thumbs,.wp-theme-item-gallery__thumb,.wp-theme-item-gallery__more',card).forEach(function(el){el.remove();});
    qa('a,button',card).forEach(function(el){if(/^\s*5\+1\s*$/.test(String(el.textContent||'')))el.remove();});
  }
  function cleanCourses(){
    var finder=q('#wp-theme-main .wpbb-courses-finder-section');if(!finder)return;
    qa('.wpbb-sector-card',finder).forEach(removeCourseGallery);
  }
  function markRow31(){var row=D.getElementById('wpbb-row-31');if(row&&row.closest('#wp-theme-main'))row.classList.add('wpbb-v152-row31');}
  function run(){ensureHero();cleanCourses();markRow31();}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',run,{once:true});else run();
  W.addEventListener('load',function(){run();W.setTimeout(run,200);W.setTimeout(run,800);W.setTimeout(run,1600);});
  var main=q('#wp-theme-main');
  if(main&&W.MutationObserver)new MutationObserver(function(ms){if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))W.setTimeout(run,60);}).observe(main,{childList:true,subtree:true});
})(window,document);
