@props([
    'type' => session()->has('error') || session()->has('toast_error') ? 'error' : 'success',
    'message' => session('toast_success') ?? session('success') ?? session('toast_error') ?? session('error')
])

@if ($message)
    @php
        $isError = $type === 'error';
        $bgClass = $isError ? 'bg-red-50 border-red-100 text-red-800' : 'bg-green-50 border-green-100 text-green-800';
        $iconClass = $isError ? 'fa-solid fa-circle-xmark text-red-500' : 'fa-solid fa-circle-check text-green-500';
        $title = $isError ? 'Error' : 'Success';
    @endphp

    <div id="blade-toast-msg"
         class="fixed bottom-5 right-5 z-50 max-w-sm w-full transform translate-y-0 opacity-100 transition-all duration-300 ease-out"
         role="alert">

        <div class="flex items-start p-4 rounded-xl shadow-xl border {{ $bgClass }}">
            <div class="flex-shrink-0 mt-0.5">
                <i class="{{ $iconClass }} text-lg"></i>
            </div>

            <div class="ml-3 flex-1">
                <p class="text-sm font-semibold">{{ $title }}</p>
                <p class="text-xs mt-0.5 opacity-90 leading-relaxed">{{ $message }}</p>
            </div>

            <div class="ml-4 flex-shrink-0 flex">
                <button type="button" onclick="closeToastComponent()" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto dismiss after 4 seconds
        setTimeout(() => { closeToastComponent(); }, 4000);

        function closeToastComponent() {
            const toast = document.getElementById('blade-toast-msg');
            if (toast) {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => { toast.remove(); }, 300);
            }
        }
    </script>
@endif
