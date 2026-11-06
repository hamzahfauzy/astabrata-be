(function(){
    const tableBody = document.getElementById('tableBodyPenghasilan');
    const addRowBtn = document.getElementById('addRowBtnPenghasilan');

    // Fungsi untuk memperbarui nomor urut dan status tombol hapus
    function updateRowIndices() {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
            
            // Nonaktifkan tombol hapus jika hanya tersisa 1 baris
            // const removeBtn = row.querySelector('.remove-row');
            // removeBtn.disabled = (rows.length === 1);
        });

        if(rows.length == 0)
        {
            const newRow = document.createElement('tr');
            newRow.classList.add('empty-row')
            newRow.innerHTML = `
                <td colspan="4" class="text-center py-4 text-muted">
                    Belum ada penghasilan. Klik tombol <b>"Tambah Penghasilan"</b> untuk memulai.
                </td>
            `;

            tableBody.appendChild(newRow);
        }
    }

    // Tambah baris baru
    addRowBtn.addEventListener('click', function () {
        const hasEmptyRow = tableBody.querySelector('tr.empty-row');
        if(hasEmptyRow)
        {
            hasEmptyRow.remove()
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="text-center row-number"></td>
            <td>
                <input type="text" name="income[description][]" class="form-control" placeholder="Masukkan deskripsi..." required>
            </td>
            <td>
                <input type="text" name="income[amount][]" class="form-control text-end nominal-input" placeholder="0" min="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(newRow);
        updateRowIndices();
    });

    // Event delegation untuk menghapus baris dan kalkulasi otomatis saat input berubah
    tableBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
            updateRowIndices();
        }
    });
})()