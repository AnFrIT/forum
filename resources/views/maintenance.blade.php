<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Техническое обслуживание - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .maintenance-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .gear {
            animation: rotate 4s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 text-center">
            
            <!-- Иконка -->
            <div class="mb-8">
                <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-600 gear" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Заголовок -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                🔧 Техническое обслуживание
            </h1>
            
            <!-- Сообщение -->
            <div class="mb-8">
                <p class="text-gray-600 text-lg leading-relaxed">
                    {{ $message }}
                </p>
            </div>
            
            <!-- Индикатор загрузки -->
            <div class="mb-8">
                <div class="flex justify-center space-x-1">
                    <div class="w-3 h-3 bg-blue-500 rounded-full maintenance-animation" style="animation-delay: 0s;"></div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full maintenance-animation" style="animation-delay: 0.5s;"></div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full maintenance-animation" style="animation-delay: 1s;"></div>
                </div>
            </div>
            
            <!-- Дополнительная информация -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">
                    ℹ️ Что происходит?
                </h3>
                <p class="text-blue-700 text-sm">
                    Мы выполняем плановые работы по улучшению сайта. 
                    Обычно это занимает не более 30 минут.
                </p>
            </div>
            
            <!-- Контактная информация -->
            <div class="text-center">
                <p class="text-gray-500 text-sm mb-4">
                    Если у вас срочные вопросы, свяжитесь с администрацией:
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:admin@al-insaf.com" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email
                    </a>
                </div>
            </div>
            
            <!-- Время последнего обновления -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-400">
                    Последнее обновление: {{ now()->format('d.m.Y H:i') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Автообновление страницы каждые 30 секунд -->
    <script>
        // Проверяем статус каждые 30 секунд
        setInterval(function() {
            fetch('/api/maintenance-status')
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'maintenance') {
                        location.reload();
                    }
                })
                .catch(() => {
                    // Если API недоступно, просто перезагружаем страницу
                    location.reload();
                });
        }, 30000);
    </script>
</body>
</html>