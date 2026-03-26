<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('meta_title', 'Tifliso Restaurant | Authentic Georgian Restaurant in Budapest')</title>
    <meta name="description" content="@yield('meta_description', 'Visit Tifliso Restaurant in Budapest for authentic Georgian cuisine, khachapuri, khinkali, BBQ, homemade Georgian bread, and premium Georgian wines. Experience true Georgian hospitality in the heart of Budapest.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Georgian restaurant Budapest, Tifliso Restaurant, Georgian food Budapest, khachapuri Budapest, khinkali Budapest, Georgian wine Budapest, authentic Georgian cuisine Hungary, Georgian BBQ Budapest, best Georgian restaurant Budapest')">
    <meta name="author" content="Tifliso Restaurant">
    <meta name="google-site-verification" content="gcoF8ElmNIQAoeZDmq0Slm1XVLje3crvpBSdGI4sXt0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="https://tifliszo.hu/" />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" />

    <link
        rel="preload"
        as="style"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
        onload="this.onload=null;this.rel='stylesheet'" />
    <noscript>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
            rel="stylesheet" />
    </noscript>
    <link href="{{ asset('frontend/css/style.css') }}?v={{ filemtime(public_path('frontend/css/style.css')) }}" rel="stylesheet" />

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-MQ62376790"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-MQ62376790');
    </script>
</head>

<body>
    @yield('content')

    <footer class="site-footer">
        <div class="container">
            <div class="row gy-5">
                <!-- Brand -->
                <div class="col-lg-4">
                    <img
                        src="{{ asset('frontend/images/logo_wt.svg') }}"
                        class="footer-logo"
                        alt="Tifliso" />
                    <p class="footer-desc">
                        Tifliso Restaurant is a traditional Georgian restaurant in Budapest, Hungary, offering authentic Georgian food, khachapuri, Georgian bread, BBQ, fine dining, and premium Georgian wines. Visit us at Ráday utca 11 for a true Georgian dining experience.
                    </p>
                </div>

                <!-- Menu -->
                <div class="col-lg-2 col-md-4">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ url('home') }}">Home</a></li>
                        <li><a href="{{ url('about') }}">About</a></li>
                        <li><a href="{{ url('general-data-protection-regulation') }}">GDPR</a></li>
                        <li><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ url('terms-of-service') }}">Terms of Service</a></li>
                        <li><a href="{{ url('refund-policy') }}">Cancellation Policy</a></li>
                    </ul>
                </div>

                <!-- Opening Hours -->
                <div class="col-lg-3 col-md-4">
                    <h6 class="footer-title">Opening Hours</h6>

                    <ul class="footer-hours">
                        <li><span>Restaurant:</span> Mon – Sun: 11:00 – 23:00</li>
                        <li><span>Kitchen:</span> Mon – Sun: 11:30 – 22:30</li>
                    </ul>

                    <h6 class="footer-title mt-3">Contact</h6>
                    <ul class="footer-contact">
                        <li>Phone: <a href="tel:+36205811111"> +36 20 581 1111</a></a></li>
                        <li>WhatsApp: <a
                                href="https://wa.me/+36205811111?text=Hi, You’ve reached Restaurant Tifliszo. How can we help?"
                                target="_blank">+36 20 581 1111</a></li>
                    </ul>
                </div>

                <!-- Address + Map -->
                <div class="col-lg-3 col-md-4">
                    <h6 class="footer-title">Find Us</h6>

                    <p class="footer-address">
                        Tifliso Restaurant<br />
                        Budapest, Hungary<br />
                        Budapest, Ráday utca 11
                    </p>

                    <div class="footer-map">
                        <div class="map-placeholder"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2696.126069528447!2d19.060309477019246!3d47.48745737117912!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4741dc5a706ea7dd%3A0xc3901e0da9414918!2sBudapest%2C%20R%C3%A1day%20u.%2011%2C%201092!5e0!3m2!1sen!2shu!4v1713966036288!5m2!1sen!2shu" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p>
                    © <span id="year"></span> Tifliso Restaurant. All Rights Reserved.
                </p>
                <p>
                    Designed & Developed by
                    <a
                        href="https://wa.me/+918799707771?text=Hi%20I%20am%20looking%20for%20website%20development"
                        target="_blank">IPulse Web Solutions</a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        function initCustomReservationSelects(root = document) {
            const selects = root.querySelectorAll('.reservation-form select.form-select:not([data-customized="true"])');

            selects.forEach((select, index) => {
                select.dataset.customized = 'true';
                select.classList.add('is-custom-select-native');

                const wrapper = document.createElement('div');
                wrapper.className = 'reservation-custom-select';

                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'reservation-custom-select__trigger';
                trigger.setAttribute('aria-haspopup', 'listbox');
                trigger.setAttribute('aria-expanded', 'false');

                const menu = document.createElement('div');
                menu.className = 'reservation-custom-select__menu';
                menu.setAttribute('role', 'listbox');
                menu.id = `reservation-custom-select-${select.name || index}`;

                trigger.setAttribute('aria-controls', menu.id);

                const syncFromSelect = () => {
                    const selectedOption = select.options[select.selectedIndex] || select.options[0];
                    trigger.textContent = selectedOption ? selectedOption.textContent.trim() : 'Select';

                    menu.querySelectorAll('.reservation-custom-select__option').forEach((optionButton) => {
                        const isSelected = optionButton.dataset.value === select.value;
                        optionButton.classList.toggle('is-selected', isSelected);
                        optionButton.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                    });
                };

                Array.from(select.options).forEach((option) => {
                    const optionButton = document.createElement('button');
                    optionButton.type = 'button';
                    optionButton.className = 'reservation-custom-select__option';
                    optionButton.textContent = option.textContent.trim();
                    optionButton.dataset.value = option.value;
                    optionButton.setAttribute('role', 'option');

                    if (!option.value) {
                        optionButton.classList.add('is-placeholder');
                    }

                    optionButton.addEventListener('click', () => {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        syncFromSelect();
                        wrapper.classList.remove('is-open');
                        trigger.setAttribute('aria-expanded', 'false');
                        trigger.focus();
                    });

                    menu.appendChild(optionButton);
                });

                trigger.addEventListener('click', () => {
                    const willOpen = !wrapper.classList.contains('is-open');
                    document.querySelectorAll('.reservation-custom-select.is-open').forEach((openSelect) => {
                        openSelect.classList.remove('is-open');
                        openSelect.querySelector('.reservation-custom-select__trigger')?.setAttribute('aria-expanded', 'false');
                    });

                    wrapper.classList.toggle('is-open', willOpen);
                    trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                });

                select.addEventListener('change', syncFromSelect);

                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);
                wrapper.appendChild(trigger);
                wrapper.appendChild(menu);

                syncFromSelect();
            });
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.reservation-custom-select.is-open').forEach((wrapper) => {
                if (!wrapper.contains(e.target)) {
                    wrapper.classList.remove('is-open');
                    wrapper.querySelector('.reservation-custom-select__trigger')?.setAttribute('aria-expanded', 'false');
                }
            });
        });

        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    @if(session('alert_text') || $errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    @endif
    @if(session('alert_text'))
    <script>
        window.addEventListener('load', function() {
            Swal.fire({
                icon: @json(session('alert_icon', 'success')),
                title: @json(session('alert_title', 'Success')),
                text: @json(session('alert_text')),
                background: '#0b0b0b',
                color: '#ffffff',
                iconColor: '#d8b46a',
                confirmButtonColor: '#d8b46a',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        window.addEventListener('load', function() {
            Swal.fire({
                icon: 'error',
                title: 'Something Went Wrong',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#b10000'
            });
        });
    </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById("visit_time");

            if (select && select.options.length <= 1) {
                for (let hour = 11; hour <= 23; hour++) {
                    [0, 15, 30, 45].forEach(function(minute) {
                        if (hour === 23 && minute > 45) return;

                        let time =
                            String(hour).padStart(2, '0') + ':' +
                            String(minute).padStart(2, '0');

                        let option = document.createElement("option");
                        option.value = time;
                        option.textContent = time;

                        select.appendChild(option);
                    });
                }
            }

            initCustomReservationSelects();
        });
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {

                // Prevent double submit
                if (e.target.dataset.submitted === 'true') {
                    e.preventDefault();
                    return;
                }

                e.target.dataset.submitted = 'true';

                // Add CSRF token if missing
                if (!e.target.querySelector('input[name="_token"]')) {
                    let token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]').content;
                    e.target.appendChild(token);
                }

                // Disable submit button + show loader text
                let btn = e.target.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.dataset.originalText = btn.innerHTML;
                    btn.innerHTML = 'Processing...';
                }
            }
        });

        document.addEventListener("DOMContentLoaded", initGalleryRouter);

        function initGalleryRouter() {
            const routes = {
                "/": () => {
                    loadGalleryData("homeGalleryContainer", "{{ route('home.gallery') }}");
                    loadLatestBlogs("homeBlogContainer", "{{ route('home.latest.blogs') }}");
                },
                "/home": () => {
                    loadGalleryData("homeGalleryContainer", "{{ route('home.gallery') }}");
                    loadLatestBlogs("homeBlogContainer", "{{ route('home.latest.blogs') }}");
                },
                "/gallery": () => loadGalleryData("imageGalleryContainer", "{{ route('gallery.images') }}")
            };

            const path = window.location.pathname;

            if (routes[path]) routes[path]();
        }
        async function loadGalleryData(containerId, apiUrl) {

            try {
                const container = document.getElementById(containerId);
                if (!container) return;

                const response = await fetch(apiUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!data?.status) return;

                const images = data.data;

                const {
                    gridHTML,
                    lightboxHTML
                } = buildGalleryHTML(images);

                container.innerHTML = gridHTML + lightboxHTML;

            } catch (error) {
                console.error("Gallery Load Failed", error);
            }
        }

        function buildGalleryHTML(images) {

            let lightboxes = [];
            const sizePattern = ["wide", "tall", "standard", "standard", "wide", "standard", "tall", "standard"];

            const gridHTML = images.map((img, index) => {
                const sizeClass = sizePattern[index % sizePattern.length];

                lightboxes.push(`
    <div id="img-${img.id}" class="image-lightbox">
        <a href="#gallery" class="lightbox-close">×</a>
        <img
            src="${img.display_url || `/storage/${img.image_path}`}"
            ${img.width ? `width="${img.width}"` : ""}
            ${img.height ? `height="${img.height}"` : ""}>
    </div>
    `);

                    return `
    <div class="gallery-item gallery-item--${sizeClass}">
        <a href="#img-${img.id}">
            <img
                src="${img.display_url || `/storage/${img.image_path}`}"
                loading="${index < 4 ? 'eager' : 'lazy'}"
                fetchpriority="${index === 0 ? 'high' : 'auto'}"
                decoding="async"
                ${img.width ? `width="${img.width}"` : ""}
                ${img.height ? `height="${img.height}"` : ""}>
        </a>
    </div>
    `;
            }).join("");

            return {
                gridHTML,
                lightboxHTML: lightboxes.join("")
            };
        }
        async function loadLatestBlogs(containerId, apiUrl) {
            try {
                const container = document.getElementById(containerId);
                if (!container) return;

                const response = await fetch(apiUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (!data?.status) return;

                const blogs = data.data;

                container.innerHTML = buildBlogHTML(blogs);

            } catch (error) {
                console.error("Blog Load Failed", error);
            }
        }

        function buildBlogHTML(blogs) {

            return blogs.map(blog => {

                const date = new Date(blog.created_at)
                    .toLocaleDateString("en-GB", {
                        day: "2-digit",
                        month: "short",
                        year: "numeric"
                    });

                return `
    <div class="col-lg-4 col-md-6">
        <article class="blog-card">

            <div class="blog-img">
                <img src="/storage/${blog.image}"
                    alt="${blog.title}"
                    loading="lazy">
            </div>

            <div class="blog-content">

                <span class="blog-date">${date}</span>

                <h3>${blog.title}</h3>

                <p>${blog.short_description ?? ''}</p>

                <a class="read-more"
                    href="/blog/${blog.slug}">
                    Read More →
                </a>

            </div>

        </article>
    </div>
    `;

            }).join('');
        }
    </script>

</body>

</html>
