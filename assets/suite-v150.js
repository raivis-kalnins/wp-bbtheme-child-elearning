/* E-Learning 3.8.11.50 - deterministic Events-parity homepage runtime. */
(function(W,D){
  'use strict';
  var CFG=W.wpbbSuiteV150||{};
  function q(sel,root){try{return(root||D).querySelector(sel);}catch(e){return null;}}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function kids(el){return el?Array.prototype.slice.call(el.children||[]):[];}
  function txt(el){return String(el&&el.textContent||'').replace(/\s+/g,' ').trim();}
  function uniq(arr){return arr.filter(function(x,i,a){return x&&a.indexOf(x)===i;});}
  function topLevel(arr){arr=uniq(arr||[]);return arr.filter(function(item){return !arr.some(function(other){return other!==item&&other.contains&&other.contains(item);});});}
  function list(v){return Array.isArray(v)?v.filter(Boolean):[];}

  function setImage(img,url){
    if(!img||!url)return;
    if(img.getAttribute('src')!==url)img.setAttribute('src',url);
    ['srcset','sizes','data-src','data-srcset','data-lazy-src','data-lazy-srcset'].forEach(function(a){img.removeAttribute(a);});
    img.style.removeProperty('opacity');
    img.style.removeProperty('visibility');
    img.style.removeProperty('filter');
    img.style.removeProperty('transform');
    img.loading='eager';img.decoding='async';
  }

  function bestHost(scope,cards){
    cards=topLevel(cards||[]);if(!scope||cards.length<2)return null;
    var candidates=uniq(cards.reduce(function(out,card){
      var node=card;
      for(var depth=0;node&&node!==scope&&depth<7;depth++,node=node.parentElement){
        if(node.matches&&node.matches('.row,.wpbb-row,.wpbb-v62-card-grid,.wpbb-sector-grid,.wp-block-post-template'))out.push(node);
      }
      return out;
    },[]));
    var best=null;
    candidates.forEach(function(host){
      var direct=kids(host).filter(function(child){return cards.some(function(card){return child===card||child.contains(card);});});
      if(direct.length<2)return;
      if(!best||direct.length>best.items.length)best={host:host,items:direct};
    });
    if(best)return best;
    var parent=cards[0]&&cards[0].parentElement;
    if(parent&&cards.every(function(card){return card.parentElement===parent;}))return{host:parent,items:cards.slice()};
    return null;
  }

  function markGrid(pick,cls){
    if(!pick||!pick.host||!pick.items||pick.items.length<2)return;
    pick.host.classList.add(cls);
    pick.items.forEach(function(item){item.classList.add('wpbb-v150-grid-cell');});
  }

  var heroInitialised=false;
  function repairHero(){
    var urls=list(CFG.hero);if(!urls.length)return;
    var shell=q('#wp-theme-main .wp-theme-sector-hero');if(!shell)return;
    var block=q('.wpbb-swiper--hero,.wpbb-v136-hero,.swiper',shell);if(!block)return;
    var slides=qa('.swiper-slide,.wpbb-swiper-slide',block).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});
    slides.forEach(function(slide,i){
      var media=q('.wpbb-swiper-slide__media,.wp-theme-hero__media,.wpbb-hero-media',slide);
      var img=media&&q('img',media);
      setImage(img,urls[i%urls.length]);
      if(media){media.style.removeProperty('background-image');media.style.removeProperty('filter');}
    });

    if(!heroInitialised){
      heroInitialised=true;
      var swiperEl=block.matches&&block.matches('.swiper')?block:(q('.swiper',block)||block);
      var sw=(swiperEl&&swiperEl.swiper)||block.swiper;
      if(sw){
        try{
          if(sw.autoplay&&typeof sw.autoplay.stop==='function')sw.autoplay.stop();
          if(sw.params)sw.params.autoplay=false;
          if(typeof sw.slideToLoop==='function')sw.slideToLoop(0,0,false);
          else if(typeof sw.slideTo==='function')sw.slideTo(0,0,false);
        }catch(e){}
      }
      // Fallback for pre-init markup: make the first authored slide visibly active.
      if(slides.length){
        slides.forEach(function(s,i){
          if(i===0){s.classList.add('swiper-slide-active');s.removeAttribute('aria-hidden');}
          else{s.classList.remove('swiper-slide-active');}
        });
      }
    }
  }

  function repairAbout(){
    if(!CFG.about)return;
    var section=q('#wp-theme-main .wp-theme-about-section');if(!section)return;
    setImage(q('.wp-theme-sector-media-text__media img,figure img,img',section),CFG.about);
  }

  function removeCourseGalleryUi(card){
    if(!card)return;
    var media=q('.wpbb-sector-card__media',card);
    if(media)media.classList.remove('wp-theme-item-gallery-card','wp-theme-item-gallery-link');
    qa('.wp-theme-item-gallery-card__open,.wp-theme-item-gallery-card__thumbs,.wp-theme-item-gallery-card__thumb,.wp-theme-item-gallery-card__more,.wp-theme-item-gallery__open,.wp-theme-item-gallery__thumbs,.wp-theme-item-gallery__thumb,.wp-theme-item-gallery__more',card).forEach(function(el){el.remove();});
    qa('a,button',card).forEach(function(el){if(/^\s*5\+1\s*$/.test(txt(el)))el.remove();});
  }

  function repairCourses(){
    var urls=list(CFG.courses);if(!urls.length)return;
    var section=q('#wp-theme-main .wpbb-courses-finder-section');if(!section)return;
    var cards=qa('.wpbb-sector-card',section).filter(function(card){return !!q('.wpbb-sector-card__media img',card);}).slice(0,urls.length);
    cards.forEach(function(card,i){removeCourseGalleryUi(card);setImage(q('.wpbb-sector-card__media img',card),urls[i]);});
  }

  function repairGallery(){
    var urls=list(CFG.gallery);if(!urls.length)return;
    var section=q('#wp-theme-main .wp-theme-gallery-section');if(!section)return;
    var slides=qa('.swiper-slide,.wpbb-swiper-slide',section).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});
    slides.slice(0,urls.length).forEach(function(slide,i){
      var media=q('.wpbb-swiper-slide__media,.swiper-slide__media,figure',slide);
      if(!media)return;
      var img=q('img',media);setImage(img,urls[i]);
      media.style.backgroundImage='none';
      media.style.removeProperty('--wpbb-v148-gallery-image');
    });
  }

  function repairBlog(){
    var urls=list(CFG.blog);if(!urls.length)return;
    var section=q('#wp-theme-main .wp-theme-insights-section');if(!section)return;
    var cards=qa('.wp-theme-blog-card',section).slice(0,urls.length);
    cards.forEach(function(card,i){setImage(q('img',card),urls[i]);});
  }

  function repairStats(){
    var scope=q('#wp-theme-main .wp-theme-home-stats');if(!scope)return;
    var cards=topLevel(qa('.wp-theme-sector-proof__item,.wpbb-fun-fact',scope));
    markGrid(bestHost(scope,cards),'wpbb-v150-stats-grid');
  }

  function repairProof(){
    var scope=q('#wp-theme-main .wpbb-sector-proof-band');if(!scope)return;
    var cards=topLevel(qa('.wpbb-sector-proof-card,.wpbb-v148-proof-card,.wpbb-icon-card,.wp-theme-sector-card',scope));
    if(cards.length<3){
      var titles=['Course-first architecture','Materials alongside lessons','Practical quizzes'];
      cards=topLevel(qa('article,.card,.wp-block-group',scope).filter(function(card){
        var h=q('h2,h3,h4,h5,h6',card);return h&&titles.indexOf(txt(h))!==-1;
      }));
    }
    var pick=bestHost(scope,cards);
    if(pick){
      markGrid(pick,'wpbb-v150-proof-grid');
      pick.items.forEach(function(cell){
        var card=q('.wpbb-sector-proof-card,.wpbb-v148-proof-card,.wpbb-icon-card,.wp-theme-sector-card,article,.card',cell);
        if(card)card.classList.add('wpbb-v150-proof-card');
      });
    }
  }

  function repairSections(){
    // The DB already provides deterministic row classes. Remove stale inline widths
    // on their direct cells so CSS grid is the only geometry owner.
    qa('#wp-theme-main .wp-theme-sector-services,#wp-theme-main .wp-theme-sector-industries,#wp-theme-main .wp-theme-case-grid,#wp-theme-main .wp-theme-sector-process-grid').forEach(function(row){
      kids(row).forEach(function(cell){
        if(!cell||!cell.style)return;
        cell.style.removeProperty('width');cell.style.removeProperty('max-width');cell.style.removeProperty('flex');cell.style.removeProperty('flex-basis');
      });
    });
  }

  function runMedia(){repairAbout();repairCourses();repairGallery();repairBlog();}
  function runLayout(){repairStats();repairProof();repairSections();}
  function runAll(){repairHero();runMedia();runLayout();}

  var timer=0;
  function schedule(){clearTimeout(timer);timer=W.setTimeout(runAll,45);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){runAll();W.setTimeout(runAll,180);W.setTimeout(runAll,650);W.setTimeout(runAll,1500);});
  W.addEventListener('resize',function(){clearTimeout(timer);timer=W.setTimeout(runLayout,80);},{passive:true});

  var finder=q('#wp-theme-main .wpbb-courses-finder-section');
  if(finder&&W.MutationObserver){
    new MutationObserver(function(records){
      if(records.some(function(r){return r.addedNodes&&r.addedNodes.length;}))W.setTimeout(repairCourses,20);
    }).observe(finder,{childList:true,subtree:true});
  }

  var main=q('#wp-theme-main');
  if(main&&W.MutationObserver){
    var queued=false;
    new MutationObserver(function(records){
      if(queued)return;
      if(!records.some(function(r){return r.addedNodes&&r.addedNodes.length;}))return;
      queued=true;W.setTimeout(function(){queued=false;runLayout();repairGallery();repairBlog();},80);
    }).observe(main,{childList:true,subtree:true});
  }
})(window,document);
