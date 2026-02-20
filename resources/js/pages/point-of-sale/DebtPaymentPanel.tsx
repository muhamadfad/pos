import React, { useState } from "react";
import { router } from "@inertiajs/react";
import type { CustomerDebt } from "./types";
import { formatRupiah } from "./utils";

interface DebtPaymentPanelProps {
    customerDebts: CustomerDebt[];
    flash?: { debt_success?: string };
    errors?: Record<string, string>;
}

export default function DebtPaymentPanel({
    customerDebts,
    flash,
    errors,
}: DebtPaymentPanelProps) {
    const [search, setSearch] = useState("");
    const [selectedCustomer, setSelectedCustomer] =
        useState<CustomerDebt | null>(null);
    const [paymentAmount, setPaymentAmount] = useState("");
    const [paymentMethod, setPaymentMethod] = useState<"cash" | "transfer">(
        "cash",
    );
    const [loading, setLoading] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);

    // Format input rupiah
    const formatInputRupiah = (value: string): string => {
        const num = value.replace(/\D/g, "");
        if (num === "") return "";
        return Number(num).toLocaleString("id-ID");
    };
    const parseInput = (formatted: string): string =>
        formatted.replace(/\D/g, "");

    const filteredCustomers = customerDebts.filter(
        (c) =>
            search.trim() === "" ||
            c.name.toLowerCase().includes(search.toLowerCase()) ||
            (c.phone ?? "").includes(search),
    );

    function selectCustomer(customer: CustomerDebt) {
        setSelectedCustomer(customer);
        setPaymentAmount("");
    }

    const totalDebt = selectedCustomer?.total_debt ?? 0;
    const paidAmount = parseFloat(parseInput(paymentAmount)) || 0;

    function handlePay() {
        if (!selectedCustomer || paidAmount <= 0) return;

        setLoading(true);
        router.post(
            "/pos/pay-debt",
            {
                customer_id: selectedCustomer.id,
                amount: paidAmount,
                payment_method: paymentMethod,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setShowSuccess(true);
                    setPaymentAmount("");
                    setSelectedCustomer(null);
                    setSearch("");
                    setPaymentMethod("cash");
                    setTimeout(() => setShowSuccess(false), 3000);
                },
                onFinish: () => setLoading(false),
            },
        );
    }

    return (
        <div className="flex flex-col h-full">
            {/* Toast sukses — inline banner, bukan modal overlay */}
            {showSuccess && (
                <div className="shrink-0 mx-4 mt-3 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl animate-pulse">
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
                    <p className="text-sm font-semibold">
                        Pembayaran kasbon berhasil diproses!
                    </p>
                </div>
            )}

            {/* Error */}
            {errors && Object.keys(errors).length > 0 && (
                <div className="px-4 pt-3 shrink-0">
                    {Object.values(errors).map((err, i) => (
                        <div
                            key={i}
                            className="text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-2 mb-2"
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

            <div className="flex-1 overflow-y-auto custom-scroll p-4 space-y-4">
                {/* Step 1: Cari Customer */}
                {!selectedCustomer && (
                    <div>
                        <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">
                            Cari Customer
                        </label>
                        <div className="relative mb-3">
                            <svg
                                className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                            <input
                                type="text"
                                placeholder="Nama atau no. HP customer..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition-colors"
                                autoFocus
                            />
                        </div>

                        {/* Daftar customer */}
                        <div className="space-y-2 max-h-[400px] overflow-y-auto">
                            {filteredCustomers.length === 0 ? (
                                <p className="text-center text-sm text-gray-400 py-8">
                                    {customerDebts.length === 0
                                        ? "Tidak ada customer yang punya hutang"
                                        : "Customer tidak ditemukan"}
                                </p>
                            ) : (
                                filteredCustomers.map((c) => (
                                    <button
                                        key={c.id}
                                        onClick={() => selectCustomer(c)}
                                        className="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-orange-50 border border-gray-200 hover:border-orange-300 rounded-xl transition-all text-left group"
                                    >
                                        <div>
                                            <p className="font-semibold text-gray-800 text-sm group-hover:text-orange-700">
                                                {c.name}
                                            </p>
                                            {c.phone && (
                                                <p className="text-xs text-gray-400">
                                                    {c.phone}
                                                </p>
                                            )}
                                            <p className="text-xs text-orange-500 font-semibold mt-0.5">
                                                Hutang:{" "}
                                                {formatRupiah(c.total_debt)}
                                            </p>
                                        </div>
                                        <svg
                                            className="w-4 h-4 text-gray-300 group-hover:text-orange-400"
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
                                ))
                            )}
                        </div>
                    </div>
                )}

                {/* Step 2: Detail Hutang & Bayar */}
                {selectedCustomer && (
                    <>
                        {/* Customer info + tombol ganti */}
                        <div className="flex items-center justify-between bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
                            <div>
                                <p className="font-bold text-orange-800 text-sm">
                                    {selectedCustomer.name}
                                </p>
                                <p className="text-xs text-orange-500 mt-0.5">
                                    Total hutang:{" "}
                                    <span className="font-black">
                                        {formatRupiah(totalDebt)}
                                    </span>
                                </p>
                            </div>
                            <button
                                onClick={() => {
                                    setSelectedCustomer(null);
                                    setPaymentAmount("");
                                }}
                                className="text-xs text-orange-600 hover:text-orange-800 font-semibold bg-orange-100 hover:bg-orange-200 px-3 py-1.5 rounded-lg transition-colors"
                            >
                                Ganti
                            </button>
                        </div>

                        {/* Daftar hutang per order (FIFO) */}
                        <div>
                            <p className="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Daftar Tagihan (Terlama → Terbaru)
                            </p>
                            {selectedCustomer.orders.length === 0 ? (
                                <div className="text-center py-6 bg-green-50 border border-green-200 rounded-xl">
                                    <p className="text-green-600 font-semibold text-sm">
                                        ✓ Tidak ada hutang
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-2">
                                    {selectedCustomer.orders.map(
                                        (order, idx) => (
                                            <div
                                                key={order.id}
                                                className="bg-white border border-gray-200 rounded-xl overflow-hidden"
                                            >
                                                <div className="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-100">
                                                    <div className="flex items-center gap-2">
                                                        <span className="w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">
                                                            {idx + 1}
                                                        </span>
                                                        <span className="text-xs font-bold text-gray-600">
                                                            {order.invoice_number ??
                                                                `#${order.id}`}
                                                        </span>
                                                    </div>
                                                    <span className="text-[10px] text-gray-400">
                                                        {new Date(
                                                            order.date,
                                                        ).toLocaleDateString(
                                                            "id-ID",
                                                            {
                                                                day: "2-digit",
                                                                month: "short",
                                                                year: "numeric",
                                                            },
                                                        )}
                                                    </span>
                                                </div>
                                                <div className="grid grid-cols-3 px-3 py-2 text-xs gap-2">
                                                    <div>
                                                        <p className="text-gray-400">
                                                            Total
                                                        </p>
                                                        <p className="font-semibold text-gray-700">
                                                            {formatRupiah(
                                                                order.total_amount,
                                                            )}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p className="text-gray-400">
                                                            Terbayar
                                                        </p>
                                                        <p className="font-semibold text-green-600">
                                                            {formatRupiah(
                                                                order.total_paid,
                                                            )}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p className="text-gray-400">
                                                            Sisa
                                                        </p>
                                                        <p className="font-black text-red-600">
                                                            {formatRupiah(
                                                                order.remaining_receivable,
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ),
                                    )}
                                </div>
                            )}
                        </div>

                        {/* Input pembayaran */}
                        {selectedCustomer.orders.length > 0 && (
                            <>
                                <div>
                                    <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">
                                        Jumlah Pembayaran
                                    </label>
                                    <div className="relative">
                                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">
                                            Rp
                                        </span>
                                        <input
                                            type="text"
                                            inputMode="numeric"
                                            placeholder="0"
                                            value={formatInputRupiah(
                                                paymentAmount,
                                            )}
                                            onChange={(e) =>
                                                setPaymentAmount(
                                                    parseInput(e.target.value),
                                                )
                                            }
                                            className="w-full pl-10 pr-3 py-3 text-right text-xl font-black border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition-colors"
                                            autoFocus
                                        />
                                    </div>
                                    {/* Quick buttons */}
                                    <div className="flex flex-wrap gap-1.5 mt-2">
                                        <button
                                            onClick={() =>
                                                setPaymentAmount(
                                                    totalDebt.toString(),
                                                )
                                            }
                                            className="text-xs py-1.5 px-3 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg text-orange-600 font-semibold transition-colors"
                                        >
                                            Lunas Semua
                                        </button>
                                        {[50000, 100000, 200000, 500000]
                                            .filter((v) => v <= totalDebt)
                                            .slice(0, 3)
                                            .map((v) => (
                                                <button
                                                    key={v}
                                                    onClick={() =>
                                                        setPaymentAmount(
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
                                    </div>
                                </div>

                                {/* Sisa setelah bayar */}
                                {paidAmount > 0 && (
                                    <div className="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 space-y-1.5 text-sm">
                                        <div className="flex justify-between">
                                            <span className="text-gray-500">
                                                Total Hutang
                                            </span>
                                            <span className="font-semibold text-gray-700">
                                                {formatRupiah(totalDebt)}
                                            </span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-500">
                                                Dibayar
                                            </span>
                                            <span className="font-semibold text-green-600">
                                                {formatRupiah(paidAmount)}
                                            </span>
                                        </div>
                                        <div className="flex justify-between pt-1.5 border-t border-gray-200">
                                            <span className="font-bold text-gray-700">
                                                Sisa Hutang
                                            </span>
                                            <span
                                                className={`font-black text-base ${totalDebt - paidAmount <= 0 ? "text-green-600" : "text-red-600"}`}
                                            >
                                                {formatRupiah(
                                                    Math.max(
                                                        0,
                                                        totalDebt - paidAmount,
                                                    ),
                                                )}
                                            </span>
                                        </div>
                                    </div>
                                )}

                                {/* Metode bayar */}
                                <div>
                                    <label className="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2 block">
                                        Metode Pembayaran
                                    </label>
                                    <div className="grid grid-cols-2 gap-2">
                                        {(["cash", "transfer"] as const).map(
                                            (m) => (
                                                <button
                                                    key={m}
                                                    onClick={() =>
                                                        setPaymentMethod(m)
                                                    }
                                                    className={`py-2.5 rounded-xl border-2 text-sm font-bold transition-all ${
                                                        paymentMethod === m
                                                            ? "border-orange-500 bg-orange-50 text-orange-700"
                                                            : "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
                                                    }`}
                                                >
                                                    {m === "cash"
                                                        ? "💵 Tunai"
                                                        : "🏦 Transfer"}
                                                </button>
                                            ),
                                        )}
                                    </div>
                                </div>
                            </>
                        )}
                    </>
                )}
            </div>

            {/* Footer — Tombol Bayar */}
            {selectedCustomer && selectedCustomer.orders.length > 0 && (
                <div className="shrink-0 p-4 border-t border-gray-200 bg-white">
                    <button
                        onClick={handlePay}
                        disabled={
                            paidAmount <= 0 || paidAmount > totalDebt || loading
                        }
                        className="w-full py-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 text-base shadow-md hover:shadow-lg active:scale-[0.98]"
                    >
                        {loading ? (
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
                                Bayar{" "}
                                {paidAmount > 0
                                    ? formatRupiah(paidAmount)
                                    : "Kasbon"}
                            </>
                        )}
                    </button>
                </div>
            )}
        </div>
    );
}
