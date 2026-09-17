(function(W,D){
  'use strict';
  var CFG=W.wpbbSuiteV147||{};
  var courseImages=Array.isArray(CFG.courseImages)?CFG.courseImages.filter(Boolean):[];
  var proofCards=Array.isArray(CFG.proofCards)?CFG.proofCards:[];

  function q(sel,root){try{return(root||D).querySelector(sel);}catch(e){return null;}}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function txt(el){return String(el&&el.textContent||'').replace(/\s+/g,' ').trim();}
  function norm(value){return String(value||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').replace(/\s+/g,' ').trim();}

  function repairFinder(){
    var finder=q('#wp-theme-main .wpbb-courses-finder-section .wpbb-sector-finder');
    if(!finder)return;
    finder.classList.remove('wpbb-v146-rail');
    var node=finder.parentElement;
    for(var depth=0;node&&depth<8;depth++,node=node.parentElement){
      if(node.classList)node.classList.remove('wpbb-v146-rail');
      if(node.classList&&node.classList.contains('wpbb-courses-finder-section'))break;
    }
  }

  function repairCourseCards(){
    var section=q('#wp-theme-main .wpbb-courses-finder-section');if(!section)return;
    var cards=qa('.wpbb-sector-card',section).filter(function(card){return !!q('.wpbb-sector-card__media img',card);}).slice(0,12);
    cards.forEach(function(card,index){
      card.dataset.wpbbChildCardGalleryReady='true';
      var media=q('.wpbb-sector-card__media',card);
      if(media){
        media.classList.remove('wp-theme-item-gallery-card','wp-theme-item-gallery-link');
        qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more',media).forEach(function(el){el.remove();});
      }
      qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more',card).forEach(function(el){el.remove();});
      var img=q('.wpbb-sector-card__media img',card);
      var url=courseImages[index%Math.max(courseImages.length,1)]||'';
      if(img&&url){
        if(img.getAttribute('src')!==url)img.src=url;
        img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('data-srcset');img.removeAttribute('data-lazy-srcset');
        img.loading='eager';img.decoding='async';
        img.style.removeProperty('opacity');img.style.removeProperty('visibility');img.style.removeProperty('filter');
      }
    });
  }

  function repairProof(){
    var band=q('#wp-theme-main .wpbb-sector-proof-band');if(!band)return;
    var cards=qa('.wpbb-sector-proof-card,.wpbb-icon-card',band).slice(0,3);
    if(cards.length!==3)return;
    cards.forEach(function(card,index){
      var copy=proofCards[index]||{};
      var title=q('h1,h2,h3,h4,h5,h6,.card-title,.wpbb-icon-card__title',card);
      var body=q('p,.card-text,.wpbb-icon-card__text',card);
      if(title&&norm(txt(title))==='card title'&&copy.title)title.textContent=copy.title;
      if(body&&/^add a short description\.?$/i.test(txt(body))&&copy.text)body.textContent=copy.text;
      card.classList.add('wpbb-v147-proof-card');
    });
  }

  function run(){
    if(D.body)D.body.classList.add('wpbb-v147-live-fixed');
    repairFinder();repairCourseCards();repairProof();
  }

  var timer=0;
  function schedule(){clearTimeout(timer);timer=W.setTimeout(run,40);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,280);W.setTimeout(run,900);});
  if(W.MutationObserver){
    var boot=function(){var main=q('#wp-theme-main');if(!main)return;new MutationObserver(function(records){if(records.some(function(record){return record.addedNodes&&record.addedNodes.length;}))schedule();}).observe(main,{childList:true,subtree:true});};
    if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
  }
})(window,document);
