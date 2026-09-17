(function(W,D){
  'use strict';
  var CFG=W.wpbbSuiteV148||{};
  var gallery=Array.isArray(CFG.gallery)?CFG.gallery.filter(Boolean):[];
  var courseImages=Array.isArray(CFG.courseImages)?CFG.courseImages.filter(Boolean):[];

  function q(sel,root){try{return(root||D).querySelector(sel);}catch(e){return null;}}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function kids(node){return node?Array.prototype.slice.call(node.children||[]):[];}
  function uniq(arr){return arr.filter(function(x,i){return x&&arr.indexOf(x)===i;});}
  function text(node){return String(node&&node.textContent||'').replace(/\s+/g,' ').trim();}

  function commonHost(nodes,root){
    nodes=uniq(nodes);if(nodes.length<2)return null;
    var p=nodes[0].parentElement,depth=0;
    while(p&&p!==D.body&&depth<10){
      if(root&&p!==root&&!root.contains(p))break;
      var direct=kids(p).filter(function(k){return nodes.some(function(n){return k===n||k.contains(n);});});
      if(direct.length>=Math.min(nodes.length,2))return p;
      p=p.parentElement;depth++;
    }
    return null;
  }

  function fixNestedFinderRail(){
    var section=q('#wp-theme-main .wpbb-courses-finder-section');if(!section)return;
    qa('.wpbb-sector-finder',section).forEach(function(finder){
      finder.classList.remove('wpbb-v137-align-section','wpbb-v136-align-section','wpbb-v146-rail');
      qa(':scope > .container,:scope > .wpbb-v67-section-inner,:scope > .wp-block-group__inner-container',finder).forEach(function(inner){
        inner.classList.remove('wpbb-v137-grid','wpbb-v136-grid');
      });
    });
  }

  function fixCourseCards(){
    var section=q('#wp-theme-main .wpbb-courses-finder-section');if(!section)return;
    var cards=qa('.wpbb-sector-card',section).filter(function(card){return !!q('.wpbb-sector-card__media img',card);}).slice(0,12);
    cards.forEach(function(card,index){
      var media=q('.wpbb-sector-card__media',card);
      if(media){
        media.classList.remove('wp-theme-item-gallery-card','wp-theme-item-gallery-link');
        qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more,.wp-theme-item-gallery__open,.wp-theme-item-gallery__thumbs,.wp-theme-item-gallery__thumb,.wp-theme-item-gallery__more',media).forEach(function(el){el.remove();});
      }
      qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more,.wp-theme-item-gallery__open,.wp-theme-item-gallery__thumbs,.wp-theme-item-gallery__thumb,.wp-theme-item-gallery__more',card).forEach(function(el){el.remove();});
      var img=q('.wpbb-sector-card__media img',card);
      if(img&&courseImages.length){
        var url=courseImages[index%courseImages.length];
        if(url&&img.getAttribute('src')!==url)img.src=url;
        img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('data-srcset');img.removeAttribute('data-lazy-srcset');
        img.loading='eager';img.decoding='async';
        img.style.removeProperty('opacity');img.style.removeProperty('visibility');img.style.removeProperty('filter');
      }
    });
  }

  function fixProofGrid(){
    var band=q('#wp-theme-main .wpbb-sector-proof-band');if(!band)return;
    var cards=qa('.wpbb-sector-proof-card,.wpbb-v148-proof-card',band).slice(0,3);
    if(cards.length!==3)return;
    var host=commonHost(cards,band);if(!host)return;
    host.classList.add('wpbb-v148-proof-grid');
    kids(host).forEach(function(cell){
      if(cards.some(function(card){return cell===card||cell.contains(card);})){cell.classList.add('wpbb-v148-proof-cell');}
    });
    cards.forEach(function(card){card.classList.add('wpbb-v148-proof-card');});
  }

  function fixProcess(){
    var section=q('#wp-theme-main .wp-theme-process-section');if(!section)return;
    var cards=qa('.wp-theme-sector-card,.wpbb-v137-process-card,.wpbb-v145-process-card',section).filter(function(card){
      return /^(choose|learn|check)$/i.test(text(q('h2,h3,h4',card)));
    }).slice(0,3);
    if(cards.length!==3)return;
    var badges=qa('.wp-theme-process-badge,.wpbb-v136-process-badge,.wpbb-badge',section).filter(function(b){return /^(0?[1-3])$/.test(text(b));}).slice(0,3);
    cards.forEach(function(card,index){
      var badge=badges[index];
      if(badge&&badge.parentElement!==card)card.insertBefore(badge,card.firstChild);
      card.classList.add('wpbb-v145-process-card');
    });
    var host=commonHost(cards,section);if(host){
      host.classList.add('wpbb-v145-process-grid');
      kids(host).forEach(function(cell){if(cards.some(function(card){return cell===card||cell.contains(card);})){cell.classList.add('wpbb-v145-process-cell');}});
    }
  }

  function fixGallery(){
    var section=q('#wp-theme-main .wp-theme-gallery-section');if(!section||!gallery.length)return;
    var slides=qa('.swiper-slide,.wpbb-swiper-slide',section).filter(function(slide){return !slide.classList.contains('swiper-slide-duplicate');});
    slides.slice(0,4).forEach(function(slide,index){
      var url=gallery[index%gallery.length];if(!url)return;
      slide.classList.add('wpbb-v148-gallery-slide');
      var media=q('.wpbb-swiper-slide__media,.swiper-slide__media,figure',slide);
      if(!media){media=D.createElement('div');media.className='wpbb-swiper-slide__media';slide.insertBefore(media,slide.firstChild);}
      media.classList.add('wpbb-v148-gallery-media');
      media.style.setProperty('--wpbb-v148-gallery-image','url("'+url.replace(/"/g,'%22')+'")');
      var img=q('img',media);
      if(!img){img=D.createElement('img');media.appendChild(img);}
      if(img.getAttribute('src')!==url)img.src=url;
      img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('width');img.removeAttribute('height');
      img.loading='eager';img.decoding='async';
      img.onerror=function(){media.style.backgroundImage='var(--wpbb-v148-gallery-image)';img.style.display='none';};
      img.style.removeProperty('display');img.style.opacity='1';img.style.visibility='visible';img.style.filter='none';
    });
  }

  function run(){
    if(D.body)D.body.classList.add('wpbb-v148-live-fixed');
    fixNestedFinderRail();fixCourseCards();fixProofGrid();fixProcess();fixGallery();
  }

  var timer=0;
  function schedule(){clearTimeout(timer);timer=W.setTimeout(run,50);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,320);W.setTimeout(run,1100);W.setTimeout(run,2400);});
  if(W.MutationObserver){
    var boot=function(){var main=q('#wp-theme-main');if(!main)return;new MutationObserver(function(records){if(records.some(function(r){return r.addedNodes&&r.addedNodes.length;}))schedule();}).observe(main,{childList:true,subtree:true});};
    if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
  }
})(window,document);
