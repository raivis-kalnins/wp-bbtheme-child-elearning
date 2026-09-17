(function(W,D){
  'use strict';
  var CFG=W.wpbbSuiteV149||{};
  var ROOT=D.documentElement;
  function q(sel,root){try{return(root||D).querySelector(sel);}catch(e){return null;}}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function list(value){return Array.isArray(value)?value.filter(Boolean):[];}

  function setImage(img,url){
    if(!img||!url)return;
    if(img.getAttribute('src')!==url)img.setAttribute('src',url);
    img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('data-srcset');img.removeAttribute('data-lazy-srcset');
    img.style.removeProperty('opacity');img.style.removeProperty('visibility');img.style.removeProperty('filter');
  }

  /* Events-theme idea, but measure the actual header inner row so section
     content lines up with the logo/navigation rather than the container box. */
  function measureRail(){
    var candidates=[
      q('.wp-theme-header-main .wp-theme-header-main__inner'),
      q('.wp-theme-header-main > .container'),
      q('.wp-theme-site-header .container'),
      q('.wp-theme-site-footer .container')
    ].filter(Boolean);
    var best=null;
    candidates.some(function(el){
      if(!el.getBoundingClientRect)return false;
      var r=el.getBoundingClientRect();
      if(r.width>320&&r.width<=W.innerWidth+2){best=r;return true;}
      return false;
    });
    if(!best)return;
    ROOT.style.setProperty('--wpbb-v149-left',Math.max(16,Math.round(best.left))+'px');
    ROOT.style.setProperty('--wpbb-v149-right',Math.max(16,Math.round(W.innerWidth-best.right))+'px');
  }

  function repairHero(){
    var urls=list(CFG.hero);if(!urls.length)return;
    var hero=q('#wp-theme-main .wpbb-v136-hero,#wp-theme-main .wpbb-swiper--hero');if(!hero)return;
    qa('.swiper-slide,.wpbb-swiper-slide',hero).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');}).forEach(function(slide,i){
      var media=q('.wpbb-swiper-slide__media,.wp-theme-hero__media,.wpbb-hero-media',slide);
      var img=media&&q('img',media);setImage(img,urls[i%urls.length]);
    });
  }

  function repairAbout(){
    if(!CFG.about)return;
    var section=q('#wp-theme-main .wp-theme-about-section');if(!section)return;
    var img=q('.wp-theme-sector-media-text__media img,figure img,img',section);setImage(img,CFG.about);
  }

  function repairCourses(){
    var urls=list(CFG.courses);if(!urls.length)return;
    var section=q('#wp-theme-main .wpbb-courses-finder-section');if(!section)return;
    qa('.wpbb-sector-card',section).filter(function(card){return !!q('.wpbb-sector-card__media img',card);}).slice(0,urls.length).forEach(function(card,i){
      setImage(q('.wpbb-sector-card__media img',card),urls[i]);
    });
  }

  function repairGallery(){
    var urls=list(CFG.gallery);if(!urls.length)return;
    var section=q('#wp-theme-main .wp-theme-gallery-section');if(!section)return;
    qa('.swiper-slide,.wpbb-swiper-slide',section).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');}).slice(0,urls.length).forEach(function(slide,i){
      var media=q('.wpbb-swiper-slide__media,.swiper-slide__media,figure',slide);
      var img=media&&q('img',media);setImage(img,urls[i]);
      if(media){
        media.style.setProperty('--wpbb-v148-gallery-image','url("'+urls[i].replace(/"/g,'%22')+'")');
        media.style.backgroundImage='url("'+urls[i].replace(/"/g,'%22')+'")';
      }
    });
  }

  function repairBlog(){
    var urls=list(CFG.blog);if(!urls.length)return;
    var section=q('#wp-theme-main .wp-theme-insights-section');if(!section)return;
    qa('.wp-theme-blog-card',section).slice(0,urls.length).forEach(function(card,i){
      setImage(q('img',card),urls[i]);
    });
  }

  function run(){measureRail();repairHero();repairAbout();repairCourses();repairGallery();repairBlog();}
  var timer=0;function schedule(){clearTimeout(timer);timer=W.setTimeout(run,40);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,300);W.setTimeout(run,1000);});
  W.addEventListener('resize',schedule,{passive:true});

  var finder=q('#wp-theme-main .wpbb-courses-finder-section');
  if(finder&&W.MutationObserver){new MutationObserver(function(records){if(records.some(function(r){return r.addedNodes&&r.addedNodes.length;}))schedule();}).observe(finder,{childList:true,subtree:true});}
})(window,document);
