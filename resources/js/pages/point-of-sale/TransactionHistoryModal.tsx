import React, { useEffect, useState } from "react";
import axios from "axios";
import { useLiveQuery } from "dexie-react-hooks";
import { db } from "../../lib/db";
import type { TransactionDetails } from "./ReceiptPrint";

interface TransactionHistoryModalProps {
    isOpen: boolean;
    onClose: () => void;
    onReprint: (transaction: TransactionDetails) => void;
}

export default function TransactionHistoryModal({ isOpen, onClose, onReprint }: TransactionHistoryModalProps) {
    const [transactions, setTransactions] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    const pendingOrdersCount = useLiveQuery(
        () => db.sync_queue.where('status').equals('pending').count(),
        [],
        0
    );

    useEffect(() => {
        if (isOpen) {
            setLoading(true);
            axios.get("/pos/transactions")
                .then((res) => {
                    let data = res.data;
                    if (typeof data === 'string') {
                        // Jika terlempar halaman login (HTML string)
                        data = [];
                    } else if (data && typeof data === 'object' && !Array.isArray(data)) {
                        // Jika response json menjadi object keys e.g. {"0": {...}}
                        data = Object.values(data);
                    } else if (!Array.isArray(data)) {
                        data = [];
                    }
                    setTransactions(data);
                })
                .catch((err) => {
                    console.error("Gagal mengambil riwayat transaksi:", err);
                })
                .finally(() => {
                    setLoading(false);
                });
        }
    }, [isOpen]);

    if (!isOpen) return null;

    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    return (
        <div className="fixed inset-0 z-[60] bg-[#001529]/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                {/* Header */}
                <div className="p-6 border-b border-[#f0f0f0] flex items-center justify-between bg-[#f5f5f5]">
                    <div>
                        <h2 className="text-xl font-bold text-[rgba(0,0,0,0.88)]">Riwayat Transaksi</h2>
                        <p className="text-sm text-[rgba(0,0,0,0.45)]">Menampilkan transaksi yang sudah tersinkronisasi di shift ini.</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="p-2 text-white/65 hover:text-[rgba(0,0,0,0.65)] hover:bg-slate-200 rounded-lg transition-colors"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {/* Banner Sinkronisasi */}
                {pendingOrdersCount > 0 && (
                    <div className="bg-[#e6f4ff] border-b border-amber-100 px-6 py-3 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <svg className="w-5 h-5 text-[#1677ff] animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p className="text-sm font-medium text-[#1677ff]">
                                Sedang mensinkronisasi {pendingOrdersCount} transaksi terbaru ke server... (Mohon tunggu sebentar)
                            </p>
                        </div>
                    </div>
                )}

                {/* Content */}
                <div className="flex-1 overflow-auto p-6 min-h-[300px]">
                    {loading ? (
                        <div className="flex justify-center items-center py-10">
                            <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                        </div>
                    ) : transactions.length === 0 ? (
                        <div className="text-center py-10">
                            <p className="text-[rgba(0,0,0,0.45)]">Belum ada transaksi di shift ini yang tersinkronisasi ke server.</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-[#f5f5f5] text-[rgba(0,0,0,0.65)] uppercase text-[11px] font-bold tracking-widest border-b border-[#f0f0f0]">
                                    <tr>
                                        <th className="px-6 py-4">Waktu</th>
                                        <th className="px-6 py-4">Invoice</th>
                                        <th className="px-6 py-4">Pelanggan</th>
                                        <th className="px-6 py-4 text-right">Total</th>
                                        <th className="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 border-b border-[#f0f0f0]">
                                    {transactions.map((trx) => (
                                        <tr key={trx.id} className="hover:bg-[#f5f5f5]/80 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap text-[rgba(0,0,0,0.65)] font-medium">
                                                {trx.date}
                                            </td>
                                            <td className="px-6 py-4 font-bold text-[#1677ff]">
                                                {trx.invoice_number}
                                            </td>
                                            <td className="px-6 py-4 text-[rgba(0,0,0,0.88)]">
                                                {trx.customerName}
                                            </td>
                                            <td className="px-6 py-4 text-right font-bold text-slate-900">
                                                {formatRupiah(trx.totalPrice)}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <button
                                                    onClick={() => onReprint(trx)}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#e6f4ff] text-[#0958d9] hover:bg-[#e6f4ff] hover:text-indigo-800 rounded-lg text-xs font-bold transition-colors"
                                                >
                                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak Ulang
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
