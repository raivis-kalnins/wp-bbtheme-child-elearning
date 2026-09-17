(function(W,D){
  'use strict';
  var cfg=W.wpbbSuiteV145||{};
  var heroUrls=Array.isArray(cfg.hero)?cfg.hero.filter(Boolean):[];
  var galleryUrls=Array.isArray(cfg.gallery)?cfg.gallery.filter(Boolean):[];

  function q(s,r){try{return (r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return [];}}
  function kids(n){return n?Array.prototype.slice.call(n.children||[]):[];}
  function uniq(a){return a.filter(function(x,i){return x&&a.indexOf(x)===i;});}
  function text(n){return String(n&&n.textContent||'').replace(/\s+/g,' ').trim();}

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

  function markGrid(host,nodes,gridClass,cellClass){
    if(!host||!nodes.length)return;
    host.classList.add(gridClass);
    kids(host).forEach(function(cell){
      if(nodes.some(function(n){return cell===n||cell.contains(n);})){cell.classList.add(cellClass);}
    });
  }

  function repairProof(){
    var main=q('#wp-theme-main');if(!main)return;
    var cards=uniq(qa('.wpbb-sector-proof-card',main)).slice(0,3);
    if(cards.length!==3){
      var band=q('.wpbb-sector-proof-band',main);
      if(band)cards=uniq(qa('.wpbb-icon-card',band)).slice(0,3);
    }
    if(cards.length!==3)return;
    var band=cards[0].closest('.wpbb-sector-proof-band,.wp-theme-section-shell,.wpbb-v67-section-shell')||main;
    var host=commonHost(cards,band)||commonHost(cards,main);if(!host)return;
    markGrid(host,cards,'wpbb-v145-proof-grid','wpbb-v145-proof-cell');
    cards.forEach(function(card){card.classList.add('wpbb-v145-proof-card');});
  }

  function repairProcess(){
    qa('#wp-theme-main .wp-theme-process-section').forEach(function(section){
      var cards=uniq(qa('.wp-theme-sector-card,.wpbb-v137-process-card,.wp-theme-process-card',section)).filter(function(card){
        return /choose|learn|check/i.test(text(q('h2,h3,h4',card))||text(card));
      }).slice(0,3);
      if(cards.length!==3)return;

      var badges=uniq(qa('.wp-theme-process-badge,.wpbb-v136-process-badge,.wpbb-badge',section)).filter(function(b){
        return /^(0?[1-3])$/.test(text(b));
      }).slice(0,3);

      cards.forEach(function(card,i){
        var badge=badges[i];
        if(badge&&badge.parentElement!==card){card.insertBefore(badge,card.firstChild);}
        card.classList.add('wpbb-v145-process-card');
      });

      var host=commonHost(cards,section);if(!host)return;
      markGrid(host,cards,'wpbb-v145-process-grid','wpbb-v145-process-cell');

      kids(host).forEach(function(cell){
        var hasCard=cards.some(function(card){return cell===card||cell.contains(card);});
        if(!hasCard&&!text(cell)&&!q('img,svg,form,input,select,button,video,iframe',cell)){
          cell.classList.add('wpbb-v145-empty-process-cell');
        }
      });
    });
  }

  function repairGallery(){
    qa('#wp-theme-main .wp-theme-gallery-section').forEach(function(section){
      var slides=qa('.wpbb-swiper--gallery .wpbb-swiper-slide,.wpbb-swiper--gallery .swiper-slide,.swiper .wpbb-swiper-slide',section)
        .filter(function(slide){return !slide.classList.contains('swiper-slide-duplicate');});
      if(!slides.length)return;
      slides.slice(0,4).forEach(function(slide,i){
        var url=galleryUrls[i%galleryUrls.length];if(!url)return;
        slide.classList.add('wpbb-v145-gallery-slide');
        var media=q('.wpbb-swiper-slide__media,.swiper-slide__media,figure',slide);
        if(!media){media=D.createElement('div');media.className='wpbb-swiper-slide__media';slide.insertBefore(media,slide.firstChild);}
        media.classList.add('wpbb-v145-gallery-media');
        media.style.setProperty('--wpbb-v145-gallery-image','url("'+url.replace(/"/g,'%22')+'")');
        var img=q('img',media);
        if(!img){img=D.createElement('img');media.appendChild(img);}
        if(img.getAttribute('src')!==url)img.src=url;
        img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('width');img.removeAttribute('height');
        img.loading='eager';img.decoding='async';
        img.style.opacity='1';img.style.visibility='visible';img.style.filter='none';
      });
    });
  }

  function repairHero(){
    var hero=q('#wp-theme-main .wpbb-v136-hero,#wp-theme-main .wpbb-swiper--hero');if(!hero||!heroUrls.length)return;
    var slides=qa('.swiper-slide',hero).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});
    slides.forEach(function(slide,i){
      var url=heroUrls[i%heroUrls.length];
      var img=q('.wpbb-swiper-slide__media img,.wp-theme-hero__media img,.wpbb-hero-media img',slide);
      if(!img||!url)return;
      if(img.getAttribute('src')!==url)img.src=url;
      img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('width');img.removeAttribute('height');
      img.loading='eager';img.decoding='async';
      try{img.fetchPriority=i===0?'high':'auto';}catch(e){}
    });
  }

  function run(){
    if(D.body)D.body.classList.add('wpbb-v145-live-fixed');
    repairHero();repairProof();repairProcess();repairGallery();
  }

  var timer=0;
  function schedule(){clearTimeout(timer);timer=W.setTimeout(run,40);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,500);W.setTimeout(run,1200);W.setTimeout(run,2200);});
  if(W.MutationObserver){
    new MutationObserver(function(ms){
      if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))schedule();
    }).observe(D.documentElement,{childList:true,subtree:true});
  }
})(window,document);
