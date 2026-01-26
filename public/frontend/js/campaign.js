// Global variables
let currentTestimonial = 0;
const testimonials = document.querySelectorAll('.testimonial-card');
const dots = document.querySelectorAll('.dot');
let isAutoPlaying = true;
let autoPlayInterval;

// DOM elements
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const contactForm = document.getElementById('contactForm');
const mobileMenu = document.getElementById('mobileMenu');
const navLinks = document.getElementById('navLinks');

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    initTestimonials();
    initNavigation();
   // initContactForm();
    //initSmoothScrolling();
    // initAnimations();
    // initAdditionalFeatures();
});

// Testimonials functionality
function initTestimonials() {
    if (testimonials.length === 0) return;
    
    showTestimonial(0);
    startAutoPlay();
    
    // Navigation buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopAutoPlay();
            previousTestimonial();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopAutoPlay();
            nextTestimonial();
        });
    }
    
    // Dots navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopAutoPlay();
            showTestimonial(index);
        });
    });
    
    // Pause auto-play on hover
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        testimonialSlider.addEventListener('mouseenter', stopAutoPlay);
        testimonialSlider.addEventListener('mouseleave', startAutoPlay);
    }
}

function showTestimonial(index) {
    testimonials.forEach(testimonial => testimonial.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (testimonials[index]) testimonials[index].classList.add('active');
    if (dots[index]) dots[index].classList.add('active');
    
    currentTestimonial = index;
}

function nextTestimonial() {
    const next = (currentTestimonial + 1) % testimonials.length;
    showTestimonial(next);
}

function previousTestimonial() {
    const prev = currentTestimonial === 0 ? testimonials.length - 1 : currentTestimonial - 1;
    showTestimonial(prev);
}

function startAutoPlay() {
    if (isAutoPlaying && testimonials.length > 1) {
        autoPlayInterval = setInterval(nextTestimonial, 5000);
    }
}

function stopAutoPlay() {
    isAutoPlaying = false;
    if (autoPlayInterval) clearInterval(autoPlayInterval);
}

// Navigation functionality
function initNavigation() {
    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }
    
    const navLinkItems = document.querySelectorAll('.nav-links a');
    navLinkItems.forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks) navLinks.classList.remove('active');
        });
    });
    
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.style.background = window.scrollY > 50
                ? 'rgba(255, 255, 255, 0.95)'
                : 'rgba(255, 255, 255, 0.9)';
        }
    });
}

// Timer functionality
let timeLeft = 10;
const countdownElement = document.getElementById('countdown');
const progressBar = document.getElementById('progressBar');
const container = document.querySelector('.thank-you-container');

function updateTimer() {
    if (!countdownElement || !progressBar || !container) return;

    countdownElement.textContent = timeLeft;
    const progressPercentage = (timeLeft / 10) * 100;
    progressBar.style.width = progressPercentage + '%';

    countdownElement.style.transform = 'scale(1.1)';
    setTimeout(() => countdownElement.style.transform = 'scale(1)', 200);

    if (timeLeft <= 0) {
        fadeOutAndRedirect();
        return;
    }

    timeLeft--;
    setTimeout(updateTimer, 1000);
}

function fadeOutAndRedirect() {
    container.classList.add('fade-out');
    setTimeout(() => {
        window.location.href = '/';
    }, 500);
}

function skipTimer() {
    timeLeft = 0;
    fadeOutAndRedirect();
}

function goHome() {
    container.classList.add('fade-out');
    setTimeout(() => {
        window.location.href = '#home';
        alert('Redirecting to home page...');
    }, 500);
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(updateTimer, 1000);

    document.addEventListener('mousemove', function(e) {
        const particles = document.querySelectorAll('.particle');
        particles.forEach((particle, index) => {
            const speed = (index + 1) * 0.01;
            const x = e.clientX * speed;
            const y = e.clientY * speed;
            particle.style.transform = `translate(${x}px, ${y}px)`;
        });
    });
});

// Add click ripple effect to buttons
document.querySelectorAll('.btn-custom').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.6)';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple-effect 0.6s linear';
        ripple.style.pointerEvents = 'none';
        
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// ✅ Renamed style variables to prevent redeclaration errors
const rippleEffectStyle = document.createElement('style');
rippleEffectStyle.textContent = `
    @keyframes ripple-effect {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleEffectStyle);

// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Scroll reveal animation
function revealOnScroll() {
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('active');
        }
    });
}
window.addEventListener('scroll', revealOnScroll);
revealOnScroll();

// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.stat-item h3');
    const speed = 200;
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const count = parseInt(counter.innerText);
        const increment = target / speed;
        if (count < target) {
            counter.innerText = Math.ceil(count + increment);
            setTimeout(animateCounters, 1);
        } else {
            counter.innerText = target;
        }
    });
}

const statsObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats');
if (statsSection) statsObserver.observe(statsSection);

// Phone card animation
function animateCard(card) {
    card.style.transform = 'scale(0.95)';
    card.style.transition = 'all 0.1s ease';
    setTimeout(() => card.style.transform = 'scale(1.05)', 100);
    setTimeout(() => card.style.transform = 'scale(1)', 200);

    const ripple = document.createElement('div');
    ripple.style.position = 'absolute';
    ripple.style.width = '100px';
    ripple.style.height = '100px';
    ripple.style.background = 'rgba(78, 205, 196, 0.3)';
    ripple.style.borderRadius = '50%';
    ripple.style.transform = 'scale(0)';
    ripple.style.animation = 'ripple 0.6s linear';
    ripple.style.pointerEvents = 'none';
    card.style.position = 'relative';
    card.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
}

// ✅ Renamed ripple style to avoid duplicate declaration
const phoneRippleStyle = document.createElement('style');
phoneRippleStyle.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(phoneRippleStyle);

// CTA functions
function startTrial(event) {
    event.target.style.transform = 'scale(0.95)';
    setTimeout(() => {
        event.target.style.transform = 'scale(1)';
        alert('🎉 Amazing! Your free 30-day trial is starting.');
    }, 150);
}

function scheduleDemo(event) {
    event.target.style.transform = 'scale(0.95)';
    setTimeout(() => {
        event.target.style.transform = 'scale(1)';
        alert('📅 Our experts will contact you within 2 hours.');
    }, 150);
}

function requestDemo(event) {
    event.target.style.transform = 'scale(0.95)';
    setTimeout(() => {
        event.target.style.transform = 'scale(1)';
        alert('🚀 Redirecting you to the interactive demo...');
    }, 150);
}

// Parallax hero
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    if (hero) hero.style.transform = `translate3d(0, ${scrolled * -0.5}px, 0)`;
});

// Reservation card active effect
document.querySelectorAll('.reservation-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.reservation-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        this.style.transform = 'translateX(15px) scale(1.02)';
        setTimeout(() => this.style.transform = 'translateX(10px)', 200);
    });
});

const activeStyles = document.createElement('style');
activeStyles.textContent = `
    .reservation-card.active {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
        border-left-width: 6px !important;
    }
`;
document.head.appendChild(activeStyles);

window.addEventListener('load', () => {
    document.querySelectorAll('.feature-item').forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('reveal');
    });
    setTimeout(revealOnScroll, 100);
});
