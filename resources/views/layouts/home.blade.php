<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <!-- Meta Tags Dasar -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jowin Coffee & Carwash - Ngopi Santai Mobil Bersih | Pontianak')</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Jowin Coffee & Carwash Pontianak - Nikmati kopi premium sambil kendaraan Anda dicuci dengan teknologi terdepan. Kopi Jowin Rp 15.000, Mie Nyemek Rp 15.000. Buka 08.00-22.00 WIB di Jl. Perdana No. 999">
    <meta name="robots" content="index, follow">

    <!-- Open Graph untuk Media Sosial -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Jowin Coffee & Carwash - Ngopi Santai Mobil Bersih">
    <meta property="og:description"
        content="Nikmati kopi premium sambil kendaraan Anda dicuci dengan teknologi terdepan di Pontianak. Kopi Jowin & Mie Nyemek hanya Rp 15.000">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Jowin Coffee & Carwash">
    <meta property="og:image" content="{{ asset('assets/icons/icon_jowin.png') }}">
    <meta property="og:locale" content="id_ID">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Hreflang -->
    <link rel="alternate" hreflang="id" href="{{ url()->current() }}">

    <!-- Tailwind CSS v4 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Jowin Coffee & Carwash",
      "description": "Kombinasi cafe dan carwash premium di Pontianak. Nikmati kopi premium sambil kendaraan Anda dicuci dengan teknologi terdepan",
      "url": "{{ url()->current() }}",
      "telephone": "+62812-3456-7890",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jl. Perdana No. 999",
        "addressLocality": "Pontianak",
        "addressRegion": "Kalimantan Barat",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -0.0621904,
        "longitude": 109.3378415
      },
      "openingHours": "Mo-Su 08:00-22:00",
      "priceRange": "Rp 15.000 - Rp 50.000",
      "servedCuisine": "Indonesian Coffee",
      "hasMenu": {
        "@type": "Menu",
        "hasMenuSection": [
          {
            "@type": "MenuSection",
            "name": "Minuman",
            "hasMenuItem": {
              "@type": "MenuItem",
              "name": "Kopi Jowin",
              "description": "Kopi dengan rasa yang khas dan aroma yang menggugah selera",
              "offers": {
                "@type": "Offer",
                "price": "15000",
                "priceCurrency": "IDR"
              }
            }
          },
          {
            "@type": "MenuSection", 
            "name": "Makanan",
            "hasMenuItem": {
              "@type": "MenuItem",
              "name": "Mie Nyemek",
              "description": "Mie dengan kuah yang kaya rasa, dilengkapi dengan topping pilihan",
              "offers": {
                "@type": "Offer",
                "price": "15000",
                "priceCurrency": "IDR"
              }
            }
          }
        ]
      },
      "additionalType": "CarWash",
      "makesOffer": [
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Cuci Motor",
            "description": "Layanan pencucian motor lengkap dengan pembersihan menyeluruh"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service", 
            "name": "Cuci Mobil Body",
            "description": "Fokus pada pembersihan eksterior mobil dengan teknologi foam wash"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Cuci Mobil Full",
            "description": "Paket lengkap pembersihan eksterior dan interior secara detail"
          }
        }
      ],
      "sameAs": [
        "https://wa.me/6281234567890",
        "https://instagram.com/jowincoffee"
      ]
    }
    </script>
</head>

<body class="bg-cream text-coffee font-sans overflow-x-hidden">
    @yield('content')

    <!-- JavaScript untuk interaktivitas -->
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Navbar scroll effect 
        function updateNavbarStyle() {
            const navbar = document.getElementById('navbar');
            const navLinks = navbar.querySelectorAll('a');
            const navbarTitle = navbar.querySelector('.text-xl.font-bold');
            const mobileMenuButton = navbar.querySelector('button svg');
            const heroSection = document.getElementById('hero');
            const scrollY = window.scrollY || window.pageYOffset;
            const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;

            if (scrollY < heroBottom - 80) {
                // Di section Beranda - semua text putih
                navbar.style.backgroundColor = 'transparent';
                navbar.classList.remove('shadow-lg');

                // Update nav links ke putih
                navLinks.forEach(link => {
                    link.style.color = 'white';
                });

                // Update title ke putih
                navbarTitle.style.color = 'white';

                // Update mobile menu button ke putih
                if (mobileMenuButton) {
                    mobileMenuButton.style.color = 'white';
                }
            } else {
                // Sudah scroll lewat Beranda - semua text coffee
                navbar.style.backgroundColor = 'var(--color-cream)';
                navbar.classList.add('shadow-lg');

                // Update nav links ke coffee
                navLinks.forEach(link => {
                    link.style.color = 'var(--color-coffee)';
                });

                // Update title ke coffee
                navbarTitle.style.color = 'var(--color-coffee)';

                // Update mobile menu button ke coffee
                if (mobileMenuButton) {
                    mobileMenuButton.style.color = 'var(--color-coffee)';
                }
            }
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                // Jangan animasi jika section id-nya 'hero' (beranda)
                if (entry.isIntersecting && entry.target.id !== 'hero') {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Close mobile menu when clicking on links
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuButton = document.querySelector('button[onclick="toggleMobileMenu()"]');

            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Observe all sections when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            updateNavbarStyle();
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
        });

        // Update navbar on scroll
        window.addEventListener('scroll', updateNavbarStyle);

        // Ripple effect for buttons
        document.addEventListener('click', function(e) {
            if (e.target.matches('button') || e.target.closest('button')) {
                const button = e.target.matches('button') ? e.target : e.target.closest('button');
                const rect = button.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.className = 'absolute bg-white/20 rounded-full pointer-events-none animate-ping';

                button.style.position = 'relative';
                button.style.overflow = 'hidden';
                button.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
