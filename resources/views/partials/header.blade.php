<header class="site-header">
    <div class="header-container">
        <a href="{{ route('home') }}" class="logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text">LaravelApp</span>
        </a>
        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Главная</a>
            <a href="{{ route('form.show') }}" class="{{ request()->routeIs('form.show') ? 'active' : '' }}">Форма заявки</a>
            <a href="{{ route('records.index') }}" class="{{ request()->routeIs('records.index') ? 'active' : '' }}">Таблица записей</a>
        </nav>
    </div>
</header>
