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
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
        rel="stylesheet" />
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" />

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

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
                        <li>Phone: <a href="tel:+36301234567"> +36 30 123 4567</a></a></li>
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
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('alert_text'))
    <script>
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
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Something Went Wrong',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#b10000'
        });
    </script>
    @endif

    <script>
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
                "/": () => loadGalleryData("homeGalleryContainer", "{{ route('home.gallery') }}"),
                "/home": () => loadGalleryData("homeGalleryContainer", "{{ route('home.gallery') }}"),
                "/gallery": () => loadGalleryData("imageGalleryContainer", "{{ route('gallery.images') }}")
            };

            const path = window.location.pathname;

            if (routes[path]) routes[path]();
        }
        async function loadGalleryData(containerId, apiUrl, columnCount = 4) {

            try {
                const container = document.getElementById(containerId);
                if (!container) return;

                const {
                    data
                } = await axios.get(apiUrl);
                if (!data?.status) return;

                const images = data.data;

                const columns = splitIntoColumns(images, columnCount);
                const {
                    gridHTML,
                    lightboxHTML
                } = buildGalleryHTML(columns);

                container.innerHTML = gridHTML + lightboxHTML;

            } catch (error) {
                console.error("Gallery Load Failed", error);
            }
        }

        function splitIntoColumns(images, columnCount) {

            const perColumn = Math.ceil(images.length / columnCount);

            return Array.from({
                    length: columnCount
                }, (_, i) =>
                images.slice(i * perColumn, (i + 1) * perColumn)
            );
        }

        function buildGalleryHTML(columns) {

            let lightboxes = [];

            const gridHTML = columns.map(col => {

                const colHTML = col.map((img, index) => {

                    const sizeClass = index % 2 === 0 ? "small" : "tall";

                    lightboxes.push(`
                <div id="img-${img.id}" class="image-lightbox">
                    <a href="#gallery" class="lightbox-close">×</a>
                    <img src="/storage/${img.image_path}">
                </div>
            `);

                    return `
                <div class="gallery-item ${sizeClass}">
                    <a href="#img-${img.id}">
                        <img src="/storage/${img.image_path}" loading="lazy">
                    </a>
                </div>
            `;
                }).join("");

                return `<div class="gallery-col">${colHTML}</div>`;

            }).join("");

            return {
                gridHTML,
                lightboxHTML: lightboxes.join("")
            };
        }
    </script>

    <script>
        /*
    document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    try {
    const res = await axios.post('/api/reservations', data);

    Swal.fire({
    icon: 'success',
    title: 'Booking Confirmed 🎉',
    text: res.data.message,
    });

    form.reset();

    } catch (err) {
    Swal.fire({
    icon: 'error',
    title: 'Booking Failed',
    text: err.response?.data?.message || 'Something went wrong',
    });
    }
    });*/
    </script>

</body>

</html>