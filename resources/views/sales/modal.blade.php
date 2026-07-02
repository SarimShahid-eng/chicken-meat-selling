  {{-- Purchase Details Modal --}}
  <div id="purchaseDetailsModal" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm js-close-modal"></div>

      <div class="relative min-h-screen flex items-center justify-center p-4">
          <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-fade-in">

              <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                  <div>
                      <h2 class="text-lg font-bold text-gray-900">Purchase Details</h2>
                      <p class="text-xs text-gray-500 mt-0.5">
                          Voucher <span id="modalVoucherNo" class="font-semibold text-amber-600">—</span>
                      </p>
                  </div>
                  <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors js-close-modal">
                      <i class="fas fa-times text-lg"></i>
                  </button>
              </div>

              <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                  {{-- Loading state --}}
                  <div id="modalLoading" class="hidden py-10 text-center">
                      <i class="fas fa-spinner fa-spin text-2xl text-amber-500"></i>
                      <p class="text-sm text-gray-400 mt-2">Loading purchase details...</p>
                  </div>

                  {{-- Error state --}}
                  <div id="modalError" class="hidden py-10 text-center">
                      <i class="fas fa-triangle-exclamation text-2xl text-red-400"></i>
                      <p class="text-sm text-red-500 mt-2">Failed to load purchase details. Please try again.</p>
                  </div>

                  <div id="modalContent" class="hidden space-y-4">
                      <div class="grid grid-cols-2 gap-4">
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</p>
                              <p id="modalProduct" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Supplier</p>
                              <p id="modalSupplier" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Region</p>
                              <p id="modalRegion" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vehicle No</p>
                              <p id="modalVehicleNo" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                      </div>

                      <hr class="border-gray-100">

                      <div class="grid grid-cols-2 gap-4">
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Crate Qty</p>
                              <p id="modalCrateQty" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Weight</p>
                              <p id="modalTotalWeight" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Weight Cut</p>
                              <p id="modalWeightCut" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Net Weight</p>
                              <p id="modalNetWeight" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                      </div>

                      <hr class="border-gray-100">

                      <div class="grid grid-cols-2 gap-4">
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rate</p>
                              <p id="modalRate" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div>
                              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rate Date</p>
                              <p id="modalRateDate" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                          </div>
                          <div class="col-span-2 bg-amber-50 border border-amber-100 rounded-lg px-4 py-3">
                              <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Total Amount</p>
                              <p id="modalTotalAmount" class="text-lg font-bold text-amber-700 mt-0.5">—</p>
                          </div>
                      </div>

                      <p class="text-xs text-gray-400 pt-1">
                          Created: <span id="modalCreatedAt">—</span>
                      </p>
                  </div>
              </div>

              <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                  <button type="button"
                      class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors js-close-modal">
                      Close
                  </button>
              </div>
          </div>
      </div>
  </div>


  <div id="rateConfirmModal"
      class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
      <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden transition-all transform">

          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-amber-50">
              <h3 class="text-base font-semibold text-amber-900 flex items-center gap-2">
                  <i class="fas fa-receipt"></i> Set Purchase Rate
              </h3>
              <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors js-close-rate-modal">
                  <i class="fas fa-times"></i>
              </button>
          </div>

          <!-- Form Formulation -->
          <form id="rateConfirmForm" method="POST" action="">
              @csrf

              <div class="p-6 space-y-4">
                  <div>
                      <label for="modalInputRate"
                          class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                          Enter Rate
                      </label>
                      <div class="relative">
                          <input type="number" step="0.01" name="rate" id="modalInputRate" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm text-gray-700"
                              placeholder="0.00">
                      </div>
                  </div>
              </div>

              <!-- Footer Buttons -->
              <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                  <button type="button"
                      class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors js-close-rate-modal">
                      Cancel
                  </button>
                  <button type="submit"
                      class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-colors shadow-sm">
                      Submit Rate
                  </button>
              </div>
          </form>
      </div>
  </div>
