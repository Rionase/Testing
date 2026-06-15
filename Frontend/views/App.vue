<template>
    <header class="navbar">
        <div class="container">
            <nav>
                <RouterLink to="/snap-pop-up" class="nav-link">Snap Pop Up Page</RouterLink>
            </nav>
        </div>
    </header>

    <main class="main-container">
        <RouterView />
    </main>
</template>

<style>
/* Reset dasar untuk seluruh halaman */
body {
    margin: 0;
    font-family: sans-serif;
    background-color: #f9f9f9;
    color: #333;
}
</style>

<style scoped>
/* Navbar Utama */
.navbar {
    background-color: #ffffff;
    border-bottom: 1px solid #ddd;
}

/* Pembungkus Konten (Navbar & Main Content memakai ini agar sejajar) */
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Mengatur posisi Logo di kiri dan Menu di kanan */
.navbar .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 60px;
}

/* Desain Link Menu */
.nav-link {
    text-decoration: none;
    color: #666;
    padding: 8px 16px;
    border-radius: 4px;
    transition: 0.2s;
}

/* Efek Hover (Saat kursor di atas menu) */
.nav-link:hover {
    color: #333;
    background-color: #f0f0f0;
}

/* Class Otomatis dari Vue Router saat Menu Aktif */
.router-link-active {
    color: #42b983;
    background-color: #e8f5e9;
    font-weight: bold;
}

.main-container {
    margin: 50px;
}
</style>
