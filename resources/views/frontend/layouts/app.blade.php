<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('meta_title', 'Mediator Blog | Insights on Hotel Management Software')</title>
    <meta name="description" content="@yield('meta_description', 'Stay updated with the Mediator Blog for expert tips and insights on hotel
management software, property management systems, and enhance guest experience')">
    <meta name="keywords" content="@yield('meta_keywords', 'hotel management software, hotel booking system, hotel PMS, property management system, hotel software Georgia, hotel reservation software, hospitality management software, cloud hotel management')">
    <meta name="author" content="Mediator">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
        rel="stylesheet" />

    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" />
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
                        Authentic Georgian cuisine in the heart of Budapest. Experience
                        tradition, hospitality, and unforgettable flavors.
                    </p>
                </div>

                <!-- Menu -->
                <div class="col-lg-2 col-md-4">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Menu</a></li>
                        <li><a href="#">Gallery</a></li>
                        <li><a href="#">Blogs</a></li>
                        <li><a href="#">Contact</a></li>
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
                        Example Street 12, 1051
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
</body>

</html>