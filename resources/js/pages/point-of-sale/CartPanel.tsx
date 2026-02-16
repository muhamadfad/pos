import React from "react";
import { Link } from "@inertiajs/react";
import type { CartItem, CashierShift } from "./types";
import CartItemRow from "./CartItemRow";

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
    onUpdateQty: (productId: number, qty: number) => void;
    onRemove: (productId: number) => void;
    onClear: () => void;
    onPaidChange: (value: string) => void;
    onCheckout: () => void;
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
    onUpdateQty,
    onRemove,
    onClear,
    onPaidChange,
    onCheckout,
}: CartPanelProps) {
    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    // Format angka dengan pemisah ribuan titik untuk input
    const formatInputRupiah = (value: string): string => {
        const num = value.replace(/\D/g, "");
        if (num === "") return "";
        return Number(num).toLocaleString("id-ID");
    };

    // Parse input rupiah kembali ke angka murni
    const parseInputRupiah = (formatted: string): string => {
        return formatted.replace(/\D/g, "");
    };

    const quickPayAmounts = [20000, 50000, 100000, 200000, 500000];

    const [showExitModal, setShowExitModal] = React.useState(false);

    return (
        <>
            {/* Exit Confirmation Modal */}
            {showExitModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center animate-fade-in">
                    <div className="bg-white rounded-2xl p-6 shadow-2xl max-w-md w-full mx-4 animate-scale-in">
                        <div className="text-center">
                            {/* Warning Icon */}
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

                            {/* Message */}
                            <h3 className="text-xl font-bold text-gray-800 mb-2">
                                Keluar dari Kasir?
                            </h3>
                            <p className="text-gray-600 mb-6 text-sm">
                                Shift Anda masih aktif. Anda akan diarahkan ke
                                halaman manajemen shift. Shift tidak akan
                                ditutup otomatis.
                            </p>

                            {/* Buttons */}
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
                                    className="py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
                                >
                                    Ya, Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Success Modal Overlay */}
            {showSuccess && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center animate-fade-in">
                    <div className="bg-white rounded-2xl p-8 shadow-2xl max-w-md w-full mx-4 animate-scale-in">
                        <div className="text-center">
                            {/* Success Icon */}
                            <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg
                                    className="w-12 h-12 text-green-600"
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
                            </div>

                            {/* Success Message */}
                            <h3 className="text-2xl font-bold text-gray-800 mb-2">
                                Transaksi Berhasil!
                            </h3>
                            <p className="text-gray-600 mb-6">
                                {flash?.success}
                            </p>

                            {/* Transaction Details */}
                            <div className="bg-gray-50 rounded-xl p-4 space-y-2 mb-6">
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-600">Total</span>
                                    <span className="font-semibold">
                                        {formatRupiah(totalPrice)}
                                    </span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-600">Bayar</span>
                                    <span className="font-semibold">
                                        {formatRupiah(paidAmount)}
                                    </span>
                                </div>
                                {change > 0 && (
                                    <div className="flex justify-between text-sm pt-2 border-t border-gray-200">
                                        <span className="text-green-600 font-medium">
                                            Kembalian
                                        </span>
                                        <span className="font-bold text-green-600">
                                            {formatRupiah(change)}
                                        </span>
                                    </div>
                                )}
                            </div>

                            {/* Auto close message */}
                            <p className="text-xs text-gray-400">
                                Otomatis tutup dalam 5 detik...
                            </p>
                        </div>
                    </div>
                </div>
            )}

            <div className="w-105 bg-white border-l border-gray-200 flex flex-col shrink-0 shadow-lg">
                {/* Cart header */}
                <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-linear-to-r from-blue-600 to-blue-700">
                    <div className="flex items-center gap-3">
                        <div className="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg
                                className="w-5 h-5 text-white"
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
                            <h2 className="font-bold text-white text-base">
                                Keranjang
                            </h2>
                            <p className="text-xs text-blue-100">
                                {totalItems} item · {cart.length} produk
                            </p>
                        </div>
                    </div>
                    {cart.length > 0 && (
                        <button
                            onClick={onClear}
                            className="text-xs text-blue-100 hover:text-white font-medium bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1"
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
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                            Kosongkan
                        </button>
                    )}
                </div>

                {/* Shift Info Banner */}
                {/* Shift Info Banner */}
                {activeShift && (
                    <div className="px-4 pt-3 pb-2 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 shrink-0">
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
                        <div className="grid grid-cols-2 gap-2 text-xs">
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
                        </div>
                    </div>
                )}
                {/* Alerts */}
                {(errors || showSuccess) && (
                    <div className="px-4 pt-3 space-y-2 shrink-0">
                        {errors &&
                            Object.values(errors).map((err, i) => (
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
                        {showSuccess && (
                            <div className="text-xs text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-lg flex items-center gap-2">
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
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                {flash?.success}
                            </div>
                        )}
                    </div>
                )}

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
                                Klik produk atau scan barcode untuk menambahkan
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

                {/* Cart footer / Checkout */}
                <div className="border-t border-gray-200 shrink-0">
                    {/* Totals */}
                    <div className="px-5 pt-4 pb-3 bg-gray-50/80 space-y-1.5">
                        <div className="flex justify-between text-sm">
                            <span className="text-gray-500">
                                Subtotal ({totalItems} item)
                            </span>
                            <span className="font-medium text-gray-700">
                                {formatRupiah(totalPrice)}
                            </span>
                        </div>
                        <div className="flex justify-between items-center">
                            <span className="text-base font-bold text-gray-800">
                                Total
                            </span>
                            <span className="text-xl font-black text-blue-600">
                                {formatRupiah(totalPrice)}
                            </span>
                        </div>
                    </div>

                    {/* Payment section */}
                    <div className="px-5 py-4 space-y-3 bg-white">
                        {/* Bayar input */}
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">
                                Pembayaran
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
                                            parseInputRupiah(e.target.value),
                                        )
                                    }
                                    onKeyDown={(e) => {
                                        if (e.key === "Enter") onCheckout();
                                    }}
                                />
                            </div>
                        </div>

                        {/* Quick Pay Buttons */}
                        {cart.length > 0 && (
                            <div className="flex flex-wrap gap-1.5">
                                <button
                                    onClick={() =>
                                        onPaidChange(totalPrice.toString())
                                    }
                                    className="text-xs py-1.5 px-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-blue-600 font-semibold transition-colors"
                                >
                                    Uang Pas
                                </button>
                                {quickPayAmounts
                                    .filter((v) => v >= totalPrice)
                                    .slice(0, 3)
                                    .map((v) => (
                                        <button
                                            key={v}
                                            onClick={() =>
                                                onPaidChange(v.toString())
                                            }
                                            className="text-xs py-1.5 px-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 font-medium transition-colors"
                                        >
                                            {v >= 1000000
                                                ? `${v / 1000000}jt`
                                                : `${v / 1000}rb`}
                                        </button>
                                    ))}
                            </div>
                        )}

                        {/* Kembalian */}
                        {paidAmount > 0 && (
                            <div
                                className={`flex justify-between items-center px-4 py-2.5 rounded-xl ${
                                    change >= 0
                                        ? "bg-green-50 border border-green-200"
                                        : "bg-red-50 border border-red-200"
                                }`}
                            >
                                <span
                                    className={`text-sm font-medium ${change >= 0 ? "text-green-600" : "text-red-500"}`}
                                >
                                    {change >= 0 ? "Kembalian" : "Kurang"}
                                </span>
                                <span
                                    className={`text-lg font-black ${change >= 0 ? "text-green-700" : "text-red-600"}`}
                                >
                                    {formatRupiah(Math.abs(change))}
                                </span>
                            </div>
                        )}

                        {/* Checkout Button */}
                        <button
                            onClick={onCheckout}
                            disabled={
                                cart.length === 0 ||
                                paidAmount < totalPrice ||
                                processing
                            }
                            className="w-full py-3.5 bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm hover:shadow-md disabled:shadow-none active:scale-[0.98]"
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
                                    Bayar {formatRupiah(totalPrice)}
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}
