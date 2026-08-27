<div class="dropdown stock-export-dropdown">
    <button type="button"
        class="btn btn-outline-teal btn-sm dropdown-toggle stock-export-toggle {{ !empty($compact) ? 'stock-export-toggle--compact' : '' }}"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="ti-download"></i> Export
    </button>
    <div class="dropdown-menu dropdown-menu-right stock-export-menu">
        <a class="dropdown-item stock-export-option" href="#" data-format="pdf">
            <i class="icofont icofont-file-pdf" aria-hidden="true"></i>
            <span>PDF</span>
        </a>
        <a class="dropdown-item stock-export-option" href="#" data-format="excel">
            <i class="icofont icofont-file-excel" aria-hidden="true"></i>
            <span>Excel</span>
        </a>
    </div>
</div>
