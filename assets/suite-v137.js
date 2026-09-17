(function(W,D){
  'use strict';
  var cfg=W.wpbbSuiteV137||{};
  var heroUrls=Array.isArray(cfg.heroUrls)?cfg.heroUrls.filter(Boolean):[];
  var theme=String(cfg.themeKey||'sector');

  function q(s,r){try{return (r||D).querySelector(s);}catch(e){return null;}}
  function qa(s,r){try{return Array.prototype.slice.call((r||D).querySelectorAll(s));}catch(e){return [];}}
  function kids(n){return n?Array.prototype.slice.call(n.children||[]):[];}
  function txt(n){return String(n&&n.textContent||'').replace(/\s+/g,' ').trim();}
  function norm(s){return String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').replace(/\s+/g,' ').trim();}
  function uniq(a){return a.filter(function(x,i){return x&&a.indexOf(x)===i;});}
  function hide(n){if(n&&n.classList)n.classList.add('wpbb-v137-hidden');}
  function mark(){if(D.body)D.body.classList.add('wpbb-v137','wpbb-v137-theme-'+theme.replace(/[^a-z0-9_-]/gi,'-'));}

  function removeMalformedBlockText(){
    var main=q('#wp-theme-main');if(!main||!D.createTreeWalker)return;
    var walker=D.createTreeWalker(main,NodeFilter.SHOW_TEXT),n,remove=[];
    while((n=walker.nextNode())){
      var t=String(n.nodeValue||'');
      if(/wp:wpbb\/icon-card|svgCode|wpbb-sector-proof-card/.test(t)&&(/<!--|<!–|wp:wpbb/.test(t)))remove.push(n);
    }
    remove.forEach(function(x){if(x.parentNode)x.parentNode.removeChild(x);});
  }

  function heroBlocks(){
    return uniq(qa('#wp-theme-main .wpbb-swiper--hero,#wp-theme-main .wp-theme-sector-hero .swiper,#wp-theme-main .wp-theme-hero .swiper').filter(function(b){
      return !b.closest('.wpbb-swiper--hero')||b.classList.contains('wpbb-swiper--hero');
    }));
  }
  function heroShell(block){return block&&block.closest('.wp-theme-sector-hero,.wp-theme-hero,.wp-theme-section-shell,section')||block&&block.parentElement||block;}
  function heroSlides(block){return qa('.swiper-slide',block).filter(function(s){return !s.classList.contains('swiper-slide-duplicate');});}
  function heroMedia(slide){return q('.wpbb-swiper-slide__media,.wp-theme-hero__media,.wpbb-hero-media',slide);}
  function ensureHeroImage(slide,url,index){
    if(!slide||!url)return;
    var media=heroMedia(slide)||slide,img=q('img',media);
    if(!img){img=D.createElement('img');img.className='wpbb-v136-hero-image';media.appendChild(img);}
    if(img.getAttribute('src')!==url)img.src=url;
    img.removeAttribute('srcset');img.removeAttribute('sizes');img.removeAttribute('width');img.removeAttribute('height');
    img.loading='eager';img.decoding='async';try{img.fetchPriority=index===0?'high':'auto';}catch(e){}
    if(media.style)media.style.removeProperty('background-image');
  }
  function repairHero(){
    var blocks=heroBlocks();if(!blocks.length)return;
    blocks.forEach(function(block,i){
      var shell=heroShell(block);
      if(i>0){hide(shell||block);return;}
      block.classList.add('wpbb-v136-hero','wpbb-v137-hero');
      if(shell)shell.classList.add('wpbb-v136-hero-shell','wpbb-v137-hero-shell');
      var ss=heroSlides(block);
      ss.forEach(function(s,idx){if(heroUrls.length)ensureHeroImage(s,heroUrls[idx%heroUrls.length],idx);});
      var host=shell||block.parentElement||block;
      qa('.wpbb-v135-hero-pagination,.wpbb-v134-hero-pagination,.wpbb-v133-hero-pagination,.wpbb-v128-hero-pagination,.wpbb-v127-hero-pagination,.wpbb-v126-hero-pagination,.swiper-pagination',host).forEach(hide);
      var count=Math.max(1,Math.min(3,heroUrls.length||ss.length||3));
      var pager=q('.wpbb-v136-hero-pagination',host);
      if(!pager){
        pager=D.createElement('div');pager.className='wpbb-v136-hero-pagination';pager.setAttribute('aria-label','Hero slides');host.appendChild(pager);
        for(var bi=0;bi<count;bi++)(function(index){
          var b=D.createElement('button');b.type='button';b.className='wpbb-v136-hero-bullet';b.setAttribute('aria-label','Go to slide '+(index+1));
          b.addEventListener('click',function(){
            var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block),sw=node&&node.swiper||block.swiper;
            if(sw&&typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(sw&&typeof sw.slideTo==='function')sw.slideTo(index);
            paint();
          });pager.appendChild(b);
        })(bi);
      }
      function paint(){
        var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block),sw=node&&node.swiper||block.swiper,idx=0;
        if(sw&&typeof sw.realIndex==='number')idx=((sw.realIndex%count)+count)%count;
        else{var a=q('.swiper-slide-active',block),arr=heroSlides(block),p=arr.indexOf(a);if(p>=0)idx=p%count;}
        qa('.wpbb-v136-hero-bullet',pager).forEach(function(b,j){b.classList.toggle('is-active',j===idx);});
      }
      paint();
      var node=block.classList.contains('swiper')?block:(q('.swiper',block)||block),sw=node&&node.swiper||block.swiper;
      if(sw&&typeof sw.on==='function'&&!block.__wpbbV137Bound){block.__wpbbV137Bound=true;sw.on('slideChange',paint);sw.on('transitionEnd',paint);}
    });
  }

  function finderSignature(finder){
    var form=finder.matches&&finder.matches('form')?finder:q('form',finder)||finder;
    var fields=qa('input,select,textarea,button',form).map(function(el){
      return [String(el.tagName||'').toLowerCase(),String(el.getAttribute('type')||''),norm(el.getAttribute('name')||''),norm(el.getAttribute('placeholder')||''),norm(txt(el))].join(':');
    });
    return norm(txt(form))+'|'+fields.join('|');
  }
  function cleanupDuplicateFinders(){
    var main=q('#wp-theme-main');if(!main)return;
    var seen={};
    var finders=qa('.wpbb-v97-hero-finder',main);
    // Older imports sometimes lost the finder wrapper class; include the exact
    // compact hero search form, but never the richer catalogue/filter form.
    qa('form',main).forEach(function(form){
      var ph=qa('input',form).map(function(i){return norm(i.getAttribute('placeholder')||'');}).join(' ');
      if(ph.indexOf('what do you want to learn')!==-1){var wrap=form.closest('.wpbb-v97-hero-finder')||form;if(finders.indexOf(wrap)<0)finders.push(wrap);}
    });
    finders.forEach(function(finder){
      var sig=finderSignature(finder);if(!sig)return;
      if(seen[sig])hide(finder);else seen[sig]=finder;
    });
  }

  function cleanupPlaceholderRows(){
    var main=q('#wp-theme-main');if(!main)return;
    qa('.wpbb-row,.row,.wpbb-v62-card-grid',main).forEach(function(row){
      if(row.closest('.wp-theme-process-section'))return;
      var cells=kids(row).filter(function(c){return c.nodeType===1&&!c.classList.contains('wpbb-v137-hidden');});
      if(cells.length<2||cells.length>6)return;
      var generic=0,meaningful=0;
      cells.forEach(function(cell){
        var title=q('.wpbb-icon-card__title,.card-title,h2,h3,h4,h5,h6,strong',cell);
        var body=q('.wpbb-icon-card__text,.card-text,p',cell);
        var tn=norm(txt(title)),bn=norm(txt(body));
        if(tn==='card title'&&(bn==='add a short description'||bn===''))generic++;
        else if(txt(cell)||q('img,svg,form,input,select,button,video,iframe',cell))meaningful++;
      });
      if(generic>=2&&generic===cells.length&&meaningful===0)hide(row);
    });
    // Known stale imported demo row: hide it only when it is still generic.
    var row31=D.getElementById('wpbb-row-31');
    if(row31&&row31.closest('#wp-theme-main')){
      var t=norm(txt(row31));
      if((t.match(/card title/g)||[]).length>=2&&(t.match(/add a short description/g)||[]).length>=2)hide(row31);
    }
  }

  function commonHost(cards,root){
    cards=uniq(cards);if(cards.length<2)return null;
    var p=cards[0].parentElement,depth=0;
    while(p&&p!==D.body&&depth<8){
      if(root&&root!==p&&!root.contains(p))break;
      var direct=kids(p).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
      if(direct.length>=Math.min(cards.length,2))return p;
      p=p.parentElement;depth++;
    }
    return null;
  }
  function markGrid(host,cards,maxCols){
    if(!host||!cards||cards.length<2)return;
    var direct=kids(host).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
    if(direct.length<2)return;
    var cols=Math.max(2,Math.min(maxCols||direct.length,direct.length,4));
    host.classList.add('wpbb-v137-grid','wpbb-v137-cols-'+cols);
    direct.forEach(function(cell){cell.classList.add('wpbb-v137-grid-cell');});
  }

  function repairProcess(){
    qa('#wp-theme-main .wp-theme-process-section').forEach(function(section){
      section.classList.add('wpbb-v137-process-section');
      var cards=uniq(qa('.wp-theme-process-card',section));
      if(cards.length<2)cards=uniq(qa('.wp-theme-sector-card,.wpbb-icon-card',section));
      if(cards.length<2)return;
      var host=commonHost(cards,section);if(!host)return;
      host.classList.add('wpbb-v137-process-grid');
      kids(host).forEach(function(cell){if(cards.some(function(c){return cell===c||cell.contains(c);})){cell.classList.add('wpbb-v137-grid-cell');}});
      cards.forEach(function(card){card.classList.add('wpbb-v137-process-card');});
    });
  }

  function repairStats(){
    qa('#wp-theme-main .wp-theme-sector-proof,#wp-theme-main .wp-theme-home-stats').forEach(function(scope){
      var cards=uniq(qa('.wp-theme-sector-proof__item,.wpbb-fun-fact',scope));if(cards.length<2)return;
      var host=commonHost(cards,scope)||scope;
      host.classList.add('wpbb-v137-stats-grid');
      kids(host).forEach(function(cell){if(cards.some(function(c){return cell===c||cell.contains(c);})){cell.classList.add('wpbb-v137-grid-cell');}});
      cards.forEach(function(c){c.classList.add('wpbb-v137-stat-card');});
    });
  }

  function repairCommonGrids(){
    var selectors=[
      '.wp-theme-services-section .wpbb-v62-card-grid','.wp-theme-industries-section .wpbb-v62-card-grid',
      '.wp-theme-case-grid','.wp-theme-case-studies-grid','.wp-theme-sector-services','.wp-theme-sector-industries',
      '.wp-theme-sector-cards','.wp-theme-feature-grid','.wp-theme-card-grid'
    ];
    qa('#wp-theme-main '+selectors.join(',#wp-theme-main ')).forEach(function(host){
      if(host.closest('.swiper,.wpbb-swiper--hero,.wp-theme-process-section')||host.classList.contains('swiper-wrapper'))return;
      var direct=kids(host).filter(function(c){return !c.classList.contains('wpbb-v137-hidden')&&(txt(c)||q('img,article,.card',c));});
      if(direct.length<2||direct.length>6)return;
      var cols=direct.length>=4?4:(direct.length===3?3:2);
      host.classList.add('wpbb-v137-grid','wpbb-v137-cols-'+cols);
      direct.forEach(function(c){c.classList.add('wpbb-v137-grid-cell');});
    });

    qa('#wp-theme-main .wp-theme-home-product-catalogue').forEach(function(section){
      var cards=uniq(qa('.wpbb-catalogue-card,.product.type-product,.iws-product-card,.product-card',section));
      if(cards.length<2)return;
      var host=commonHost(cards,section);if(host)markGrid(host,cards,cards.length>=4?4:3);
    });
  }

  function alignSections(){
    qa('#wp-theme-main .wp-theme-section-shell,#wp-theme-main .wpbb-v67-section-shell,#wp-theme-main section').forEach(function(section){
      if(!section.classList.contains('wpbb-v137-hero-shell')&&!section.classList.contains('wpbb-v136-hero-shell'))section.classList.add('wpbb-v137-align-section');
    });
  }

  function cleanupDuplicateHeroLikeSections(){
    var primary=heroBlocks()[0];if(!primary)return;
    qa('#wp-theme-main .wp-theme-sector-hero,#wp-theme-main .wp-theme-hero').forEach(function(section){
      if(!section.contains(primary)&&q('.swiper,.wpbb-swiper--hero',section))hide(section);
    });
  }

  function run(){
    mark();removeMalformedBlockText();repairHero();cleanupDuplicateHeroLikeSections();cleanupDuplicateFinders();cleanupPlaceholderRows();repairProcess();repairStats();repairCommonGrids();alignSections();
  }
  var pending=false;
  function schedule(){if(pending)return;pending=true;W.setTimeout(function(){pending=false;run();},60);}
  if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
  W.addEventListener('load',function(){run();W.setTimeout(run,320);W.setTimeout(run,1100);});
  if(W.MutationObserver)new MutationObserver(function(ms){if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))schedule();}).observe(D.documentElement,{childList:true,subtree:true});
})(window,document);
