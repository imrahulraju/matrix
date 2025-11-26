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
    type: 'loop',
    perPage: 10,
    perMove: 1,
    autoplay: true,
    interval: 2000,
    arrows: false,
    pagination: false,
    gap: '1rem',
    pauseOnHover: false,
    pauseOnFocus: false,
    autoWidth: true,
    breakpoints: {
      1024: { perPage: 5 },
      768: { perPage: 3 },
      480: { perPage: 3 },
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


// Modal

document.addEventListener('DOMContentLoaded', () => {
  // Function to close the modal and pause the video
  const closeModal = (modalElement) => {
    if (!modalElement) return;

    modalElement.classList.remove('is-open');
    modalElement.setAttribute('aria-hidden', 'true');

    // Find the iframe inside the closed modal
    const iframe = modalElement.querySelector('.video-container iframe');

    if (iframe) {
      // Reset the iframe's source URL to stop playback
      const src = iframe.src;
      // This stops the video without having to call the YouTube IFrame API
      iframe.src = src.replace('?autoplay=1', '?autoplay=0');

      // To be safe, if you didn't include ?autoplay=1 in the HTML initially, 
      // the simplest way is to set src to itself, which reloads it and stops playback:
      // iframe.src = src; 
    }
  };

  // 1. Handle Button Clicks (Open Modal)
  document.querySelectorAll('.mx-modal-trigger').forEach(button => {
    button.addEventListener('click', () => {
      const targetSelector = button.getAttribute('data-modal-target');
      const targetModal = document.querySelector(targetSelector);

      if (targetModal) {
        targetModal.classList.add('is-open');
        targetModal.setAttribute('aria-hidden', 'false');

        // Optional: Automatically start playback when opened
        const iframe = targetModal.querySelector('.video-container iframe');
        if (iframe) {
          iframe.src = iframe.src.replace('?autoplay=0', '?autoplay=1');
        }
      }
    });
  });

  // 2. Handle Close Actions (Close Button and Overlay Click)
  document.querySelectorAll('[data-modal-close]').forEach(closer => {
    closer.addEventListener('click', (event) => {
      // Traverse up the DOM to find the parent .mx-modal element
      const modalToClose = event.target.closest('.mx-modal');
      closeModal(modalToClose);
    });
  });

  // 3. Handle ESC Key Press (Close Modal)
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      // Find the currently open modal
      const openModal = document.querySelector('.mx-modal.is-open');
      closeModal(openModal);
    }
  });
});


