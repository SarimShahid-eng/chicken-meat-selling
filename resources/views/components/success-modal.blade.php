@props([
    'id' => 'successModal',
    'title' => 'Sale Recorded Successfully!',
    'message' => session('sales_successful'),
    'saleId' => session('sale_id'),
    'printRoute' => 'sales.receipt',
])

@if ($message)
    <div id="{{ $id }}" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            onclick="closeModal('{{ $id }}')"></div>

        <!-- Content Container -->
        <div class="flex min-h-full items-start justify-center p-4 text-center sm:p-6">
            <div
                class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white p-6 text-left shadow-2xl transition-all border border-gray-100 mt-8 z-10">

                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $message }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" onclick="closeModal('{{ $id }}')"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer">
                        Close
                    </button>

                    @if ($saleId)
                        <a href="{{ route($printRoute, $saleId) }}" target="_blank"
                            onclick="closeModal('{{ $id }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition-all">
                            <i class="fa-solid fa-print"></i>
                            <span>Print Voucher</span>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        if (typeof closeModal !== 'function') {
            function closeModal(modalId = 'successModal') {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.remove();
                }
            }
        }
    </script>
@endif
