document.addEventListener("DOMContentLoaded", function () {
    const toggler = document.getElementById('menuToggle');
    const bsCollapse = new bootstrap.Collapse(document.getElementById('navbarNav'), {
      toggle: false
    });
  
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth < 992) { // only on mobile/tablet
          bsCollapse.hide(); // force close the menu
          toggler.classList.remove('opened'); // remove cross icon
        }
      });
    });
  
    toggler.addEventListener('click', function () {
      this.classList.toggle('opened');
    });
  });
  