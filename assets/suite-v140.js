/* E-Learning 3.8.11.40 - measured grid owner, hero pager and Woo shell repair. */
(function(W,D){
  'use strict';
  var ROOT=D.documentElement;
  function q(sel,root){try{return(root||D).querySelector(sel);}catch(e){return null;}}
  function qa(sel,root){try{return Array.prototype.slice.call((root||D).querySelectorAll(sel));}catch(e){return[];}}
  function kids(el){return el?Array.prototype.slice.call(el.children||[]):[];}
  function txt(el){return String(el&&el.textContent||'').replace(/\s+/g,' ').trim();}
  function norm(v){return String(v||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').replace(/\s+/g,' ').trim();}
  function uniq(arr){return arr.filter(function(x,i,a){return x&&a.indexOf(x)===i;});}
  function topLevel(arr){return uniq(arr).filter(function(item){return !arr.some(function(other){return other!==item&&other.contains&&other.contains(item);});});}
  function ready(fn){if(D.readyState==='loading')D.addEventListener('DOMContentLoaded',fn,{once:true});else fn();}

  function markBody(){if(D.body)D.body.classList.add('wpbb-v140','wpbb-v140-theme-elearning');}

  function measureGrid(){
    var candidates=[q('.wp-theme-header-main > .container'),q('.wp-theme-header-main .container'),q('.wp-theme-site-header .container'),q('.wp-theme-site-footer .container')].filter(Boolean);
    var best=null;
    candidates.some(function(el){var r=el.getBoundingClientRect();if(r.width>320&&r.width<=W.innerWidth+2){best=r;return true;}return false;});
    if(!best)return;
    ROOT.style.setProperty('--wpbb-v140-left',Math.max(16,Math.round(best.left))+'px');
    ROOT.style.setProperty('--wpbb-v140-right',Math.max(16,Math.round(W.innerWidth-best.right))+'px');
  }

  function bestHost(scope,cards){
    cards=topLevel(cards||[]);if(!scope||cards.length<2)return null;
    var candidates=[];
    qa('.row,.wpbb-row,.wpbb-v62-card-grid,.wp-block-post-template,.products,ul.products,.wpbb-lms-course-grid',scope).forEach(function(host){
      var direct=kids(host).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
      if(direct.length>=2)candidates.push({host:host,items:direct,count:direct.length});
    });
    if(candidates.length){candidates.sort(function(a,b){return b.count-a.count;});return candidates[0];}
    var p=cards[0]&&cards[0].parentElement,depth=0;
    while(p&&p!==scope.parentElement&&depth<7){
      var direct=kids(p).filter(function(k){return cards.some(function(c){return k===c||k.contains(c);});});
      if(direct.length>=2)return{host:p,items:direct,count:direct.length};
      p=p.parentElement;depth++;
    }
    return null;
  }
  function markGrid(host,items,cols,extra){
    if(!host||!items||items.length<2)return;
    host.classList.add('wpbb-v140-grid','wpbb-v140-cols-'+Math.max(2,Math.min(4,cols||items.length)));
    if(extra)host.classList.add(extra);
    items.forEach(function(item){item.classList.add('wpbb-v140-grid-cell');});
  }
  function markSection(scopeSel,cardSel,cols,extra,cardExtra){
    qa('#wp-theme-main '+scopeSel).forEach(function(scope){
      var cards=topLevel(qa(cardSel,scope));if(cards.length<2)return;
      var pick=bestHost(scope,cards);if(!pick)return;
      markGrid(pick.host,pick.items,cols,extra);
      if(cardExtra)cards.forEach(function(c){c.classList.add(cardExtra);});
    });
  }

  function markKnownGrids(){
    markSection('.wp-theme-services-section','.wp-theme-sector-card,.wpbb-icon-card',4,'wpbb-v140-services-grid');
    markSection('.wp-theme-industries-section','.wp-theme-sector-card,.wpbb-icon-card',4,'wpbb-v140-industries-grid');
    markSection('.wp-theme-process-section','.wp-theme-process-card,.wp-theme-sector-card,.wpbb-icon-card',3,'wpbb-v140-process-grid','wpbb-v140-process-card');
    markSection('.wp-theme-home-stats,.wp-theme-sector-proof','.wpbb-fun-fact,.wp-theme-sector-proof__item',4,'wpbb-v140-stats-grid');
    markSection('.wpbb-sector-proof-band','.wpbb-sector-proof-card,.wp-theme-sector-card,.wpbb-icon-card',3,'wpbb-v140-proof-grid','wpbb-v140-proof-card');
    markSection('.wp-theme-home-product-catalogue','.wpbb-catalogue-card,.product.type-product,.iws-product-card,.product-card',4,'wpbb-v140-product-grid','wpbb-v140-product-card');
    markSection('.wpbb-courses-finder-section,.wpbb-sector-finder,.wp-theme-sector-finder','.wpbb-lms-course-card,.wpbb-sector-card,.course,.type-course',3,'wpbb-v140-course-grid','wpbb-v140-course-card');
    markSection('.wp-theme-sector-courses,.wpbb-lms-course-grid','.wpbb-lms-course-card,.wpbb-sector-card,.course,.type-course',3,'wpbb-v140-course-grid','wpbb-v140-course-card');
    markSection('.wpbb-lms-dashboard','.wpbb-lms-dashboard-grid > article',3,'wpbb-v140-dashboard-grid','wpbb-v140-dashboard-card');
    markSection('.wp-theme-insights-section,.wp-theme-blog-preview-section','.wp-block-post,article',3,'wpbb-v140-blog-grid');
    markSection('.wp-theme-gallery-section','.wp-theme-gallery-card',4,'wpbb-v140-gallery-grid');
  }

  function finderSignature(finder){
    var form=finder.matches&&finder.matches('form')?finder:q('form',finder)||finder;
    var fields=qa('input,select,textarea,button',form).map(function(el){return[String(el.tagName||'').toLowerCase(),String(el.getAttribute('type')||''),norm(el.getAttribute('name')||''),norm(el.getAttribute('placeholder')||''),norm(txt(el))].join(':');});
    return norm(txt(form))+'|'+fields.join('|');
  }
  function cleanupDuplicateFinders(){
    var main=q('#wp-theme-main');if(!main||!D.body||!D.body.classList.contains('home'))return;
    var finders=qa('.wpbb-v97-hero-finder',main);
    qa('form',main).forEach(function(form){
      var ph=qa('input',form).map(function(i){return norm(i.getAttribute('placeholder')||'');}).join(' ');
      if(ph.indexOf('what do you want to learn')!==-1){var wrap=form.closest('.wpbb-v97-hero-finder')||form;if(finders.indexOf(wrap)<0)finders.push(wrap);}
    });
    var seen={};
    finders.forEach(function(finder){var sig=finderSignature(finder);if(!sig)return;if(seen[sig])finder.classList.add('wpbb-v140-hidden');else seen[sig]=finder;});
  }
  function cleanupPlaceholderRows(){
    var main=q('#wp-theme-main');if(!main)return;
    qa('.wpbb-row,.row,.wpbb-v62-card-grid',main).forEach(function(row){
      if(row.closest('.wp-theme-process-section'))return;
      var cells=kids(row).filter(function(c){return c.nodeType===1&&!c.classList.contains('wpbb-v140-hidden');});
      if(cells.length<2||cells.length>6)return;
      var generic=0,meaningful=0;
      cells.forEach(function(cell){
        var title=q('.wpbb-icon-card__title,.card-title,h2,h3,h4,h5,h6,strong',cell),body=q('.wpbb-icon-card__text,.card-text,p',cell);
        var tn=norm(txt(title)),bn=norm(txt(body));
        if(tn==='card title'&&(bn==='add a short description'||bn==='add a short description '||bn===''))generic++;
        else if(txt(cell)||q('img,svg,form,input,select,button,video,iframe',cell))meaningful++;
      });
      if(generic>=2&&generic===cells.length&&meaningful===0)row.classList.add('wpbb-v140-hidden');
    });
  }

  function heroBlocks(){return qa('#wp-theme-main .wpbb-swiper--hero');}
  function swiperEl(block){return block&&(block.matches&&block.matches('.swiper')?block:q('.swiper',block));}
  function uniqueSlides(el){
    if(!el)return[];var slides=qa('.swiper-wrapper > .swiper-slide',el);if(!slides.length)slides=qa('.swiper-slide',el);
    var seen={},out=[];slides.forEach(function(slide,index){if(slide.classList.contains('swiper-slide-duplicate'))return;var raw=slide.getAttribute('data-swiper-slide-index'),key=(raw===null||raw==='')?'dom-'+index:String(raw);if(seen[key])return;seen[key]=1;out.push(slide);});
    return out.length?out:slides;
  }
  function activeIndex(sw,count){var n=sw&&typeof sw.realIndex==='number'?sw.realIndex:(sw&&typeof sw.activeIndex==='number'?sw.activeIndex:0);n=parseInt(n,10);if(!isFinite(n)||n<0)n=0;return count?n%count:0;}
  function paintPager(pager,sw,count){var active=activeIndex(sw,count);qa('.wpbb-v140-hero-pagination__bullet',pager).forEach(function(button,index){var on=index===active;button.classList.toggle('is-active',on);if(on)button.setAttribute('aria-current','true');else button.removeAttribute('aria-current');});}
  function ensurePager(block){
    var el=swiperEl(block);if(!el)return;var count=uniqueSlides(el).length;if(count<2)return;
    var pager=q(':scope > .wpbb-v140-hero-pagination',el);
    if(!pager){pager=D.createElement('div');pager.className='wpbb-v140-hero-pagination';pager.setAttribute('role','group');pager.setAttribute('aria-label','Hero slides');el.appendChild(pager);}
    if(pager.children.length!==count){pager.innerHTML='';for(var i=0;i<count;i++){var b=D.createElement('button');b.type='button';b.className='wpbb-v140-hero-pagination__bullet';b.setAttribute('data-wpbb-v140-slide',String(i));b.setAttribute('aria-label','Go to hero slide '+(i+1)+' of '+count);pager.appendChild(b);}}
    if(!pager.dataset.wpbbV140Bound){pager.dataset.wpbbV140Bound='1';pager.addEventListener('click',function(event){var button=event.target&&event.target.closest?event.target.closest('[data-wpbb-v140-slide]'):null;if(!button)return;var index=parseInt(button.getAttribute('data-wpbb-v140-slide'),10)||0,sw=(el&&el.swiper)||(block&&block.swiper)||null;if(sw){try{if(typeof sw.slideToLoop==='function')sw.slideToLoop(index);else if(typeof sw.slideTo==='function')sw.slideTo(index);}catch(e){}}paintPager(pager,sw,count);});}
    var sw=(el&&el.swiper)||(block&&block.swiper)||null;
    if(sw&&pager._wpbbV140Swiper!==sw){pager._wpbbV140Swiper=sw;if(typeof sw.on==='function'){var update=function(){paintPager(pager,sw,count);};try{sw.on('slideChange',update);sw.on('realIndexChange',update);sw.on('transitionEnd',update);}catch(e){}}}
    paintPager(pager,sw,count);
  }

  function menuTrigger(menu){var li=menu&&menu.parentElement;if(!li)return null;try{return li.querySelector(':scope > a, :scope > button, :scope > .wp-theme-nav-link')||li;}catch(e){return li.querySelector('a,button')||li;}}
  function positionMega(menu){
    if(!menu)return;if(!W.matchMedia('(min-width: 992px)').matches){menu.style.removeProperty('top');return;}
    var trigger=menuTrigger(menu);if(!trigger||!trigger.getBoundingClientRect)return;var r=trigger.getBoundingClientRect(),li=menu.parentElement,lr=li&&li.getBoundingClientRect?li.getBoundingClientRect():r,bottom=Math.ceil(Math.max(r.bottom,lr.bottom)-6);
    if(bottom>0){menu.style.setProperty('top',bottom+'px','important');ROOT.style.setProperty('--wpbb-v140-mega-top',bottom+'px');}
  }
  function bindMegas(){qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(function(menu){if(!menu.dataset.wpbbV140Bound){menu.dataset.wpbbV140Bound='1';var li=menu.parentElement;if(li){li.addEventListener('pointerenter',function(){positionMega(menu);},{passive:true});li.addEventListener('focusin',function(){positionMega(menu);});}}positionMega(menu);});}

  function commonParent(a,b){if(!a||!b)return null;var p=a.parentElement,depth=0;while(p&&depth<7){if(p.contains(b))return p;p=p.parentElement;depth++;}return null;}
  function repairWoo(){
    var main=q('#wp-theme-main');if(!main)return;
    if(main.classList.contains('wp-theme-woo-legacy--catalog'))qa('ul.products,.products',main).forEach(function(g){if(qa(':scope > li.product',g).length>1)g.classList.add('wpbb-v140-shop-grid');});
    if(main.classList.contains('wp-theme-woo-legacy--cart')){
      var form=q('.woocommerce-cart-form',main),tot=q('.cart-collaterals',main);if(form&&tot){var host=commonParent(form,tot);if(host)host.classList.add('wpbb-v140-cart-grid');}
    }
    if(main.classList.contains('wp-theme-woo-legacy--account')){
      var wrap=q('.woocommerce',main);if(wrap&&q('.woocommerce-MyAccount-navigation',wrap)&&q('.woocommerce-MyAccount-content',wrap))wrap.classList.add('wpbb-v140-account-grid');
      var slugs=['orders','downloads','edit-address','edit-account','payment-methods','lost-password','view-order','add-payment-method','delete-payment-method','set-default-payment-method','customer-logout'];
      qa('a[href]',main).forEach(function(link){try{var url=new URL(link.href,W.location.href);if(url.origin!==W.location.origin)return;var parts=url.pathname.replace(/^\/+|\/+$/g,'').split('/').filter(Boolean);if(parts.length!==1||slugs.indexOf(parts[0])===-1)return;url.pathname='/my-account/'+parts[0]+'/';link.href=url.toString();}catch(e){}});
    }
  }

  function run(){markBody();measureGrid();cleanupDuplicateFinders();cleanupPlaceholderRows();markKnownGrids();heroBlocks().forEach(ensurePager);bindMegas();repairWoo();}
  var timer=0;function schedule(){clearTimeout(timer);timer=W.setTimeout(run,50);}
  ready(function(){run();[150,500,1200].forEach(function(ms){W.setTimeout(run,ms);});});
  W.addEventListener('load',function(){run();W.setTimeout(run,300);});
  W.addEventListener('resize',function(){clearTimeout(timer);timer=W.setTimeout(run,110);},{passive:true});
  W.addEventListener('scroll',function(){qa('.wp-theme-primary-menu>li>.wp-theme-mega-menu').forEach(positionMega);},{passive:true});
  if(W.MutationObserver){var observer=new MutationObserver(function(ms){if(ms.some(function(m){return m.addedNodes&&m.addedNodes.length;}))schedule();});observer.observe(D.documentElement,{childList:true,subtree:true});W.setTimeout(function(){observer.disconnect();},5200);}
})(window,document);
