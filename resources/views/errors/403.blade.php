@extends('layouts.app')

@section('title', 'Доступ запрещен - ' . setting('forum_name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="error-template">
            <h1 class="display-1 text-warning">403</h1>
            <h2>Доступ запрещен</h2>
            <p class="lead">У вас нет прав для просмотра этой страницы.</p>
            
            <div class="error-actions mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-house"></i> На главную
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>

            <div class="mt-5">
                <h5>Возможные причины:</h5>
                <ul class="list-unstyled">
                    <li>У вас недостаточно прав для доступа к этой странице</li>
                    <li>Вы не авторизованы на сайте</li>
                    <li>Ваш аккаунт заблокирован</li>
                    <li>Страница доступна только определенным пользователям</li>
                </ul>
            </div>

            @guest
                <div class="mt-4">
                    <p>Если у вас есть аккаунт, попробуйте войти:</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Войти
                    </a>
                </div>
            @endguest
        </div>
    </div>
</div>

<style>
.error-template {
    padding: 40px 15px;
}
.error-template .display-1 {
    font-size: 8rem;
    font-weight: 300;
}
</style>
@endsection