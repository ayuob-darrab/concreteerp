  @if (session('success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
          class="flex items-center justify-center rounded bg-success-light p-3.5 text-success dark:bg-success-dark-light relative transition-all duration-500 ease-in-out">
          <span class="text-center">
              <strong class="mr-1">نجاح</strong> {{ session('success') }}
          </span>
          <button type="button" class="absolute top-2 right-2 hover:opacity-80" @click="show = false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  class="h-5 w-5">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
          </button>
      </div>
  @endif

  @if (session('error'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
          class="flex items-center justify-center rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light relative transition-all duration-500 ease-in-out">
          <span class="text-center">
              <strong class="mr-1">خطأ!</strong> {{ session('error') }}
          </span>
          <button type="button" class="absolute top-2 right-2 hover:opacity-80" @click="show = false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  class="h-5 w-5">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
          </button>
      </div>
  @endif

  @if (session('warning'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
          class="flex items-center justify-center rounded bg-warning-light p-3.5 text-warning dark:bg-warning-dark-light relative transition-all duration-500 ease-in-out">

          <span class="text-center">
              <strong class="mr-1">تنبيه!</strong> {{ session('warning') }}
          </span>

          <button type="button" class="absolute top-2 right-2 hover:opacity-80" @click="show = false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  class="h-5 w-5">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
          </button>
      </div>
  @endif
