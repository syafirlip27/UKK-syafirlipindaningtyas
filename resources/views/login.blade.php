<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50 min-h-screen flex items-center justify-center">

  <div class="bg-white shadow-md rounded-3xl flex w-full max-w-5xl overflow-hidden">

    <!-- Left side: Form -->
    <div class="w-1/2 p-10 flex flex-col justify-center">
      <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">LOGIN</h1>
      @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
          <ul class="list-disc pl-5">
            @foreach($errors->all() as $item)
              <li>{{ $item }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
          <input type="text" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" placeholder="you@example.com" />
        </div>

        <div>
          <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
          <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" placeholder="Password" />
        </div>

        <button type="submit" class="w-full bg-[#1a9bfc] text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition duration-300">Login</button>
      </form>
    </div>

    <!-- Right side: Image -->
    <div class="w-1/2 hidden md:block">
        <img src="{{ asset('assets/images/online-shopping.jpg') }}" alt="Online Shopping" />
    </div>

  </div>

</body>

</html>
