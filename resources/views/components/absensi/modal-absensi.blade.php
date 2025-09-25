<div id="absenceModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Modal Card -->
    <div class="relative mx-auto mt-10 w-[800px] rounded-xl bg-white shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between border-b px-6 py-4">
            <div class="flex items-center gap-3">
                <button type="button" id="absenceModalCloseBtn"
                    class="text-2xl font-bold text-gray-400 hover:text-gray-600">&times;</button>
                <h3 class="text-lg font-bold text-black">Surat Persetujuan Izin/Sakit:</h3>
            </div>
            <div class="text-right font-medium text-black" id="absenceModalUserName">-</div>
        </div>

        <!-- Body -->
        <div class="grid grid-cols-2 gap-6 px-6 py-5">
            <!-- Foto -->
            <div>
                <div class="mb-2 text-sm font-semibold text-black">Foto:</div>
                <div class="flex h-56 w-full items-center justify-center rounded-lg bg-gray-200">
                    <img id="absenceModalImage" alt="Lampiran" class="hidden h-full w-full rounded-lg object-contain" />
                    <span id="absenceModalImagePlaceholder" class="text-lg font-normal text-gray-500">Pictures</span>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <div class="mb-2 text-sm font-semibold text-black">Catatan:</div>
                <div class="flex h-56 w-full items-center justify-center rounded-lg bg-gray-200">
                    <div id="absenceModalNote"
                        class="h-full w-full overflow-auto whitespace-pre-wrap p-4 text-gray-700"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 pb-6">
            <button type="button" id="absenceRejectBtn"
                class="rounded-lg bg-[#F15A4A] px-5 py-2 text-white shadow hover:brightness-95">Tolak</button>
            <button type="button" id="absenceApproveBtn"
                class="rounded-lg bg-[#60A5FA] px-5 py-2 text-white shadow hover:brightness-95">Setujui</button>
            <!-- Status buttons (shown after approval/rejection) -->
            <button type="button" id="absenceStatusRejected"
                class="rounded-lg bg-red-500 px-5 py-2 text-white shadow cursor-not-allowed opacity-75 hidden">Ditolak</button>
            <button type="button" id="absenceStatusApproved"
                class="rounded-lg bg-green-500 px-5 py-2 text-white shadow cursor-not-allowed opacity-75 hidden">Disetujui</button>
        </div>
    </div>

    <script>
        // Safe-guard: only register once
        (function() {
            if (window.__absenceModalBound) return;
            window.__absenceModalBound = true;

            const modal = document.getElementById('absenceModal');
            const closeBtn = document.getElementById('absenceModalCloseBtn');
            const nameEl = document.getElementById('absenceModalUserName');
            const noteEl = document.getElementById('absenceModalNote');
            const imgEl = document.getElementById('absenceModalImage');
            const imgPh = document.getElementById('absenceModalImagePlaceholder');
            const approveBtn = document.getElementById('absenceApproveBtn');
            const rejectBtn = document.getElementById('absenceRejectBtn');
            const statusApprovedBtn = document.getElementById('absenceStatusApproved');
            const statusRejectedBtn = document.getElementById('absenceStatusRejected');

            let currentContext = null; // {kind: 'attendance'|'absence', id, userName}

            function openModal(ctx) {
                currentContext = ctx;
                nameEl.textContent = ctx.userName || '-';
                noteEl.textContent = ctx.note || '-';

                // Handle image display
                if (ctx.imageUrl && ctx.imageUrl.trim() !== '') {
                    imgEl.src = ctx.imageUrl;
                    imgEl.classList.remove('hidden');
                    imgPh.classList.add('hidden');

                    // Handle image load error
                    imgEl.onerror = function() {
                        imgEl.classList.add('hidden');
                        imgPh.classList.remove('hidden');
                        imgPh.textContent = 'Gagal memuat gambar';
                    };
                } else {
                    imgEl.src = '';
                    imgEl.classList.add('hidden');
                    imgPh.classList.remove('hidden');
                    imgPh.textContent = 'Pictures';
                }

                // Handle button display based on approval status
                const showAction = ctx.kind === 'absence' && !!ctx.id;
                const isApproved = ctx.isApproved;
                const isRejected = ctx.isRejected;

                if (showAction) {
                    if (isApproved === true) {
                        // Show approved status button
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        statusApprovedBtn.classList.remove('hidden');
                        statusRejectedBtn.classList.add('hidden');
                    } else if (isRejected === true || isApproved === false) {
                        // Show rejected status button
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        statusApprovedBtn.classList.add('hidden');
                        statusRejectedBtn.classList.remove('hidden');
                    } else {
                        // Show action buttons for pending requests
                        approveBtn.classList.remove('hidden');
                        rejectBtn.classList.remove('hidden');
                        statusApprovedBtn.classList.add('hidden');
                        statusRejectedBtn.classList.add('hidden');
                    }
                } else {
                    // Hide all buttons for non-absence requests
                    approveBtn.classList.add('hidden');
                    rejectBtn.classList.add('hidden');
                    statusApprovedBtn.classList.add('hidden');
                    statusRejectedBtn.classList.add('hidden');
                }

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Public API used by pages
            window.AbsenceModal = {
                open: openModal,
                close: closeModal
            };

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            // Helper function to get auth token from multiple sources
            function getAuthToken() {
                // Try to get from cookies first
                const cookies = document.cookie.split(';');
                for (let cookie of cookies) {
                    const [name, value] = cookie.trim().split('=');
                    if (name === 'auth_token') {
                        return value;
                    }
                }
                
                // Try to get from localStorage
                const localStorageToken = localStorage.getItem('auth_token');
                if (localStorageToken) {
                    return localStorageToken;
                }
                
                // Try to get from sessionStorage
                const sessionStorageToken = sessionStorage.getItem('auth_token');
                if (sessionStorageToken) {
                    return sessionStorageToken;
                }
                
                return null;
            }

            async function sendApproval(isApproved) {
                if (!currentContext || currentContext.kind !== 'absence' || !currentContext.id) return;

                // Show confirmation alert
                const action = isApproved ? 'menyetujui' : 'menolak';
                const confirmed = confirm(`Apakah Anda yakin ingin ${action} pengajuan ini?`);
                if (!confirmed) return;

                try {
                    approveBtn.disabled = true;
                    rejectBtn.disabled = true;
                    
                    // Get auth token
                    const authToken = getAuthToken();
                    if (!authToken) {
                        throw new Error('Token autentikasi tidak ditemukan. Silakan login kembali.');
                    }
                    
                    const res = await fetch(`/api/absences/${currentContext.id}/approve`, {
                        method: 'PATCH',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${authToken}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'include',
                        body: JSON.stringify({ is_approved: !!isApproved })
                    });
                    
                    if (!res.ok) {
                        const errorData = await res.json().catch(() => ({}));
                        const errorMessage = errorData.message || `HTTP ${res.status}: Gagal memperbarui status`;
                        throw new Error(errorMessage);
                    }
                    
                    const result = await res.json();
                    const statusText = isApproved ? 'disetujui' : 'ditolak';

                    // Update UI to show status immediately
                    if (isApproved) {
                        // Show approved status button
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        statusApprovedBtn.classList.remove('hidden');
                        statusRejectedBtn.classList.add('hidden');
                        // Update context
                        currentContext.isApproved = true;
                        currentContext.isRejected = false;
                    } else {
                        // Show rejected status button
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        statusApprovedBtn.classList.add('hidden');
                        statusRejectedBtn.classList.remove('hidden');
                        // Update context
                        currentContext.isApproved = false;
                        currentContext.isRejected = true;
                    }

                    // Show success alert
                    alert(`Pengajuan berhasil ${statusText}!`);

                    // Don't close modal immediately, let user see the status change
                    // closeModal();
                    
                    // Refresh listing if helper exists on page
                    if (typeof window.fetchAttendanceData === 'function') {
                        window.fetchAttendanceData();
                    }
                } catch (err) {
                    console.error('Approval error:', err);
                    alert('Terjadi kesalahan: ' + (err.message || 'Gagal memproses permintaan'));
                } finally {
                    approveBtn.disabled = false;
                    rejectBtn.disabled = false;
                }
            }

            approveBtn.addEventListener('click', () => sendApproval(true));
            rejectBtn.addEventListener('click', () => sendApproval(false));

            // Expose helper to fetch detail by id (absence)
            window.loadAbsenceDetailAndOpen = async function(id, userName) {
                try {
                    // Get auth token
                    const authToken = getAuthToken();
                    if (!authToken) {
                        throw new Error('Token autentikasi tidak ditemukan. Silakan login kembali.');
                    }
                    
                    const res = await fetch(`/api/absences/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${authToken}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'include'
                    });
                    
                    if (!res.ok) {
                        const errorData = await res.json().catch(() => ({}));
                        const errorMessage = errorData.message || `HTTP ${res.status}: Gagal mengambil detail`;
                        throw new Error(errorMessage);
                    }
                    
                    const json = await res.json();
                    const data = json.data || {};

                    // Construct proper image URL
                    let imageUrl = '';
                    if (data.upload_attachment) {
                        // Handle both relative and absolute paths
                        if (data.upload_attachment.startsWith('http')) {
                            imageUrl = data.upload_attachment;
                        } else {
                            imageUrl = `/storage/${data.upload_attachment}`;
                        }
                    }

                    // Get user name from data if not provided
                    const displayName = data.user && data.user.name ? data.user.name : userName;

                    openModal({
                        kind: 'absence',
                        id: id,
                        userName: displayName,
                        note: data.description || '-',
                        imageUrl: imageUrl
                    });
                } catch (e) {
                    console.error('Load absence detail error:', e);
                    alert('Tidak dapat membuka detail pengajuan: ' + e.message);
                }
            }
        })();
    </script>
</div>
