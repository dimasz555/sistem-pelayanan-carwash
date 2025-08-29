{{-- resources/views/index.blade.php --}}
@extends('layouts.home')

@section('title', 'Jowin Coffee & Carwash')

@section('content')

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50  transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-r from-coffee to-orange rounded-full flex items-center justify-center">
                    <img src="{{ asset('assets/icons/icon_jowin.png') }}" alt="Jowin Logo"
                        class="w-10 h-10 rounded-full object-cover">
                </div>
                <span class="text-xl font-bold">
                    Jowin Coffee & Carwash
                </span>
            </div>
            <div class="hidden md:flex gap-8">
                <a href="#hero" class="hover:underline transition-colors duration-300">Beranda</a>
                <a href="#cafe" class="hover:underline transition-colors duration-300">Cafe</a>
                <a href="#carwash" class="hover:underline transition-colors duration-300">Carwash</a>
                <a href="#contact" class="hover:underline transition-colors duration-300">Kontak</a>
            </div>
            <button class="md:hidden p-2" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-transparent border-t border-b border-coffee/20">
            <div class="px-2 py-2 space-y-1">
                <a href="#hero" class="block py-2 hover:text-orange transition-colors duration-300">Beranda</a>
                <a href="#cafe" class="block py-2 hover:text-orange transition-colors duration-300">Cafe</a>
                <a href="#carwash" class="block py-2 hover:text-orange transition-colors duration-300">Carwash</a>
                <a href="#contact" class="block py-2 hover:text-orange transition-colors duration-300">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg"></div>
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1200&h=800&fit=crop" alt="Coffee Shop"
                class="w-full h-full object-cover opacity-30">
        </div>

        <!-- Animated Background Elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-orange/10 rounded-full floating"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 bg-coffee/10 rounded-full floating" style="animation-delay: 1s;">
        </div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-cream/10 rounded-full floating" style="animation-delay: 2s;">
        </div>

        <div class="relative z-10 text-center max-w-4xl mx-auto px-4">
            <div class="animate-fade-in-up">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 text-cream leading-tight">
                    <span class="block">Ngopi Santai</span>
                    <span class="block bg-gradient-to-r from-orange to-cream bg-clip-text text-transparent">Mobil
                        Bersih</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-cream/90">
                    Nikmati kopi premium sambil kendaraan Anda dicuci dengan teknologi terdepan
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="#cafe"
                        class="px-8 py-4 bg-orange hover:bg-orange/90 text-coffee font-bold rounded-full transition-all duration-300 transform hover:scale-105 animate-glow">
                        Menu Cafe
                    </a>
                    <a href="#carwash"
                        class="px-8 py-4 border-2 border-cream text-cream hover:bg-cream hover:text-coffee font-bold rounded-full transition-all duration-300">
                        Promo Carwash
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce-gentle">
            <svg class="w-6 h-6 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Cafe Section -->
    <section id="cafe" class="py-20 bg-cream relative">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1200&h=800&fit=crop"
                alt="Coffee Background" class="w-full h-full object-cover opacity-10">
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="text-center mb-16 animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 text-coffee">
                    Menu <span class="text-orange">Unggulan</span>
                </h2>
                <p class="text-lg text-coffee/70 max-w-2xl mx-auto">
                    Dua signature menu yang akan memanjakan lidah Anda
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Kopi Jowin -->
                <div
                    class="group relative overflow-visible rounded-3xl bg-white shadow-2xl transform hover:scale-105 transition-all duration-500 h-96">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-coffee/90 to-orange/90 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl">
                    </div>
                    <!-- Coffee Image -->
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 z-20">
                        <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200&h=200&fit=crop"
                            alt="Kopi Jowin" class="w-28 h-28 object-cover rounded-full drop-shadow-2xl">
                    </div>
                    <div class="relative z-10 p-8 h-full flex flex-col justify-between pt-20">
                        <div class="flex-1">
                            <h3
                                class="text-2xl font-bold mb-4 text-coffee group-hover:text-cream transition-colors duration-500 text-center">
                                Kopi Jowin
                            </h3>
                            <p
                                class="text-coffee/70 group-hover:text-cream/90 mb-6 transition-colors duration-500 text-center leading-relaxed">
                                Kopi dengan rasa yang khas dan aroma yang menggugah selera. Diseduh dengan
                                teknik manual brewing terbaik untuk pengalaman kopi yang tak terlupakan.
                            </p>
                        </div>
                        <div class="text-center">
                            <span
                                class="text-3xl font-bold text-orange group-hover:text-cream transition-colors duration-500">
                                Rp 15.000
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mie Nyemek -->
                <div
                    class="group relative overflow-visible rounded-3xl bg-white shadow-2xl transform hover:scale-105 transition-all duration-500 h-96">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-orange/90 to-coffee/90 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl">
                    </div>
                    <!-- Noodle Image -->
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 z-20">
                        <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=200&h=200&fit=crop"
                            alt="Mie Nyemek" class="w-28 h-28 object-cover rounded-full drop-shadow-2xl">
                    </div>
                    <div class="relative z-10 p-8 h-full flex flex-col justify-between pt-20">
                        <div class="flex-1">
                            <h3
                                class="text-2xl font-bold mb-4 text-coffee group-hover:text-cream transition-colors duration-500 text-center">
                                Mie Nyemek
                            </h3>
                            <p
                                class="text-coffee/70 group-hover:text-cream/90 mb-6 transition-colors duration-500 text-center leading-relaxed">
                                Mie dengan kuah yang kaya rasa, dilengkapi dengan topping pilihan dan bumbu rahasia yang
                                membuatnya istimewa. Sajian hangat yang sempurna untuk segala cuaca.
                            </p>
                        </div>
                        <div class="text-center">
                            <span
                                class="text-3xl font-bold text-orange group-hover:text-cream transition-colors duration-500">
                                Rp 15.000
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Carwash Section -->
    <section id="carwash" class="py-20 bg-gradient-to-br from-coffee to-coffee/90 text-cream relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1605164598708-25701594473e?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Coffee Background" class="w-full h-full object-cover opacity-10">
        </div>

        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full bg-repeat"
                style="background-image: url('data:image/svg+xml,<svg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'><g fill=\'none\' fill-rule=\'evenodd\'><g fill=\'%23ffffff\' fill-opacity=\'0.1\'><circle cx=\'30\' cy=\'30\' r=\'4\'/></g></svg>')">
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Layanan <span class="text-orange">Carwash</span>
                </h2>
                <p class="text-lg text-cream/80 max-w-2xl mx-auto">
                    Menggunakan teknologi modern untuk hasil yang maksimal
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 mb-16">
                <!-- Promo 1 -->
                <div class="relative group">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-orange to-orange/80 rounded-3xl transform rotate-6 group-hover:rotate-3 transition-transform duration-300">
                    </div>
                    <div
                        class="relative bg-cream text-coffee p-8 rounded-3xl shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300">
                        <!-- Service Image -->
                        <div class="mb-6 flex justify-center">
                            <img src="https://images.unsplash.com/photo-1607860108855-64acf2078ed9?w=200&h=150&fit=crop"
                                alt="Motor Wash" class="w-full h-32 object-cover rounded-2xl">
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Cuci Motor</h3>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-orange">Rp 20.000</span>
                            <span class="text-lg line-through text-coffee/50 ml-2">Rp 25.000</span>
                        </div>
                        <ul class="space-y-2 mb-6 text-coffee/70">
                            <li>✓ Cuci menyeluruh</li>
                            <li>✓ Poles body motor</li>
                            <li>✓ Pembersihan velg</li>
                        </ul>
                    </div>
                </div>

                <!-- Promo 2 -->
                <div class="relative group">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-orange to-orange/80 rounded-3xl transform -rotate-3 group-hover:rotate-0 transition-transform duration-300">
                    </div>
                    <div
                        class="relative bg-cream text-coffee p-8 rounded-3xl shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300">
                        <!-- Service Image -->
                        <div class="mb-6 flex justify-center">
                            <img src="https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=200&h=150&fit=crop"
                                alt="Car Wash" class="w-full h-32 object-cover rounded-2xl">
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Cuci Mobil</h3>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-orange">Rp 65.000</span>
                            <span class="text-lg line-through text-coffee/50 ml-2">Rp 85.000</span>
                        </div>
                        <ul class="space-y-2 mb-6 text-coffee/70">
                            <li>✓ Cuci + wax protection</li>
                            <li>✓ Interior detailing</li>
                            <li>✓ Poles dashboard</li>
                            <li>✓ Pewangi premium</li>
                        </ul>
                    </div>
                </div>

                <!-- Promo 3 -->
                <div class="relative group">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-orange to-orange/80 rounded-3xl transform rotate-2 group-hover:-rotate-1 transition-transform duration-300">
                    </div>
                    <div
                        class="relative bg-cream text-coffee p-8 rounded-3xl shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300">
                        <!-- Service Image -->
                        <div class="mb-6 flex justify-center">
                            <img src="https://images.unsplash.com/photo-1493238792000-8113da705763?w=200&h=150&fit=crop"
                                alt="Quick Wash" class="w-full h-32 object-cover rounded-2xl">
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Cuci Cepat</h3>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-orange">Rp 20.000</span>
                            <span class="text-lg line-through text-coffee/50 ml-2">Rp 30.000</span>
                        </div>
                        <ul class="space-y-2 mb-6 text-coffee/70">
                            <li>✓ Cuci cepat 15 menit</li>
                            <li>✓ Bilas bersih</li>
                            <li>✓ Lap kering</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tracking Button Section -->
            <div class="text-center">
                <div
                    class="bg-gradient-to-r from-orange/20 to-coffee/20 backdrop-blur-sm rounded-3xl p-8 max-w-2xl mx-auto border border-orange/30">
                    <h3 class="text-2xl font-bold mb-4 text-cream">Cek Status Cucian Anda</h3>
                    <p class="text-cream/80 mb-6">Pantau progress pencucian kendaraan Anda secara real-time</p>

                    <a href="{{ route('tracking.index') }}"
                        class="inline-block px-8 py-4 bg-orange text-coffee font-bold rounded-full hover:bg-orange/90 transition-all duration-300 transform hover:scale-105">
                        Cek Status Booking
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-cream relative">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?w=1200&h=800&fit=crop"
                alt="Contact Background" class="w-full h-full object-cover opacity-10">
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 text-coffee">
                    Temukan <span class="text-orange">Lokasi Kami</span>
                </h2>
                <p class="text-lg text-coffee/70 max-w-2xl mx-auto">
                    Kunjungi kami untuk pengalaman yang tak terlupakan
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-coffee to-orange rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-cream" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-coffee mb-2">Alamat</h3>
                            <p class="text-coffee/70">Jl. Perdana No. 999, Pontianak, Kalimantan Barat</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-coffee to-orange rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-cream" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-coffee mb-2">Telepon</h3>
                            <p class="text-coffee/70">+62 812-3456-7890</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-coffee to-orange rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-cream" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-coffee mb-2">Jam Operasional</h3>
                            <p class="text-coffee/70">Senin - Minggu: 08.00 - 22.00 WIB</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-coffee/10 to-orange/10 rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-coffee mb-4">Hubungi Kami</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <a href="https://wa.me/6281234567890" target="_blank"
                                class="flex items-center justify-center space-x-2 bg-green-500 text-white px-6 py-3 rounded-full hover:bg-green-600 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                            <a href="https://instagram.com/jowincoffee" target="_blank"
                                class="flex items-center justify-center space-x-2 bg-purple-500 text-white px-6 py-3 rounded-full hover:bg-[#833AB4] transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                                </svg>
                                <span>Instagram</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div
                    class="bg-gradient-to-br from-coffee/10 to-orange/10 rounded-3xl h-96 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-coffee/5 to-orange/5"></div>
                    <iframe class="w-full h-full rounded-3xl border-0"
                        src="https://maps.google.com/maps?q=pontianak&t=&z=13&ie=UTF8&iwloc=&output=embed" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-coffee text-cream py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-orange to-cream rounded-full flex items-center justify-center">
                            <img src="{{ asset('assets/icons/icon_jowin.png') }}" alt="Jowin Logo"
                                class="w-10 h-10 rounded-full object-cover">
                        </div>
                        <span class="text-2xl font-bold">Jowin Coffee & Carwash</span>
                    </div>
                    <p class="text-cream/70 mb-4">
                        Kombinasi sempurna antara cita rasa kopi premium dan layanan carwash berkualitas tinggi di
                        Pontianak.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-orange">Menu & Layanan</h3>
                    <ul class="space-y-2">
                        <li><a href="#hero"
                                class="text-cream/70 hover:text-orange transition-colors duration-300">Beranda</a></li>
                        <li><a href="#cafe" class="text-cream/70 hover:text-orange transition-colors duration-300">Menu
                                Cafe</a></li>
                        <li><a href="#carwash"
                                class="text-cream/70 hover:text-orange transition-colors duration-300">Layanan Carwash</a>
                        </li>
                        <li><a href="#contact"
                                class="text-cream/70 hover:text-orange transition-colors duration-300">Kontak</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-orange">Kontak</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" />
                            </svg>
                            <span class="text-cream/70 text-sm">Jl. Perdana No. 999, Pontianak</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z" />
                            </svg>
                            <span class="text-cream/70 text-sm">+62 812-3456-7890</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z" />
                            </svg>
                            <span class="text-cream/70 text-sm">08.00 - 22.00 WIB</span>
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-4">
                        <a href="https://wa.me/6281234567890" target="_blank"
                            class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center hover:bg-green-600 transform hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </a>
                        <a href="https://instagram.com/jowincoffee" target="_blank"
                            class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center hover:bg-purple-600 transform hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-cream/20 pt-10 text-center">
                <p class="text-cream/60 text-sm">
                    © 2025 Jowin Coffee & Carwash
                </p>
            </div>
        </div>
    </footer>

@endsection
