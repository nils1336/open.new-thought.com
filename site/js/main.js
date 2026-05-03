// Modal helpers
  function openModal(id){
    const m = document.getElementById(id);
    m.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(id){
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
  }
  function closeModalOnBg(e, id){
    if(e.target === e.currentTarget) closeModal(id);
  }
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') document.querySelectorAll('.modal-overlay.open').forEach(m=>{ m.classList.remove('open'); document.body.style.overflow=''; }); });

  // Hamburger menu
  const hamburger = document.getElementById('hamburger');
  const navMobile = document.getElementById('nav-mobile');
  hamburger.addEventListener('click', ()=>{
    const open = navMobile.classList.toggle('open');
    hamburger.classList.toggle('open', open);
    hamburger.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
  });
  document.querySelectorAll('#nav-mobile a').forEach(a=>{
    a.addEventListener('click', ()=>{
      navMobile.classList.remove('open');
      hamburger.classList.remove('open');
    });
  });

  // Anchor scroll: always offset by actual nav height
  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', e=>{
      const id = a.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if(!target) return;
      e.preventDefault();
      const navH = document.querySelector('nav.top').getBoundingClientRect().height;
      const top = target.getBoundingClientRect().top + window.scrollY - navH;
      window.scrollTo({top, behavior:'smooth'});
    });
  });

  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target);}});
  },{threshold:0.1, rootMargin:"0px 0px -40px 0px"});
  document.querySelectorAll('.reveal, .stag').forEach(el=>io.observe(el));

  const $ = (id)=>document.getElementById(id);
  const fmt = (n)=>Math.round(n).toLocaleString('de-DE');
  const state = new WeakMap();
  function animateNum(el, target){
    const from = state.get(el) ?? 0;
    const start = performance.now();
    const dur = 600;
    const ease = t=>1-Math.pow(1-t,3);
    function step(now){
      const t = Math.min(1, (now-start)/dur);
      const v = from + (target-from)*ease(t);
      el.textContent = fmt(v);
      if(t<1) requestAnimationFrame(step);
      else { state.set(el, target); el.textContent = fmt(target);}
    }
    requestAnimationFrame(step);
  }
  function calc(){
    const employees = +$('employees').value;
    const cost = +$('cost').value;
    const initiatives = +$('initiatives').value;
    const hours = +$('hours').value;
    $('vEmp').textContent = employees;
    $('vCost').textContent = '€ ' + cost.toLocaleString('de-DE');
    $('vInit').textContent = initiatives;
    $('vHrs').textContent = hours.toString().replace('.',',') + ' h';
    const hourly = cost/1800;
    const lostTime = employees*hours*46*hourly*0.30;
    const lostPilots = initiatives*0.65*(80*hourly+2000);
    const aiPC = employees*hours*46*hourly;
    const lostCompound = aiPC*0.12;
    const lostTotal = lostTime+lostPilots+lostCompound;
    const savings = lostTotal*0.60;
    animateNum($('savings'), savings);
    animateNum($('lostTime'), lostTime);
    animateNum($('lostPilots'), lostPilots);
    animateNum($('lostCompound'), lostCompound);
    animateNum($('lostTotal'), lostTotal);
    $('recoveryPct').textContent = '60 %';
    $('recoveryFill').style.width = '60%';
  }
  ['employees','cost','initiatives','hours'].forEach(id=>$(id).addEventListener('input', calc));
  calc();

  // Scroll-to-top button
  const scrollTopBtn = document.getElementById('scrollTop');
  window.addEventListener('scroll', ()=>{
    scrollTopBtn.classList.toggle('visible', window.scrollY > 500);
  }, {passive:true});
  scrollTopBtn.addEventListener('click', ()=> window.scrollTo({top:0, behavior:'smooth'}));

  // Active nav scroll spy
  const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');
  const sections = Array.from(navLinks).map(a=>{
    const id = a.getAttribute('href').slice(1);
    return {a, el: document.getElementById(id)};
  }).filter(s=>s.el);
  const navH = document.querySelector('nav.top').getBoundingClientRect().height;
  function updateActiveNav(){
    const scrollY = window.scrollY + navH + 32;
    let current = sections[0];
    for(const s of sections){
      if(s.el.getBoundingClientRect().top + window.scrollY <= scrollY) current = s;
    }
    navLinks.forEach(a=>a.classList.remove('active'));
    if(current) current.a.classList.add('active');
  }
  window.addEventListener('scroll', updateActiveNav, {passive:true});
  updateActiveNav();

  // Count-up for stat numbers (hero-meta + proof cells)
  const countEls = document.querySelectorAll('.hero-meta .v, .proof-cell .v');
  const countIO = new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
      if(!entry.isIntersecting) return;
      countIO.unobserve(entry.target);
      const el = entry.target;
      const unit = el.querySelector('.u');
      const unitHTML = unit ? unit.outerHTML : '';
      const raw = el.textContent.trim();
      const num = parseFloat(raw.replace(/[^0-9.]/g,''));
      if(isNaN(num)) return;
      const dur = 900, start = performance.now();
      const ease = t => 1 - Math.pow(1-t, 3);
      function step(now){
        const t = Math.min(1,(now-start)/dur);
        const v = num * ease(t);
        const disp = Number.isInteger(num) ? Math.round(v) : v.toFixed(1);
        el.innerHTML = disp + unitHTML;
        if(t<1) requestAnimationFrame(step);
        else el.innerHTML = num + unitHTML;
      }
      requestAnimationFrame(step);
    });
  },{threshold:0.4});

  // Venn diagram — global so inline onclick works
  var _vennActive = null;
  function vennActivate(id){
    var panelIds = { mensch:'vpanel-mensch', komm:'vpanel-komm', system:'vpanel-system' };
    var svgClasses = { mensch:'active-mensch', komm:'active-komm', system:'active-system' };
    var svg = document.querySelector('.venn-svg');

    var panels = document.querySelector('.venn-panels');

    // hide all panels & reset svg classes
    ['vpanel-mensch','vpanel-komm','vpanel-system'].forEach(function(pid){
      var p = document.getElementById(pid);
      if(p) p.style.display = 'none';
    });
    if(svg) svg.classList.remove('active-mensch','active-komm','active-system','has-active');
    if(panels) panels.classList.remove('has-active');

    // toggle off on re-click
    if(_vennActive === id){ _vennActive = null; return; }
    _vennActive = id;

    if(svg){
      svg.classList.add('has-active');
      svg.classList.add(svgClasses[id]);
    }

    if(panels) panels.classList.add('has-active');
    var panel = document.getElementById(panelIds[id]);
    if(panel) panel.style.display = 'flex';

    if(id === 'system'){
      setTimeout(function(){
        var os = document.getElementById('os');
        var nav = document.querySelector('nav.top');
        var navH = nav ? nav.getBoundingClientRect().height : 0;
        window.scrollTo({top: os.getBoundingClientRect().top + window.scrollY - navH, behavior:'smooth'});
      }, 300);
    }
  }
  countEls.forEach(el=>countIO.observe(el));