<style>
    .product-form .switch {
        position: relative;
        display: inline-block;
        width: 180px;
        height: 40px;
    }

    .product-form .switch input {
        display: none;
    }

    .product-form .slider {
        position: absolute;
        inset: 0;
        background: oklch(var(--n) / 0.2);
        border-radius: 40px;
        cursor: pointer;
        transition: 0.35s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: oklch(var(--p));
        padding: 0 12px;
    }

    /* Text content */
    .product-form .slider::after {
        content: "TANPA VARIAN";
    }

    /* Circle */
    .product-form .slider::before {
        content: "";
        position: absolute;
        width: 32px;
        height: 32px;
        background: white;
        border-radius: 50%;
        left: 4px;
        transition: 0.35s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    /* Checked State */
    .product-form input:checked + .slider {
        background: oklch(var(--p));
        color: oklch(var(--pc));
    }

    .product-form input:checked + .slider::after {
        content: "DENGAN VARIAN";
    }

    .product-form input:checked + .slider::before {
        transform: translateX(140px);
    }
</style>
<label class="switch hover:scale-105 duration-200">
    <input type="checkbox" x-model="variantMode">
    <span class="slider"></span>
</label>
