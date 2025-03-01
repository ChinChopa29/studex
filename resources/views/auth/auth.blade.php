<!DOCTYPE html>
<html lang="ru">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Авторизация</title>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
   @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 flex justify-center items-center">
   <div class="container mx-auto flex justify-center items-center h-screen">
      <div class="bg-white py-8 px-8 rounded-lg w-1/4">
         <h1 class="text-center text-black text-xl font-bold mb-4">Вход в систему</h1>
         <form action="{{route('auth')}}" class="flex flex-col gap-4" method="post">
            @csrf

            <input type="email" class="text-black border-2 rounded-lg py-2 px-4 transition-all duration-200" placeholder="Почта" name="email" value="{{ old('email') }}">
            @error('email')
               <span class="text-red-500">{{ $message }}</span>
            @enderror

            <input type="password" class="text-black border-2 rounded-lg py-2 px-4" placeholder="Пароль" name="password" value="{{ old('password') }}">
            @error('password')
               <span class="text-red-500">{{ $message }}</span>
            @enderror

            <button type="submit" class="bg-blue-500 text-white rounded-lg py-2 px-4 hover:bg-blue-600 transition-all duration-200">Войти</button>
            @error('login')
                  <span class="text-red-500 text-center">{{$message}}</span>
            @enderror

         </form>
      </div>
   </div>
</body>
</html>