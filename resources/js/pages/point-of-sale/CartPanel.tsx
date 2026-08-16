import React from "react";
import type { CartItem, CashierShift, Customer } from "./types";
import CartItemRow from "./CartItemRow";
import { formatRupiah } from "./utils";
import CustomerCombobox from "../../components/CustomerCombobox";

interface CartPanelProps {
    cart: CartItem[];
    totalItems: number;
    totalPrice: number;
    paid: string;
    paidAmount: number;
    change: number;
    processing: boolean;
    errors?: Record<string, string>;
    flash?: { success?: string };
    showSuccess: boolean;
    paidRef: React.RefObject<HTMLInputElement | null>;
    activeShift: CashierShift | null;
    customers: Customer[];
    selectedCustomerId: number | null;
    onUpdateQty: (productId: number, qty: number) => void;
    onRemove: (productId: number) => void;
    onClear: () => void;
    onPaidChange: (value: string) => void;
    onCustomerChange: (customerId: number | null) => void;
    onCheckout: (
        paymentType: "lunas" | "partial" | "kasbon",
        paymentMethod: "cash" | "transfer",
    ) => void;
    onCreateCustomer?: (name: string) => void;
}

export default function CartPanel({
    cart,
    totalItems,
    totalPrice,
    paid,
    paidAmount,
    change,
    processing,
    errors,
    flash,
    showSuccess,
    paidRef,
    activeShift,
    customers,
    selectedCustomerId,
    onUpdateQty,
    onRemove,
    onClear,
    onPaidChange,
    onCustomerChange,
    onCheckout,
    onCreateCustomer,
}: CartPanelProps) {
    // "cart" | "payment"
    const [step, setStep] = React.useState<"cart" | "payment">("cart");
    const [showExitModal, setShowExitModal] = React.useState(false);
    const [paymentType, setPaymentType] = React.useState<
        "lunas" | "partial" | "kasbon"
    >("lunas");
    const [paymentMethod, setPaymentMethod] = React.useState<
        "cash" | "transfer"
    >("cash");

    const hasCustomer = selectedCustomerId !== null;
    const canKasbon = hasCustomer;
    const canPartial = hasCustomer;

    // Format angka dengan pemisah ribuan titik untuk input
    const formatInputRupiah = (value: string): string => {
        const num = value.replace(/\D/g, "");
        if (num === "") return "";
        return Number(num).toLocaleString("id-ID");
    };
    const parseInputRupiah = (formatted: string): string => {
        return formatted.replace(/\D/g, "");
    };

    const quickPayAmounts = [20000, 50000, 100000, 200000, 500000];

    // Saat checkout selesai sukses, kembali ke step cart
    React.useEffect(() => {
        if (showSuccess) {
            setStep("cart");
            setPaymentType("lunas");
            setPaymentMethod("cash");
            onCustomerChange(null);
        }
    }, [showSuccess]);

    // Reset ke lunas kalau customer dicopot dan tipe tidak bisa lagi
    React.useEffect(() => {
        if (
            !selectedCustomerId &&
            (paymentType === "kasbon" || paymentType === "partial")
        ) {
            setPaymentType("lunas");
        }
    }, [selectedCustomerId]);

    return (
        <>
            {/* Exit Confirmation Modal */}
            {showExitModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                    <div className="bg-white rounded-2xl p-6 shadow-2xl max-w-md w-full mx-4">
                        <div className="text-center">
                            <div className="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg
                                    className="w-10 h-10 text-yellow-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                            </div>
                            <h3 className="text-xl font-bold text-gray-800 mb-2">
                                Keluar dari Kasir?
                            </h3>
                            <p className="text-gray-600 mb-6 text-sm">
                                Shift Anda masih aktif. Anda akan diarahkan ke
                                halaman manajemen shift.
                            </p>
                            <div className="grid grid-cols-2 gap-3">
                                <button
                                    onClick={() => setShowExitModal(false)}
                                    className="py-2.5 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    onClick={() =>
                                        (window.location.href = "/pos/shift")
                                    }
                                    className="py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all"
                                >
                                    Ya, Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <div className="flex-1 min-h-0 flex flex-col bg-white overflow-hidden h-full">
                {/* Toast sukses â€” inline banner */}
                {showSuccess && (
                    <div className="shrink-0 mx-4 mt-3 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
                        <svg
                            className="w-5 h-5 text-green-600 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-bold">
                                Transaksi Berhasil!
                            </p>
                            {flash?.success && (
                                <p className="text-xs text-green-700 mt-0.5">
                                    {flash.success}
                                </p>
                            )}
                        </div>
                    </div>
                )}

                {/* â”€â”€ SHIFT BANNER â”€â”€ */}
                {activeShift && (
                    <div className="px-4 pt-3 pb-2 bg-linear-to-r from-green-50 to-emerald-50 border-b border-green-100 shrink-0">
                        <div className="flex items-center justify-between mb-2">
                            <div className="flex items-center gap-2">
                                <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span className="text-xs font-semibold text-green-700">
                                    Shift Aktif
                                </span>
                            </div>
                            <button
                                onClick={() => setShowExitModal(true)}
                                className="text-xs text-red-600 hover:text-red-700 font-medium px-2 py-1 hover:bg-red-50 rounded transition-colors"
                            >
                                Tutup Kasir
                            </button>
                        </div>
                        <div className="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <div className="text-green-600">Modal Awal</div>
                                <div className="font-semibold text-gray-800">
                                    {formatRupiah(activeShift.opening_balance)}
                                </div>
                            </div>
                            <div>
                                <div className="text-green-600">
                                    Total Penjualan
                                </div>
                                <div className="font-semibold text-gray-800">
                                    {formatRupiah(activeShift.total_sales)}
                                </div>
                            </div>
                            <div>
                                <div className="text-blue-600">
                                    Total Transfer
                                </div>
                                <div className="font-semibold text-gray-800">
                                    {formatRupiah(activeShift.total_transfer || 0)}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* â”€â”€ HEADER â”€â”€ */}
                <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg
                                className="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h2 className="font-extrabold text-gray-900 text-base tracking-tight">
                                {step === "cart" ? "Pesanan Saat Ini" : "Pembayaran"}
                            </h2>
                            <p className="text-xs text-gray-400 font-medium mt-0.5">
                                {step === "cart"
                                    ? `${totalItems} item Â· ${cart.length} produk`
                                    : "Selesaikan Transaksi"}
                            </p>
                        </div>
                    </div>
                    {/* Step back / clear */}
                    <div className="flex items-center gap-2">
                        {step === "payment" ? (
                            <button
                                onClick={() => setStep("cart")}
                                className="text-xs text-gray-500 hover:text-gray-900 font-bold bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5 border border-gray-200"
                            >
                                <svg
                                    className="w-3.5 h-3.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                                Kembali
                            </button>
                        ) : cart.length > 0 ? (
                            <button
                                onClick={onClear}
                                className="text-xs text-red-500 hover:text-red-700 font-bold bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5 border border-red-100"
                            >
                                <svg
                                    className="w-3.5 h-3.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Kosongkan
                            </button>
                        ) : null}
                    </div>
                </div>

                {/* â”€â”€ ERROR ALERTS â”€â”€ */}
                {errors && Object.keys(errors).length > 0 && (
                    <div className="px-4 pt-3 space-y-2 shrink-0">
                        {Object.values(errors).map((err, i) => (
                            <div
                                key={i}
                                className="text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-2"
                            >
                                <svg
                                    className="w-4 h-4 shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                {err}
                            </div>
                        ))}
                    </div>
                )}

                {/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                    STEP 1: CART
                â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */}
                {step === "cart" && (
                    <>
                        {/* Cart items */}
                        <div className="flex-1 overflow-y-auto custom-scroll">
                            {cart.length === 0 ? (
                                <div className="flex flex-col items-center justify-center h-full text-gray-300 px-4">
                                    <svg
                                        className="w-20 h-20 mb-3"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={1}
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"
                                        />
                                    </svg>
                                    <p className="text-sm font-medium text-gray-400">
                                        Keranjang masih kosong
                                    </p>
                                    <p className="text-xs text-gray-300 mt-1">
                                        Klik produk atau scan barcode untuk
                                        menambahkan
                                    </p>
                                </div>
                            ) : (
                                <div className="divide-y divide-gray-100">
                                    {cart.map((item, i) => (
                                        <CartItemRow
                                            key={item.product_id}
                                            item={item}
                                            index={i + 1}
                                            onUpdateQty={onUpdateQty}
                                            onRemove={onRemove}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Cart footer */}
                        <div className="border-t border-gray-100 shrink-0 bg-white">
                            <div className="px-6 pt-5 pb-4 space-y-2">
                                <div className="flex justify-between text-sm font-medium">
                                    <span className="text-gray-500">
                                        Subtotal <span className="text-gray-400">({totalItems} item)</span>
                                    </span>
                                    <span className="text-gray-800">
                                        {formatRupiah(totalPrice)}
                                    </span>
                                </div>
                                <div className="flex justify-between items-end pt-2 border-t border-dashed border-gray-200">
                                    <span className="text-sm font-bold text-gray-500 uppercase tracking-wider">
                                        Total Bayar
                                    </span>
                                    <span className="text-3xl font-black text-gray-900 tracking-tighter">
                                        {formatRupiah(totalPrice)}
                                    </span>
                                </div>
                            </div>
                            <div className="px-6 py-5 border-t border-gray-50">
                                <button
                                    onClick={() => setStep("payment")}
                                    disabled={cart.length === 0}
                                    className="w-full py-4 bg-gray-900 hover:bg-gray-800 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-2xl transition-all flex items-center justify-center gap-2 shadow-xl shadow-gray-900/10 active:scale-[0.98] text-base"
                                >
                                    <svg
                                        className="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                    Lanjut ke Pembayaran
                                    <svg
                                        className="w-4 h-4 ml-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </>
                )}

                {/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                    STEP 2: PAYMENT
                â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */}
                {step === "payment" && (
                    <>
                        {/* Konten scrollable */}
                        <div className="flex-1 overflow-y-auto custom-scroll">
                            <div className="p-4 space-y-4">
                                {/* Order Summary */}
                                <div className="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                                    <div className="px-4 py-2.5 bg-gray-100 border-b border-gray-200">
                                        <p className="text-xs font-bold text-gray-600 uppercase tracking-wide">
                                            Ringkasan Pesanan
                                        </p>
                                    </div>
                                    <div className="divide-y divide-gray-100 max-h-36 overflow-y-auto">
                                        {cart.map((item) => (
                                            <div
                                                key={item.product_id}
                                                className="flex items-center justify-between px-4 py-2 text-sm"
                                            >
                                                <span className="text-gray-700 truncate flex-1 mr-2">
                                                    {item.name}
                                                </span>
                                                <span className="text-gray-400 shrink-0 text-xs mr-2">
                                                    Ã—{item.qty}
                                                </span>
                                                <span className="font-semibold text-gray-800 shrink-0">
                                                    {formatRupiah(
                                                        item.price * item.qty,
                                                    )}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="flex justify-between items-center px-4 py-3 border-t border-gray-200 bg-white">
                                        <span className="font-bold text-gray-800">
                                            Total
                                        </span>
                                        <span className="text-xl font-black text-blue-600">
                                            {formatRupiah(totalPrice)}
                                        </span>
                                    </div>
                                </div>

                                {/* Customer Selection */}
                                <CustomerCombobox
                                    customers={customers ?? []}
                                    value={selectedCustomerId}
                                    onChange={onCustomerChange}
                                    label="Customer"
                                    placeholder="Cari nama atau no. HP customer..."
                                    allowWalkIn={true}
                                    onCreateCustomer={onCreateCustomer}
                                />

                                {/* Payment Type */}
                                <div>
                                    <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">
                                        Tipe Pembayaran
                                    </label>
                                    <div className="grid grid-cols-3 gap-2">
                                        {/* Lunas */}
                                        <button
                                            onClick={() => {
                                                setPaymentType("lunas");
                                                onPaidChange(
                                                    totalPrice.toString(),
                                                );
                                            }}
                                            className={`py-2 px-2 rounded-xl border-2 text-xs font-bold transition-all flex flex-col items-center gap-0.5 ${
                                                paymentType === "lunas"
                                                    ? "border-green-500 bg-green-50 text-green-700"
                                                    : "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
                                            }`}
                                        >
                                            <svg className="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /></svg>
                                            Lunas
                                        </button>
                                        {/* Partial */}
                                        <button
                                            onClick={() => {
                                                if (canPartial) {
                                                    setPaymentType("partial");
                                                    onPaidChange("");
                                                }
                                            }}
                                            disabled={!canPartial}
                                            title={
                                                !canPartial
                                                    ? "Pilih customer terlebih dahulu"
                                                    : ""
                                            }
                                            className={`py-2 px-2 rounded-xl border-2 text-xs font-bold transition-all flex flex-col items-center gap-0.5 ${
                                                paymentType === "partial"
                                                    ? "border-yellow-500 bg-yellow-50 text-yellow-700"
                                                    : canPartial
                                                      ? "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
                                                      : "border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed"
                                            }`}
                                        >
                                            <svg className="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Sebagian
                                        </button>
                                        {/* Kasbon */}
                                        <button
                                            onClick={() => {
                                                if (canKasbon) {
                                                    setPaymentType("kasbon");
                                                    onPaidChange("0");
                                                }
                                            }}
                                            disabled={!canKasbon}
                                            title={
                                                !canKasbon
                                                    ? "Pilih customer terlebih dahulu"
                                                    : ""
                                            }
                                            className={`py-2 px-2 rounded-xl border-2 text-xs font-bold transition-all flex flex-col items-center gap-0.5 ${
                                                paymentType === "kasbon"
                                                    ? "border-orange-500 bg-orange-50 text-orange-700"
                                                    : canKasbon
                                                      ? "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
                                                      : "border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed"
                                            }`}
                                        >
                                            <svg className="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                            Kasbon
                                        </button>
                                    </div>
                                    {!hasCustomer && (
                                        <p className="text-[10px] text-amber-600 mt-1.5 flex items-center gap-1">
                                            <svg
                                                className="w-3 h-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            Pilih customer untuk opsi Sebagian /
                                            Kasbon
                                        </p>
                                    )}
                                </div>

                                {/* Metode Pembayaran */}
                                {paymentType !== "kasbon" && (
                                    <div>
                                        <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">
                                            Metode Pembayaran
                                        </label>
                                        <div className="grid grid-cols-2 gap-2">
                                            {(
                                                ["cash", "transfer"] as const
                                            ).map((m) => (
                                                <button
                                                    key={m}
                                                    onClick={() =>
                                                        setPaymentMethod(m)
                                                    }
                                                    className={`py-2.5 rounded-xl border-2 text-sm font-bold transition-all ${
                                                        paymentMethod === m
                                                            ? "border-blue-500 bg-blue-50 text-blue-700"
                                                            : "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
                                                    }`}
                                                >
                                                    {m === "cash"
                                                        ? <span className="flex items-center justify-center gap-1.5"><svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Tunai</span>
                                                        : <span className="flex items-center justify-center gap-1.5"><svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Transfer</span>}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* Bayar Input â€” hanya jika bukan kasbon */}
                                {paymentType !== "kasbon" && (
                                    <div>
                                        <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                            <svg
                                                className="w-3.5 h-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                                />
                                            </svg>
                                            {paymentType === "partial"
                                                ? "Uang Muka / DP"
                                                : "Jumlah Pembayaran"}
                                        </label>
                                        <div className="relative">
                                            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">
                                                Rp
                                            </span>
                                            <input
                                                ref={paidRef}
                                                type="text"
                                                inputMode="numeric"
                                                placeholder="0"
                                                className="w-full pl-10 pr-3 py-3 text-right text-xl font-black border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                                                value={formatInputRupiah(paid)}
                                                onChange={(e) =>
                                                    onPaidChange(
                                                        parseInputRupiah(
                                                            e.target.value,
                                                        ),
                                                    )
                                                }
                                                onKeyDown={(e) => {
                                                    if (e.key === "Enter")
                                                        onCheckout(
                                                            paymentType,
                                                            paymentMethod,
                                                        );
                                                }}
                                                autoFocus
                                            />
                                        </div>

                                        {/* Quick Pay */}
                                        <div className="flex flex-wrap gap-1.5 mt-2">
                                            {paymentType === "lunas" && (
                                                <button
                                                    onClick={() =>
                                                        onPaidChange(
                                                            totalPrice.toString(),
                                                        )
                                                    }
                                                    className="text-xs py-1.5 px-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-blue-600 font-semibold transition-colors"
                                                >
                                                    Uang Pas
                                                </button>
                                            )}
                                            {paymentType === "lunas" &&
                                                quickPayAmounts
                                                    .filter(
                                                        (v) => v >= totalPrice,
                                                    )
                                                    .slice(0, 3)
                                                    .map((v) => (
                                                        <button
                                                            key={v}
                                                            onClick={() =>
                                                                onPaidChange(
                                                                    v.toString(),
                                                                )
                                                            }
                                                            className="text-xs py-1.5 px-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-medium transition-colors"
                                                        >
                                                            {v >= 1000000
                                                                ? `${v / 1000000}jt`
                                                                : `${v / 1000}rb`}
                                                        </button>
                                                    ))}
                                            {paymentType === "partial" &&
                                                [
                                                    Math.round(
                                                        totalPrice * 0.25,
                                                    ),
                                                    Math.round(
                                                        totalPrice * 0.5,
                                                    ),
                                                    Math.round(
                                                        totalPrice * 0.75,
                                                    ),
                                                ].map((v) => (
                                                    <button
                                                        key={v}
                                                        onClick={() =>
                                                            onPaidChange(
                                                                v.toString(),
                                                            )
                                                        }
                                                        className="text-xs py-1.5 px-3 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg text-yellow-700 font-medium transition-colors"
                                                    >
                                                        {v >= 1000000
                                                            ? `${(v / 1000000).toFixed(1)}jt`
                                                            : `${Math.round(v / 1000)}rb`}
                                                    </button>
                                                ))}
                                        </div>
                                    </div>
                                )}

                                {/* Kasbon info */}
                                {paymentType === "kasbon" && (
                                    <div className="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
                                        <p className="text-sm font-bold text-orange-800 mb-1 flex items-center gap-1.5">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg> Seluruh tagihan jadi kasbon
                                        </p>
                                        <p className="text-xs text-orange-600">
                                            Total{" "}
                                            <span className="font-black">
                                                {formatRupiah(totalPrice)}
                                            </span>{" "}
                                            akan dicatat sebagai hutang customer
                                            dan bisa dibayar nanti melalui menu{" "}
                                            <strong>Bayar Kasbon</strong>.
                                        </p>
                                    </div>
                                )}

                                {/* Partial info & sisa */}
                                {paymentType === "partial" &&
                                    paidAmount > 0 && (
                                        <div className="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 space-y-1 text-sm">
                                            <div className="flex justify-between">
                                                <span className="text-gray-500">
                                                    Total
                                                </span>
                                                <span className="font-semibold">
                                                    {formatRupiah(totalPrice)}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-gray-500">
                                                    Dibayar sekarang
                                                </span>
                                                <span className="font-semibold text-green-600">
                                                    {formatRupiah(paidAmount)}
                                                </span>
                                            </div>
                                            <div className="flex justify-between pt-1 border-t border-yellow-200">
                                                <span className="font-bold text-gray-700">
                                                    Sisa (jadi kasbon)
                                                </span>
                                                <span className="font-black text-orange-600">
                                                    {formatRupiah(
                                                        Math.max(
                                                            0,
                                                            totalPrice -
                                                                paidAmount,
                                                        ),
                                                    )}
                                                </span>
                                            </div>
                                        </div>
                                    )}

                                {/* Kembalian â€” hanya jika lunas dan ada uang lebih */}
                                {paymentType === "lunas" && paidAmount > 0 && (
                                    <div
                                        className={`flex justify-between items-center px-4 py-3 rounded-xl ${change >= 0 ? "bg-green-50 border border-green-200" : "bg-red-50 border border-red-200"}`}
                                    >
                                        <span
                                            className={`text-sm font-medium ${change >= 0 ? "text-green-600" : "text-red-500"}`}
                                        >
                                            {change >= 0
                                                ? "Kembalian"
                                                : "Kurang"}
                                        </span>
                                        <span
                                            className={`text-lg font-black ${change >= 0 ? "text-green-700" : "text-red-600"}`}
                                        >
                                            {formatRupiah(Math.abs(change))}
                                        </span>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Tombol Bayar â€” sticky di bawah */}
                        <div className="shrink-0 p-4 border-t border-gray-200 bg-white">
                            <button
                                onClick={() =>
                                    onCheckout(paymentType, paymentMethod)
                                }
                                disabled={
                                    (paymentType === "lunas"
                                        ? paidAmount < totalPrice
                                        : paymentType === "partial"
                                          ? paidAmount <= 0 ||
                                            paidAmount >= totalPrice
                                          : false) /* kasbon: always enabled */ ||
                                    processing
                                }
                                className="w-full py-4 bg-gray-900 hover:bg-gray-800 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-2xl transition-all flex items-center justify-center gap-2 text-base shadow-xl shadow-gray-900/10 active:scale-[0.98]"
                            >
                                {processing ? (
                                    <>
                                        <svg
                                            className="w-5 h-5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                className="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                strokeWidth="4"
                                            />
                                            <path
                                                className="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                            />
                                        </svg>
                                        Memproses...
                                    </>
                                ) : (
                                    <>
                                        <svg
                                            className="w-5 h-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        {paymentType === "kasbon"
                                            ? `Catat Kasbon ${formatRupiah(totalPrice)}`
                                            : paymentType === "partial"
                                              ? `Bayar DP ${paidAmount > 0 ? formatRupiah(paidAmount) : "..."}`
                                              : `Konfirmasi Bayar ${formatRupiah(totalPrice)}`}
                                    </>
                                )}
                            </button>
                        </div>
                    </>
                )}
            </div>
        </>
    );
}
