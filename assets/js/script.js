// mx-header.js - Class-based toggle for mobile menu
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.mx-header__menu-toggle');
    const nav = document.querySelector('.mx-header__nav');
  
    if (!menuToggle || !nav) return;
  
    // Toggle visible class on click
    menuToggle.addEventListener('click', (e) => {
      e.preventDefault();
      nav.classList.toggle('mx-header__nav--visible');
      menuToggle.classList.toggle('mx-header__menu-toggle--active');
    });
  
    // Optional: Close menu when clicking a nav link
    const navLinks = document.querySelectorAll('.mx-header__nav-item');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('mx-header__nav--visible');
        menuToggle.classList.remove('mx-header__menu-toggle--active');
      });
    });
  });

  //tab
  class Tabs {
    constructor(groupId) {
      this.groupId = groupId;
      this.tabBtns = document.querySelectorAll(
        `.mx-tab[data-tab-group="${groupId}"] .mx-tab__btns-item`
      );
      this.contents = document.querySelectorAll(
        `.mx-tab-content[data-tab-group="${groupId}"] .mx-tab-content__item`
      );
      this.init();
    }
  
    init() {
      this.tabBtns.forEach(btn => {
        btn.addEventListener("click", () => {
          const target = btn.getAttribute("data-tab-btn");
  
          // Remove active classes
          this.tabBtns.forEach(b => b.classList.remove("active"));
          this.contents.forEach(c => c.classList.remove("active"));
  
          // Add active class to clicked tab + related content
          btn.classList.add("active");
          const content = document.querySelector(
            `.mx-tab-content[data-tab-group="${this.groupId}"] .mx-tab-content__item[data-content-btn="${target}"]`
          );
          if (content) content.classList.add("active");
        });
      });
  
      // Default: open first tab
      if (this.tabBtns.length > 0) this.tabBtns[0].click();
    }
  }
  
  // ✅ Only loop through `.mx-tab`, not every `[data-tab-group]`
  document.querySelectorAll(".mx-tab").forEach(tab => {
    const groupId = tab.getAttribute("data-tab-group");
    new Tabs(groupId);
  });



//   Splide

document.addEventListener("DOMContentLoaded", function () {
    new Splide("#mx-review-slide", {
        type: "loop",
        perPage: 3,
        perMove: 1,
        gap: "1rem",
        arrows: false,   // hide arrows
        pagination: true, // show dots
        breakpoints: {
        768: {
          perPage: 1,
        },
      },
    }).mount();
  });
  


  document.addEventListener('DOMContentLoaded', function () {
    new Splide('#clientSplide', {
      type       : 'loop',
      perPage    : 10,
      perMove    : 1,
      autoplay   : true,
      interval   : 2000,
      arrows     : false,
      pagination : false,
      gap        : '1rem',
      pauseOnHover: false,
      pauseOnFocus: false,
      autoWidth: true,
      breakpoints: {
        1024: { perPage: 5 },
        768 : { perPage: 3 },
        480 : { perPage: 3 },
      },
    }).mount();
  });




  document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector(".mx-header");
  
    window.addEventListener("scroll", function () {
      if (window.scrollY > 0) {
        header.classList.add("is-scrolling");
      } else {
        header.classList.remove("is-scrolling");
      }
    });
  });



  // animate

  document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll('[data-animate="true"]');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const delay = el.getAttribute("data-animate-speed") || 0;
          const duration = el.getAttribute("data-animate-duration") || 1;

          el.style.transitionDuration = `${duration}s`;
          el.style.transitionDelay = `${delay}s`;
          el.classList.add("visible");

          // animate once
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.2 });

    items.forEach(el => observer.observe(el));
  });

  // counter
  document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".mx-counter__item h4");
  
    const animateCounter = (el) => {
      const target = +el.getAttribute("data-count");
      const span = el.querySelector("span");
      let count = 0;
      const duration = 2000; // total animation time in ms
      const step = Math.ceil(target / (duration / 20));
  
      const interval = setInterval(() => {
        count += step;
        if (count >= target) {
          count = target;
          clearInterval(interval);
        }
        span.textContent = count;
      }, 20);
    };
  
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          obs.unobserve(entry.target); // run only once
        }
      });
    }, { threshold: 0.3 });
  
    counters.forEach((counter) => observer.observe(counter));
  });
  
  
  // IntersectionObserver
  const counterSection = document.querySelector('.mx-counter');
  const counters = document.querySelectorAll('.mx-counter h4');
  
  let counterStarted = false;
  
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting && !counterStarted) {
      counterStarted = true;
      counters.forEach(counter => {
        let target = parseInt(counter.getAttribute('data-count'));
        animateCounter(counter, target);
      });
    }
  }, { threshold: 0.4 });
  
  observer.observe(counterSection);


  // smooth scroll
    document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');
    
        links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const targetEl = document.getElementById(targetId);
    
            if (targetEl) {
            window.scrollTo({
                top: targetEl.offsetTop - 60, // Adjust for fixed header height
                behavior: 'smooth'
            });
            }
        });
        });
    });


// image scroll animation
    document.addEventListener("DOMContentLoaded", () => {
      const containers = document.querySelectorAll(".mx-why-Matrix__animation");

      containers.forEach(container => {
        const item = container.querySelector(".mx-why-Matrix__animation-item");
        const img = item.querySelector("img");

        let speed = parseFloat(container.dataset.speed) || 30; // px per sec
        let loop = container.dataset.loop || "infinite";
        let direction = "down";
        let currentLoop = 0;
        let startTime = null;
        let containerHeight, imageHeight, maxScroll;

        const start = () => {
          containerHeight = container.clientHeight;
          imageHeight = img.clientHeight;
          maxScroll = imageHeight - containerHeight;
          startTime = null;
          requestAnimationFrame(animate);
        };

        const animate = (timestamp) => {
          if (!startTime) startTime = timestamp;
          const elapsed = (timestamp - startTime) / 1000;
          let distance = speed * elapsed;

          if (direction === "down") {
            item.style.transform = `translateY(-${Math.min(distance, maxScroll)}px)`;
            if (distance >= maxScroll) {
              direction = "up";
              startTime = timestamp;
            }
          } else {
            item.style.transform = `translateY(-${maxScroll - Math.min(distance, maxScroll)}px)`;
            if (distance >= maxScroll) {
              direction = "down";
              startTime = timestamp;
              currentLoop++;
              if (loop !== "infinite" && currentLoop >= Number(loop)) return;
            }
          }

          requestAnimationFrame(animate);
        };

        img.onload = start;
        if (img.complete) start();
      });
    });


// scroll section image change
    const cards = document.querySelectorAll('.mx-card--list');
    const images = document.querySelectorAll('.mx-solution__image--item');

    window.addEventListener('scroll', () => {
      cards.forEach((card, index) => {
        const rect = card.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        // When card enters the visible area (around 1/3 of screen)
        if (rect.top < windowHeight * 0.7 && rect.bottom > windowHeight * 0.3) {
          // Activate this card and image
          cards.forEach(c => c.classList.remove('active'));
          images.forEach(i => i.classList.remove('active'));
          card.classList.add('active');
          if (images[index]) images[index].classList.add('active');
        }
      });
    });


    // popup


    /*
  MxPopupManager
  - All markup uses classes/data attributes prefixed with `mx-`
  - Multiple popups supported. Trigger buttons use data-mx-target="popupId".
  - When a popup is closed, the iframe.src is cleared (unloaded) to stop the video.
  - When opened, the original src is restored.
*/
class MxPopupManager {
  constructor() {
    // find all popups
    this.popups = new Map(); // id => {root, iframe, originalSrc}
    this._collectPopups();
    this._bindGlobalEvents();
  }

  _collectPopups() {
    const popupEls = document.querySelectorAll('[data-mx-popup]');
    popupEls.forEach(root => {
      const id = root.id;
      if (!id) return console.warn('mx-popup missing id:', root);
      const iframe = root.querySelector('[data-mx-iframe]');
      const orig = iframe ? iframe.getAttribute('src') : null;
      // For initial load we keep the src; but we will clear src on close to stop playback
      this.popups.set(id, { root, iframe, originalSrc: orig });
      // wire up close controls inside this popup
      root.addEventListener('click', (e) => {
        if (e.target.closest('[data-mx-close]')) {
          this.close(id);
        }
      });
    });

    // wire triggers
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-mx-target]');
      if (!trigger) return;
      const target = trigger.getAttribute('data-mx-target');
      if (!target) return;
      this.open(target);
    });
  }

  _bindGlobalEvents() {
    // ESC to close topmost open popup
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const open = [...this.popups.values()].find(p => p.root.classList.contains('mx-popup--open'));
        if (open) {
          this.close(open.root.id);
        }
      }
    });
    // trap focus lightly: when popup opens, focus its content
    // (For a full focus trap you'd need more code; this is a minimal friendly approach)
  }

  open(id) {
    const info = this.popups.get(id);
    if (!info) { console.warn('mx-popup target not found:', id); return; }
    const { root, iframe, originalSrc } = info;
    // restore iframe src if it exists and is empty
    if (iframe && (!iframe.getAttribute('src') || iframe.getAttribute('src') === '')) {
      iframe.setAttribute('src', originalSrc);
    }
    root.classList.add('mx-popup--open');
    // small delay then focus for accessibility
    setTimeout(() => {
      const content = root.querySelector('.mx-popup__content');
      if (content) content.focus();
    }, 120);
    // disable page scroll
    document.documentElement.style.overflow = 'hidden';
  }

  close(id) {
    const info = this.popups.get(id);
    if (!info) return;
    const { root, iframe } = info;
    root.classList.remove('mx-popup--open');

    // Unload the iframe to stop playback and free resources
    if (iframe) {
      // store current src in originalSrc if it was changed dynamically
      // then clear src
      iframe.setAttribute('data-mx-prev-src', iframe.getAttribute('src') || '');
      iframe.setAttribute('src', '');
    }

    // restore page scroll only if no other popups are open
    const anyOpen = [...this.popups.values()].some(p => p.root.classList.contains('mx-popup--open'));
    if (!anyOpen) {
      document.documentElement.style.overflow = '';
    }
  }

  // optional helper: toggle
  toggle(id) {
    const info = this.popups.get(id);
    if (!info) return;
    if (info.root.classList.contains('mx-popup--open')) this.close(id);
    else this.open(id);
  }
}


