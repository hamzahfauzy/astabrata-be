(function(){
    // Data State Strategy
    let state = {
        items: []
    };

    // Standard Units for Dropdown / Auto-complete suggestions
    const standardUnits = ["m1", "m2", "m3", "Kg", "Bh", "Ls", "Pcs", "Org", "Set", "Titik", "Lbr"];

    /**
        * Converts numeric value to formatted IDR string (e.g. 1500000 -> "Rp 1.500.000")
        */
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    }

    /**
        * Formats unformatted number string with thousand dots for live inputs
        */
    function formatNumberWithDots(val) {
        let num = val.replace(/\D/g, "");
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    /**
        * Parses formatted number string (e.g., "1.500.000") back to raw float
        */
    function parseFormattedNumber(val) {
        if (typeof val === 'number') return val;
        if (!val) return 0;
        return parseFloat(val.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    /**
        * Converts number to Indonesian Terbilang words
        */
    function terbilang(angka) {
        angka = Math.floor(Math.abs(angka));
        const huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        
        if (angka < 12) return huruf[angka];
        if (angka < 20) return terbilang(angka - 10) + " Belas";
        if (angka < 100) return terbilang(Math.floor(angka / 10)) + " Puluh " + terbilang(angka % 10);
        if (angka < 200) return "Seratus " + terbilang(angka - 100);
        if (angka < 1000) return terbilang(Math.floor(angka / 100)) + " Ratus " + terbilang(angka % 100);
        if (angka < 2000) return "Seribu " + terbilang(angka - 1000);
        if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + " Ribu " + terbilang(angka % 1000);
        if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + " Juta " + terbilang(angka % 1000000);
        if (angka < 1000000000000) return terbilang(Math.floor(angka / 1000000000)) + " Miliar " + terbilang(angka % 1000000000);
        if (angka < 1000000000000000) return terbilang(Math.floor(angka / 1000000000000)) + " Triliun " + terbilang(angka % 1000000000000);
        
        return "Angka Terlalu Besar";
    }

    /**
        * Main render engine for the RAB Table
        */
    function renderTable() {
        const tbody = document.getElementById('rabTableBody');
        tbody.innerHTML = '';

        if (state.items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Belum ada item. Klik tombol <b>"Tambah Item"</b> untuk memulai.
                    </td>
                </tr>`;
            calculateTotals();
            return;
        }

        state.items.forEach((item, itemIdx) => {
            const rowTotal = (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
            const itemRow = document.createElement('tr');
            
            itemRow.innerHTML = `
                <td class="text-center text-muted">${itemIdx + 1}</td>
                <td>
                    <input type="text" name="budgets[description][]" class="form-control input-plain" value="${escapeHtml(item.description)}" placeholder="Uraian...">
                </td>
                <td>
                    <input type="number"  name="budgets[qty][]" step="any" min="0" class="form-control input-plain text-end" value="${item.qty}" oninput="rab.updateItem(${itemIdx}, 'quantity', this.value)">
                </td>
                <td>
                    <input type="text"  name="budgets[unit][]" class="form-control input-plain text-center" value="${escapeHtml(item.unit)}" placeholder="m2 / pcs">
                </td>
                <td>
                    <div class="input-group currency-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control input-plain text-end" name="budgets[price][]" value="${formatNumberWithDots(Math.round(item.price).toString())}" oninput="rab.handlePriceInput(${itemIdx}, this)">
                    </div>
                </td>
                <td class="text-end fw-semibold text-dark">
                    ${formatRupiah(rowTotal)}
                </td>
                <td class="text-center no-print">
                    <button class="btn btn-sm btn-link text-danger p-0" type="button" onclick="rab.deleteItem(${itemIdx})" title="Hapus Baris">
                        <i class="bi bi-trash fs-6"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(itemRow);
        });

        updateDatalist();
        calculateTotals();
    }

    /**
        * Handle Real-time Price Input Formatting
        */
    function handlePriceInput(itemIdx, inputEl) {
        let rawValue = inputEl.value;
        let formatted = formatNumberWithDots(rawValue);
        inputEl.value = formatted;

        let rawNumeric = parseFormattedNumber(formatted);
        state.items[itemIdx].price = rawNumeric;

        calculateTotals();
        
        const item = state.items[itemIdx];
        const rowTotal = (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
        
        const tr = inputEl.closest('tr');
        if (tr && tr.children[5]) {
            tr.children[5].textContent = formatRupiah(rowTotal);
        }
    }

    /**
     * Update Item Properties
     */
    function updateItem(itemIdx, field, value) {
        if (field === 'quantity') {
            state.items[itemIdx].qty = parseFloat(value) || 0;
        } else {
            state.items[itemIdx][field] = value;
        }

        if (field === 'quantity') {
            const item = state.items[itemIdx];
            const tr = event ? event.target.closest('tr') : null;
            if (tr && tr.children[5]) {
                tr.children[5].textContent = formatRupiah((parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0));
            }
        }

        calculateTotals();
    }

    function addItem() {
        state.items.push({
            description: "",
            qty: 1,
            unit: "m2",
            price: 0
        });
        renderTable();
    }

    function deleteItem(itemIdx) {
        state.items.splice(itemIdx, 1);
        renderTable();
        // showToast("Item dihapus.");
    }

    /**
     * Compute Subtotal, Tax, and Grand Total
     */
    function calculateTotals() {
        let subtotal = 0;

        state.items.forEach(item => {
            const rowTotal = (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
            subtotal += rowTotal;
        });

        const isTaxEnabled = false;
        const taxAmount = isTaxEnabled ? (subtotal * 0.11) : 0;
        const grandTotal = subtotal + taxAmount;

        // document.getElementById('subtotalDisplay').textContent = formatRupiah(subtotal);
        // document.getElementById('taxDisplay').textContent = formatRupiah(taxAmount);
        document.getElementById('grandTotalDisplay').textContent = formatRupiah(grandTotal);

        const terbilangText = grandTotal > 0 ? (terbilang(grandTotal) + " Rupiah") : "Nol Rupiah";
        document.getElementById('terbilangDisplay').textContent = terbilangText;

        // const authorVal = document.getElementById('projectAuthor').value;
        // document.getElementById('sigAuthor').textContent = authorVal || "Estimator";
    }

    function clearAllData() {
        if (confirm("Apakah Anda yakin ingin mengosongkan semua data RAB ini?")) {
            state.items = [];
            renderTable();
            showToast("Semua data telah dikosongkan.");
        }
    }

    function updateDatalist() {
        let datalist = document.getElementById('unitsList');
        if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = 'unitsList';
            document.body.appendChild(datalist);
        }
        datalist.innerHTML = standardUnits.map(u => `<option value="${u}">`).join('');
    }

    function escapeHtml(text) {
        if (!text) return "";
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function showToast(msg) {
        document.getElementById('toastMessage').textContent = msg;
        const toastEl = document.getElementById('actionToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
        toast.show();
    }

    function loadFromData(data){
        state.items = data
        renderTable();
    }

    window.rab = {
        addItem,
        handlePriceInput,
        updateItem,
        deleteItem,
        loadFromData,
    }

    // Initialize App
    // window.onload = function() {
    //     loadFromLocalStorage();
    // };

})()