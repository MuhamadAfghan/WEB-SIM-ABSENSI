<!-- Modal Tambah Karyawan -->
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="#" class="w-100">
      @csrf
      <div class="modal-content border-0 rounded-4 shadow-sm p-3">
        
        <div class="border-0 d-flex align-items-center px-2 pt-2 pb-1">
          <button type="button" class="btn-close me-1 fw-bold" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="modal-title fw-bold mb-0">Tambah Karyawan</h5>
        </div>
        <hr class="my-2">

        <div class="modal-body px-2 pb-2">
          <!-- Nama -->
          <div class="mb-1">
            <label for="nama" class="form-label fw-semibold fs-5">Nama</label>
            <input type="text" name="nama" id="nama" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-3 py-3" required>
          </div>

          <!-- NIP -->
          <div class="mb-1">
            <label for="nip" class="form-label fw-semibold fs-5">NIP</label>
            <input type="text" name="nip" id="nip" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-3 py-3" required>
          </div>

          <!-- Divisi -->
          <div class="mb-1">
            <label for="divisi" class="form-label fw-semibold fs-5">Divisi</label>
            <input type="text" name="divisi" id="divisi" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-3 py-3" required>
          </div>

          <!-- Email -->
          <div class="mb-1">
            <label for="email" class="form-label fw-semibold fs-5">Email</label>
            <input type="email" name="email" id="email" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-3 py-3" required>
          </div>

          <!-- Telepon -->
          <div class="mb-1">
            <label for="telepon" class="form-label fw-semibold fs-5">No. Telepon</label>
            <input type="text" name="telepon" id="telepon" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-4 py-3" required>
          </div>

          <!-- Password -->
          <div class="mb-1">
            <label for="password" class="form-label fw-semibold fs-5">Password</label>
            <input type="password" name="password" id="password" class="form-control rounded-3 shadow-sm border-0 bg-secondary-subtle px-3 py-3" required>
          </div>

          <!-- Tombol Simpan -->
          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary rounded-3 fw-bold py-2 fs-5" style="background-color: #58B4FF; border: none;">
              Tambah
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
